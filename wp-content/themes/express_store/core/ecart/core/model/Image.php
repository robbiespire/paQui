<?php
/**
 * ImageProcessor class
 *
 * 
 * @version 1.0
 * @package Ecart
 **/

class ImageProcessor {
	var $src;
	var $processed;
	var $width = false;
	var $height = false;
	var $axis = 'y';
	var $aspect = 1;
	var $dx = 0;
	var $dy = 0;
	var $alpha = false;

	function ImageProcessor ($data,$width,$height) {
		$this->src = new StdClass();
		$this->src->width = $width;
		$this->src->height = $height;
		$this->src->aspect = $width/$height;

		if ($data) $this->src->image = imagecreatefromstring($data);
		else $this->src->image = false;
	}

	function canvas ($width,$height,$alpha=false) {
		$this->processed = ImageCreateTrueColor($width,$height);
		if ($alpha) {
			ImageAlphaBlending($this->processed, false);
			$transparent = ImageColorAllocateAlpha($this->processed, 0, 0, 0, 127);
			ImageFill($this->processed, 0, 0, $transparent);
			ImageSaveAlpha($this->processed, true);
			$this->alpha = true;
		}
	}

	function scale ($width,$height,$fit='all',$alpha=false,$fill=false,$dx=false,$dy=false,$cropscale=false) {
		$this->aspect = $width/$height;

		// Allocate a new true color image
		$this->canvas($width,$height,$alpha);

		// Determine the dimensions to use for resizing
		$this->dimensions($width,$height,$fit,$dx,$dy,$cropscale);

		// Fill image with matte color
		if ($fit == "matte") {
			$rgb = false;
			if (is_int($fill)) $rgb = $this->hexrgb($fill);

			// Default to white
			if (!is_array($rgb)) $rgb = array('red'=>255,'green'=>255,'blue'=>255);

			// Allocate the color in the image palette
			$matte = ImageColorAllocate($this->processed, $rgb['red'], $rgb['green'], $rgb['blue']);

			// Fill the canvas
			ImageFill($this->processed,0,0,$matte);
		}

		if (!$this->src->image) {
			// Determine the mock dimensions from the resample operation
			if ($fit == "crop") {
				$this->width = min($width,$this->width);
				$this->height = min($height,$this->height);
			} elseif ($fill !== false) {
				$this->width = $width;
				$this->height = $height;
			}
			return;
		}
		// Resample the image
		ImageCopyResampled(
			$this->processed,$this->src->image,
			$this->dx, $this->dy,					// dest_x, dest_y
			0, 0,									// src_x, src_y
			$this->width, $this->height, 			// dest_width, dest_height
			$this->src->width, $this->src->height	// src_width, src_height
		);
		$this->width = imagesx($this->processed);
		$this->height = imagesy($this->processed);
		if (function_exists('apply_filters')) return apply_filters('ecart_image_scale',$this);
	}

	function dimensions ($width,$height,$fit='all',$dx=false,$dy=false,$cropscale=false) {
		if ($this->src->width <= $width && $this->src->height <= $height) {
			$this->width = $this->src->width;
			$this->height = $this->src->height;
			if ($fit == "matte") $this->center($width,$height,$dx,$dy);
			return false;
		}

		if ($fit == "crop") {
			if ($this->src->aspect/$this->aspect <= 1)
				$this->axis = 'x'; // Scale & crop
		} elseif ($this->src->aspect/$this->aspect >= 1)
			$this->axis = 'x'; // Scale & fit (with or without matte)

		$this->resized($width,$height,$this->axis);

		if ($fit == "crop") { // Center cropped image on the canvas
			if ($cropscale !== false) {
				$this->width = $this->src->width * $cropscale;
				$this->height = $this->src->height * $cropscale;
			} elseif ($this->src->width <= $width || $this->src->height <= $height) {
				$this->width = $this->src->width;
				$this->height = $this->src->height;
			}
		}

		$this->center($width,$height,$dx,$dy);

		return true;
	}

	private function center ($width,$height,$dx=false,$dy=false) {
		$this->dx = ($dx !== false)?$dx:($this->width - $width)*-0.5;
		$this->dy = ($dy !== false)?$dy:($this->height - $height)*-0.5;
	}

	private function resized ($width,$height,$axis='y') {
		if ($axis == "x") list($this->width,$this->height) = array($width,$this->ratioHeight($width));
		else list($this->width,$this->height) = array($this->ratioWidth($height),$height);
	}

	private function ratioWidth ($height) {
		$s_height = $height/$this->src->height;
		return ceil($this->src->width * $s_height);
	}

	private function ratioHeight ($width) {
		$s_width = $width/$this->src->width;
		return ceil($this->src->height * $s_width);
	}

	/**
	 * Return the processed image
	 *	 
	 * @since 1.0
	 *
	 * @return string Binary string of the image data
	 **/
	function imagefile ($quality=80) {
		if (!isset($this->processed)) $image =& $this->src->image;
		else $image = &$this->processed;

		imageinterlace($image, true);						// For progressive loading
		ob_start();  										// Start capturing output buffer stream
		if ($this->alpha) imagepng($image);					// Output the image to the stream
		else imagejpeg($image,NULL,$quality);
		$buffer = ob_get_contents(); 						// Get the bugger
		ob_end_clean(); 									// Clear the buffer
		return $buffer;										// Send it back
	}

	/**
	 * Performs an unsharp mask on the processed image
	 *
	 * Photoshop-like unsharp mask processing using image convolution.
	 * Original algorithm by Torstein Hansi
	 * 
	 * @since 1.0
	 * @version 2.1.1
	 *
	 * @return void Description...
	 **/
	function UnsharpMask ($amount=50, $radius=0.5, $threshold=3) {
		if (!isset($this->processed)) $image =& $this->src->image;
		else $image = &$this->processed;

	    // Attempt to calibrate the parameters to Photoshop
	    if ($amount > 500) $amount = 500;
	    $amount = $amount * 0.016;
	    if ($radius > 50) $radius = 50;
	    $radius = $radius * 2;
	    if ($threshold > 255) $threshold = 255;

	    $radius = abs(round($radius));
	    if ($radius == 0) return $image;
	    $w = imagesx($image); $h = imagesy($image);
	    $canvas = imagecreatetruecolor($w, $h);
	    $blur = imagecreatetruecolor($w, $h);

	    /**
	     * Gaussian blur matrix:
		 *	1    2    1
		 *	2    4    2
	     *	1    2    1
		 **/

	    if (function_exists('imageconvolution')) { // PHP >= 5.1
            $matrix = array(
				array( 1, 2, 1 ),
            	array( 2, 4, 2 ),
            	array( 1, 2, 1 )
        	);
	        imagecopy ($blur, $image, 0, 0, 0, 0, $w, $h);
	        imageconvolution($blur, $matrix, 16, 0);
	    } else {

			// Move copies of the image around one pixel at the time and merge them with weight
			// according to the matrix. The same matrix is simply repeated for higher radii.
	        for ($i = 0; $i < $radius; $i++)    {
	            imagecopy ($blur, $image, 0, 0, 1, 0, $w - 1, $h); // left
	            imagecopymerge ($blur, $image, 1, 0, 0, 0, $w, $h, 50); // right
	            imagecopymerge ($blur, $image, 0, 0, 0, 0, $w, $h, 50); // center
	            imagecopy ($canvas, $blur, 0, 0, 0, 0, $w, $h);

	            imagecopymerge ($blur, $canvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up
	            imagecopymerge ($blur, $canvas, 0, 1, 0, 0, $w, $h, 25); // down
	        }
	    }

	    if ($threshold > 0){
	        // Calculate the difference between the blurred pixels and the original
	        // and set the pixels
	        for ($x = 0; $x < $w-1; $x++) { // each row
	            for ($y = 0; $y < $h; $y++) { // each pixel

	                $rgbOrig = ImageColorAt($image, $x, $y);
	                $rOrig = (($rgbOrig >> 16) & 0xFF);
	                $gOrig = (($rgbOrig >> 8) & 0xFF);
	                $bOrig = ($rgbOrig & 0xFF);

	                $rgbBlur = ImageColorAt($blur, $x, $y);

	                $rBlur = (($rgbBlur >> 16) & 0xFF);
	                $gBlur = (($rgbBlur >> 8) & 0xFF);
	                $bBlur = ($rgbBlur & 0xFF);

	                // When the masked pixels differ less from the original
	                // than the threshold specifies, they are set to their original value.
	                $rNew = (abs($rOrig - $rBlur) >= $threshold)
	                    ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
	                    : $rOrig;
	                $gNew = (abs($gOrig - $gBlur) >= $threshold)
	                    ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
	                    : $gOrig;
	                $bNew = (abs($bOrig - $bBlur) >= $threshold)
	                    ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
	                    : $bOrig;



	                if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
	                        $pixCol = ImageColorAllocate($image, $rNew, $gNew, $bNew);
	                        ImageSetPixel($image, $x, $y, $pixCol);
	                    }
	            }
	        }
	    } else {
	        for ($x = 0; $x < $w; $x++) { // each row
	            for ($y = 0; $y < $h; $y++) { // each pixel
	                $rgbOrig = ImageColorAt($image, $x, $y);
	                $rOrig = (($rgbOrig >> 16) & 0xFF);
	                $gOrig = (($rgbOrig >> 8) & 0xFF);
	                $bOrig = ($rgbOrig & 0xFF);

	                $rgbBlur = ImageColorAt($blur, $x, $y);

	                $rBlur = (($rgbBlur >> 16) & 0xFF);
	                $gBlur = (($rgbBlur >> 8) & 0xFF);
	                $bBlur = ($rgbBlur & 0xFF);

	                $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
	                    if ($rNew > 255) $rNew=255;
	                    elseif($rNew < 0) $rNew=0;
	                $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
	                    if ($gNew > 255) $gNew=255;
	                    elseif($gNew < 0) $gNew=0;
	                $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
	                    if($bNew > 255) $bNew=255;
	                    elseif($bNew < 0) $bNew=0;
	                $rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew;
	                    ImageSetPixel($image, $x, $y, $rgbNew);
	            }
	        }
	    }
	    imagedestroy($canvas);
	    imagedestroy($blur);

	}

	/**
	 * Convert a decimal-encoded hexadecimal color to RGB color values
	 *
	 * Uses bit-shifty voodoo magic to pick the color spectrum apart.
	 *	 
	 * @since 1.1
	 *
	 * @param int $color Decimal color value
	 * @return array RGB color values
	 **/
	function hexrgb ($color) {
		return array(
			"red" => (0xFF & ($color >> 0x10)),
			"green" => (0xFF & ($color >> 0x8)),
			"blue" => (0xFF & $color)
		);
	}

} // END class ImageProcessor

?>