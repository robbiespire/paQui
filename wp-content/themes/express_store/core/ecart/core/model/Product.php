<?php
/**
 * Product.php
 *
 * Database management of catalog products
 *
 * @version 1.1
 * @package ecart
 * @since 1.0
 * @subpackage products
 **/

require_once("Asset.php");
require_once("Price.php");
require_once("Promotion.php");

class Product extends DatabaseObject {
	static $table = "product";
	var $prices = array();
	var $pricekey = array();
	var $priceid = array();
	var $categories = array();
	var $tags = array();
	var $images = array();
	var $specs = array();
	var $max = array();
	var $min = array();
	var $onsale = false;
	var $freeshipping = false;
	var $outofstock = false;
	var $stock = 0;
	var $options = 0;

	function __construct ($id=false,$key=false) {
		$this->init(self::$table);
		$this->load($id,$key);
	}

	/**
	 * Loads specified relational data associated with the product
	 *	 
	 * @since 1.1
	 *
	 * @param array $options List of data to load (prices, images, categories, tags, specs)
	 * @param array $products List of products to load data for
	 * @return void
	 **/
	function load_data ($options=false,&$products=false) {
		global $Ecart;
		$db =& DB::get();

		// Load object schemas on request

		$catalogtable = DatabaseObject::tablename(Catalog::$table);

		$Dataset = array();
		if (in_array('prices',$options)) {
			$this->prices = array();
			$promotable = DatabaseObject::tablename(Promotion::$table);
			$discounttable = DatabaseObject::tablename(Discount::$table);
			$assettable = DatabaseObject::tablename(ProductDownload::$table);

			$Dataset['prices'] = new Price();
			$Dataset['prices']->_datatypes['promos'] = "MAX(promo.status)";
			$Dataset['prices']->_datatypes['promotions'] = "group_concat(promo.name)";
			$Dataset['prices']->_datatypes['percentoff'] = "SUM(IF (promo.type='Percentage Off',promo.discount,0))";
			$Dataset['prices']->_datatypes['amountoff'] = "SUM(IF (promo.type='Amount Off',promo.discount,0))";
			$Dataset['prices']->_datatypes['freeshipping'] = "SUM(IF (promo.type='Free Shipping',1,0))";
			$Dataset['prices']->_datatypes['buyqty'] = "IF (promo.type='Buy X Get Y Free',promo.buyqty,0)";
			$Dataset['prices']->_datatypes['getqty'] = "IF (promo.type='Buy X Get Y Free',promo.getqty,0)";
			$Dataset['prices']->_datatypes['download'] = "download.id";
			$Dataset['prices']->_datatypes['filename'] = "download.name";
			$Dataset['prices']->_datatypes['filedata'] = "download.value";
		}

		if (in_array('images',$options)) {
			$this->images = array();
			$Dataset['images'] = new ProductImage();
			array_merge($Dataset['images']->_datatypes,$Dataset['images']->_xcols);
		}

		if (in_array('categories',$options)) {
			$this->categories = array();
			$Dataset['categories'] = new Category();
			unset($Dataset['categories']->_datatypes['priceranges']);
			unset($Dataset['categories']->_datatypes['specs']);
			unset($Dataset['categories']->_datatypes['options']);
			unset($Dataset['categories']->_datatypes['prices']);
		}

		if (in_array('specs',$options)) {
			$this->specs = array();
			$Dataset['specs'] = new Spec();
		}

		if (in_array('tags',$options)) {
			$this->tags = array();
			$Dataset['tags'] = new Tag();
		}

		// Determine the maximum columns to allocate
		$maxcols = 0;
		foreach ($Dataset as $set) {
			$cols = count($set->_datatypes);
			if ($cols > $maxcols) $maxcols = $cols;
		}

		// Prepare product list depending on single product or entire list
		$ids = array();
		if (isset($products) && is_array($products)) {
			foreach ($products as $product) $ids[] = $product->id;
		} else $ids[0] = $this->id;

		// Skip if there are no product ids
		if (empty($ids) || empty($ids[0])) return false;

		// Build the mega-query
		foreach ($Dataset as $rtype => $set) {

			// Allocate generic columns for record data
			$columns = array(); $i = 0;
			foreach ($set->_datatypes as $key => $datatype)
				$columns[] = ((strpos($datatype,'.')!==false)?"$datatype":"{$set->_table}.$key")." AS c".($i++);
			for ($i = $i; $i < $maxcols; $i++)
				$columns[] = "'' AS c$i";

			$cols = join(',',$columns);

			// Build object-specific selects and UNION them
			$where = "";
			if (isset($query)) $query .= " UNION ";
			else $query = "";
			switch($rtype) {
				case "prices":
					foreach ($ids as $id) $where .= ((!empty($where))?" OR ":"")."$set->_table.product=$id";
					$query .= "(SELECT '$set->_table' as dataset,$set->_table.product AS product,'$rtype' AS rtype,'' AS alphaorder,$set->_table.sortorder AS sortorder,$cols FROM $set->_table
								LEFT JOIN $assettable AS download ON $set->_table.id=download.parent AND download.context='price' AND download.type='download'
								LEFT JOIN $discounttable AS discount ON discount.product=$set->_table.product AND discount.price=$set->_table.id
								LEFT JOIN $promotable AS promo ON promo.id=discount.promo AND
								(promo.status='enabled' AND ((UNIX_TIMESTAMP(starts)=1 AND UNIX_TIMESTAMP(ends)=1)
								OR (".time()." > UNIX_TIMESTAMP(starts) AND ".time()." < UNIX_TIMESTAMP(ends))
								OR (UNIX_TIMESTAMP(starts)=1 AND ".time()." < UNIX_TIMESTAMP(ends))
								OR (".time()." > UNIX_TIMESTAMP(starts) AND UNIX_TIMESTAMP(ends)=1) ))
								WHERE $where GROUP BY $set->_table.id)";
					break;
				case "images":
					$ordering = $Ecart->Settings->get('product_image_order');
					if (empty($ordering)) $ordering = "ASC";
					$orderby = $Ecart->Settings->get('product_image_orderby');

					$sortorder = "0";
					if ($orderby == "sortorder" || $orderby == "created") {
						if ($orderby == "created") $orderby = "UNIX_TIMESTAMP(created)";
						switch ($ordering) {
							case "DESC": $sortorder = "$orderby*-1"; break;
							case "RAND": $sortorder = "RAND()"; break;
							default: $sortorder = "$orderby";
						}
					}

					$alphaorder = "''";
					if ($orderby == "name") {
						switch ($ordering) {
							case "DESC": $alphaorder = "$orderby"; break;
							case "RAND": $alphaorder = "RAND()"; break;
							default: $alphaorder = "$orderby";
						}
					}

					foreach ($ids as $id) $where .= ((!empty($where))?" OR ":"")."parent=$id";
					$where = "($where) AND context='product' AND type='image'";
					$query .= "(SELECT '$set->_table' as dataset,parent AS product,'$rtype' AS rtype,$alphaorder AS alphaorder,$sortorder AS sortorder,$cols FROM $set->_table WHERE $where ORDER BY $orderby)";
					break;
				case "specs":
					foreach ($ids as $id) $where .= ((!empty($where))?" OR ":"")."parent=$id AND context='product' AND type='spec'";
					$query .= "(SELECT '$set->_table' as dataset,parent AS product,'$rtype' AS rtype,'' AS alphaorder,sortorder AS sortorder,$cols FROM $set->_table WHERE $where)";
					break;
				case "categories":
					foreach ($ids as $id) $where .= ((!empty($where))?" OR ":"")."catalog.product=$id";
					$where = "($where) AND catalog.type='category'";
					$query .= "(SELECT '$set->_table' as dataset,catalog.product AS product,'$rtype' AS rtype,$set->_table.name AS alphaorder,0 AS sortorder,$cols FROM $catalogtable AS catalog LEFT JOIN $set->_table ON catalog.parent=$set->_table.id AND catalog.type='category' WHERE $where)";
					break;
				case "tags":
					foreach ($ids as $id) $where .= ((!empty($where))?" OR ":"")."catalog.product=$id";
					$where = "($where) AND catalog.type='tag'";
					$query .= "(SELECT '$set->_table' as dataset,catalog.product AS product,'$rtype' AS rtype,$set->_table.name AS alphaorder,0 AS sortorder,$cols FROM $catalogtable AS catalog LEFT JOIN $set->_table ON catalog.parent=$set->_table.id AND type='tag' WHERE $where)";
					break;
			}
		}

		// Add order by columns
		$query .= " ORDER BY sortorder";
		// die($query);

		// Execute the query
		$data = $db->query($query,AS_ARRAY);

		// Process the results into specific product object data in a product set

		foreach ($data as $row) {
			if (is_array($products) && isset($products[$row->product]))
				$target = $products[$row->product];
			else $target = $this;

			$record = new stdClass(); $i = 0; $name = "";
			foreach ($Dataset[$row->rtype]->_datatypes AS $key => $datatype) {
				$column = 'c'.$i++;
				$record->{$key} = '';
				if ($key == "name") $name = $row->{$column};
				if (!empty($row->{$column})) {
					if (preg_match("/^[sibNaO](?:\:.+?\{.*\}$|\:.+;$|;$)/",$row->{$column}))
						$row->{$column} = unserialize($row->{$column});
					$record->{$key} = $row->{$column};
				}
			}

			if ($row->rtype == "images") {
				$image = new ProductImage();
				$image->copydata($record,false,array());
				$image->expopulate();
				$name = $image->filename;
				$record = $image;

				// Reset the product's loaded images if the image was already
				// loaded from another context (like Category::load_products())
				if (isset($target->{$row->rtype}[0]) && $target->{$row->rtype}[0]->id == $image->id)
					$target->{$row->rtype} = array();
			}

			$target->{$row->rtype}[] = $record;
			if (!empty($name)) {
				if (isset($target->{$row->rtype.'key'}[$name]))
					$target->{$row->rtype.'key'}[$name] = array($target->{$row->rtype.'key'}[$name],$record);
				else $target->{$row->rtype.'key'}[$name] = $record;
			}
		}

		if (is_array($products)) {
			foreach ($products as $product) if (!empty($product->prices)) $product->pricing();
		} else {
			if (!empty($this->prices)) $this->pricing($options);
		}

	} // end load_data()

	/**
	 * Aggregates product pricing information
	 *	 
	 * @since 1.1
	 *
	 * @param array $options ecart() tag option list
	 * @return void
	 **/
	function pricing ($options = false) {

		// Variation range index/properties
		$varranges = array('price' => 'price','saleprice'=>'promoprice');

		$variations = ($this->variations == "on");
		$freeshipping = true;
		$this->inventory = false;
		foreach ($this->prices as $i => &$price) {
			$price->price = (float)$price->price;
			$price->saleprice = (float)$price->saleprice;
			$price->shipfee = (float)$price->shipfee;
			$price->promoprice = 0;

			// Build secondary lookup table using the price id as the key
			$this->priceid[$price->id] = $price;

			if (defined('WP_ADMIN') && !isset($options['taxes'])) $options['taxes'] = true;
			if (defined('WP_ADMIN') && value_is_true($options['taxes']) && $price->tax == "on") {
				$Settings =& EcartSettings();
				$base = $Settings->get('base_operations');
				if ($base['vat']) {
					$Taxes = new CartTax();
					$taxrate = $Taxes->rate($this);
					$price->price += $price->price*$taxrate;
					$price->saleprice += $price->saleprice*$taxrate;
				}
			}

			if ($price->type == "N/A" || $price->context == "addon" || ($i > 0 && !$variations)) continue;

			// Build third lookup table using the combined optionkey
			$this->pricekey[$price->optionkey] = $price;

			// Boolean flag for custom product sales
			$price->onsale = false;
			if ($price->sale == "on" && $price->type != "N/A")
				$this->onsale = $price->onsale = true;

			$price->stocked = false;
			if ($price->inventory == "on" && $price->type != "N/A") {
				$this->stock += $price->stock;
				$this->inventory = $price->stocked = true;
			}

			if ($price->freeshipping == '0' || $price->shipping == 'on')
				$freeshipping = false;

			if ($price->onsale) $price->promoprice = (float)$price->saleprice;
			else $price->promoprice = (float)$price->price;

			if ((isset($price->promos) && $price->promos == 'enabled')) {
				if ($price->percentoff > 0) {
					$price->promoprice = $price->promoprice - ($price->promoprice * ($price->percentoff/100));
					$this->onsale = $price->onsale = true;
				}
				if ($price->amountoff > 0) {
					$price->promoprice = $price->promoprice - $price->amountoff;
					$this->onsale = $price->onsale = true;;
				}
			}

			// Grab price and saleprice ranges (minimum - maximum)
			if ($price->type != "N/A") {
				if (!$price->price) $price->price = 0;
				if ($price->stocked) $varranges['stock'] = 'stock';

				foreach ($varranges as $name => $prop) {
					if (!isset($price->$prop)) continue;

					if (!isset($this->min[$name])) $this->min[$name] = $price->$prop;
					else $this->min[$name] = min($this->min[$name],$price->$prop);
					if ($this->min[$name] == $price->$prop) $this->min[$name.'_tax'] = ($price->tax == "on");


					if (!isset($this->max[$name])) $this->max[$name] = $price->$prop;
					else $this->max[$name] = max($this->max[$name],$price->$prop);
					if ($this->max[$name] == $price->$prop) $this->max[$name.'_tax'] = ($price->tax == "on");
				}
			}

			// Determine savings ranges
			if ($price->onsale && isset($this->min['price']) && isset($this->min['saleprice'])) {

				if (!isset($this->min['saved'])) {
					$this->min['saved'] = $price->price;
					$this->min['savings'] = 100;
					$this->max['saved'] = $this->max['savings'] = 0;
				}

				$this->min['saved'] = min($this->min['saved'],($price->price-$price->promoprice));
				$this->max['saved'] = max($this->max['saved'],($price->price-$price->promoprice));

				// Find lowest savings percentage
				if ($this->min['saved'] == ($price->price-$price->promoprice))
					$this->min['savings'] = (1 - $price->promoprice/($price->price == 0?1:$price->price))*100;
				if ($this->max['saved'] == ($price->price-$price->promoprice))
					$this->max['savings'] = (1 - $price->promoprice/($price->price == 0?1:$price->price))*100;
			}

			// Determine weight ranges
			if($price->weight && $price->weight > 0) {
				if(!isset($this->min['weight'])) $this->min['weight'] = $this->max['weight'] = $price->weight;
				$this->min['weight'] = min($this->min['weight'],$price->weight);
				$this->max['weight'] = max($this->max['weight'],$price->weight);
			}

		} // end foreach($price)

		if ($this->inventory && $this->stock <= 0) $this->outofstock = true;
		if ($freeshipping) $this->freeshipping = true;
	}

	/**
	 * Detect if the product is currently published
	 *	 
	 * @since 1.1
	 *
	 * @return boolean
	 **/
	function published () {
		return ($this->status == "publish" && time() >= $this->publish);
	}

	/**
	 * Returns the number of this product sold
	 *	 
	 * @since 1.1
	 *
	 * @return int
	 **/
	function sold () {
		$db =& DB::get();
		$purchased = DatabaseObject::tablename(Purchased::$table);
		$r = $db->query("SELECT count(id) AS sold FROM $purchased WHERE product=$this->id LIMIT 1");
		return $r->sold;
	}

	/**
	 * Merges specs with identical names into an array of values
	 *	 
	 * @since 1.1
	 *
	 * @return void
	 **/
	function merge_specs () {
		$merged = array();
		foreach ($this->specs as $key => $spec) {
			if (!isset($merged[$spec->name])) $merged[$spec->name] = $spec;
			else {
				if (!is_array($merged[$spec->name]->value))
					$merged[$spec->name]->value = array($merged[$spec->name]->value);
				$merged[$spec->name]->value[] = $spec->value;
			}
		}
		$this->specs = $merged;
	}

	/**
	 * Saves product category assignments to the catalog
	 *	 
	 * @since 1.1
	 *
	 * @param array $updates Updated list of category ids the product is assigned to
	 * @return void
	 **/
	function save_categories ($updates) {
		$db = DB::get();

		if (empty($updates)) $updates = array();

		$current = array();
		foreach ($this->categories as $category) $current[] = $category->id;

		$added = array_diff($updates,$current);
		$removed = array_diff($current,$updates);

		$table = DatabaseObject::tablename(Catalog::$table);

		if (!empty($added)) {
			foreach ($added as $id) {
				if (empty($id)) continue;
				$db->query("INSERT $table SET parent='$id',type='category',product='$this->id',created=now(),modified=now()");
			}
		}

		if (!empty($removed)) {
			foreach ($removed as $id) {
				if (empty($id)) continue;
				$db->query("DELETE LOW_PRIORITY FROM $table WHERE parent='$id' AND type='category' AND product='$this->id'");
			}

		}

	}

	function save_tags ($updates) {
		$db = DB::get();

		if (empty($updates)) $updates = array();
		$updates = stripslashes_deep($updates);

		$current = array();
		foreach ($this->tags as $tag) $current[] = $tag->name;

		$added = array_diff($updates,$current);
		$removed = array_diff($current,$updates);

		if (!empty($added)) {
			$catalog = DatabaseObject::tablename(Catalog::$table);
			$tagtable = DatabaseObject::tablename(Tag::$table);
			$where = "";
			foreach ($added as $tag) $where .= ($where == ""?"":" OR ")."name='".$db->escape($tag)."'";
			$results = $db->query("SELECT id,name FROM $tagtable WHERE $where",AS_ARRAY);
			$exists = array();
			foreach ($results as $tag) $exists[$tag->id] = $tag->name;

			foreach ($added as $tag) {
				if (empty($tag)) continue; // No empty tags
				$tagid = array_search($tag,$exists);

				if (!$tagid) {
					$Tag = new Tag();
					$Tag->name = $tag;
					$Tag->save();
					$tagid = $Tag->id;
				}

				if (!empty($tagid))
					$db->query("INSERT $catalog SET parent='$tagid',type='tag',product='$this->id',created=now(),modified=now()");

			}
		}

		if (!empty($removed)) {
			$catalog = DatabaseObject::tablename(Catalog::$table);
			foreach ($removed as $tag) {
				// Ensure loading tag records by case-sensitive name with BINARY casting
				$Tag = new Tag($tag,'BINARY name');
				if (!empty($Tag->id))
					$db->query("DELETE LOW_PRIORITY FROM $catalog WHERE parent='$Tag->id' AND type='tag' AND product='$this->id'");
			}
		}

	}

	/**
	 * optionkey
	 * There is no Zul only XOR! */
	function optionkey ($ids=array(),$deprecated=false) {
		if ($deprecated) $factor = 101;
		else $factor = 7001;
		if (empty($ids)) return 0;
		$key = 0;
		foreach ($ids as $set => $id)
			$key = $key ^ ($id*$factor);
		return $key;
	}

	/**
	 * save_imageorder()
	 * Updates the sortorder of image assets (source, featured and thumbnails)
	 * based on the provided array of image ids */
	function save_imageorder ($ordering) {
		$db = DB::get();
		$table = DatabaseObject::tablename(ProductImage::$table);
		foreach ($ordering as $i => $id)
			$db->query("UPDATE LOW_PRIORITY $table SET sortorder='$i' WHERE (id='$id' AND parent='$this->id' AND context='product' AND type='image')");
		return true;
	}

	/**
	 * link_images()
	 * Updates the product id of the images to link to the product
	 * when the product being saved is new (has no previous id assigned) */
	function link_images ($images) {
		if (empty($images)) return false;
		$db = DB::get();
		$table = DatabaseObject::tablename(ProductImage::$table);
		$set = "id=".join(' OR id=',$images);
		$query = "UPDATE $table SET parent='$this->id',context='product' WHERE ".$set;
		$db->query($query);
		return true;
	}

	/**
	 * update_images()
	 * Updates the image details for all cached images */
	function update_images ($images) {
		if (!is_array($images)) return false;

		foreach ($images as $img) {
			$Image = new ProductImage($img['id']);
			$Image->title = $img['title'];
			$Image->alt = $img['alt'];

			if (!empty($img['cropping'])) {
				require_once(ECART_PATH."/core/model/Image.php");

				foreach ($img['cropping'] as $id => $cropping) {
					if (empty($cropping)) continue;
					$Cropped = new ProductImage($id);

					list($Cropped->settings['dx'],
						$Cropped->settings['dy'],
						$Cropped->settings['cropscale']) = explode(',', $cropping);
					extract($Cropped->settings);

					$Resized = new ImageProcessor($Image->retrieve(),$Image->width,$Image->height);
					$scaled = $Image->scaled($width,$height,$scale);
					$scale = $Cropped->_scaling[$scale];
					$quality = ($quality === false)?$Cropped->_quality:$quality;

					$Resized->scale($scaled['width'],$scaled['height'],$scale,$alpha,$fill,(int)$dx,(int)$dy,(float)$cropscale);

					// Post sharpen
					if ($sharpen !== false) $Resized->UnsharpMask($sharpen);
					$Cropped->data = $Resized->imagefile($quality);
					if (empty($Cropped->data)) return false;

					$Cropped->size = strlen($Cropped->data);
					if ($Cropped->store( $Cropped->data ) === false)
						return false;

					$Cropped->save();

				}
			}

			$Image->save();
		}

		return true;
	}


	/**
	 * delete_images()
	 * Delete provided array of image ids, removing the source image and
	 * all related images (small and thumbnails) */
	function delete_images ($images) {
		$db = &DB::get();
		$imagetable = DatabaseObject::tablename(ProductImage::$table);
		$imagesets = "";
		foreach ($images as $image) {
			$imagesets .= (!empty($imagesets)?" OR ":"");
			$imagesets .= "((context='product' AND parent='$this->id' AND id='$image') OR (context='image' AND parent='$image'))";
		}
		if (!empty($imagesets))
			$db->query("DELETE FROM $imagetable WHERE type='image' AND ($imagesets)");
		return true;
	}

	/**
	 * Deletes the record associated with this object */
	function delete () {
		$db = DB::get();
		$id = $this->{$this->_key};
		if (empty($id)) return false;

		// Delete from categories
		$table = DatabaseObject::tablename(Catalog::$table);
		$db->query("DELETE LOW_PRIORITY FROM $table WHERE product='$id'");

		// Delete prices
		$table = DatabaseObject::tablename(Price::$table);
		$db->query("DELETE LOW_PRIORITY FROM $table WHERE product='$id'");

		// Delete images/files
		$table = DatabaseObject::tablename(ProductImage::$table);

		// Delete images
		$images = array();
		$src = $db->query("SELECT id FROM $table WHERE parent='$id' AND context='product' AND type='image'",AS_ARRAY);
		foreach ($src as $img) $images[] = $img->id;
		$this->delete_images($images);

		// Delete product meta (specs, images, downloads)
		$table = DatabaseObject::tablename(MetaObject::$table);
		$db->query("DELETE LOW_PRIORITY FROM $table WHERE parent='$id' AND context='product'");

		// Delete record
		$db->query("DELETE FROM $this->_table WHERE $this->_key='$id'");

	}

	function duplicate () {
		$db =& DB::get();

		$this->load_data(array('prices','specs','categories','tags','images','taxes'=>'false'));
		$this->id = '';
		$this->name = $this->name.' '.__('copy','Ecart');
		$this->slug = sanitize_title_with_dashes($this->name);

		// Check for an existing product slug
		$existing = $db->query("SELECT slug FROM $this->_table WHERE slug='$this->slug' LIMIT 1");
		if ($existing) {
			$suffix = 2;
			while($existing) {
				$altslug = substr($this->slug, 0, 200-(strlen($suffix)+1)). "-$suffix";
				$existing = $db->query("SELECT slug FROM $this->_table WHERE slug='$altslug' LIMIT 1");
				$suffix++;
			}
			$this->slug = $altslug;
		}
		$this->created = '';
		$this->modified = '';

		$this->save();

		// Copy prices
		foreach ($this->prices as $price) {
			$Price = new Price();
			$Price->updates($price,array('id','product','created','modified'));
			$Price->product = $this->id;
			$Price->save();
		}

		// Copy sepcs
		foreach ($this->specs as $spec) {
			$Spec = new Spec();
			$Spec->updates($spec,array('id','parent','created','modified'));
			$Spec->parent = $this->id;
			$Spec->save();
		}

		// Copy categories
		$categories = array();
		foreach ($this->categories as $category) $categories[] = $category->id;
		$this->categories = array();
		$this->save_categories($categories);

		// Copy tags
		$taglist = array();
		foreach ($this->tags as $tag) $taglist[] = $tag->name;
		$this->tags = array();
		$this->save_tags($taglist);

		// Copy product images
		foreach ($this->images as $ProductImage) {
			$Image = new ProductImage();
			$Image->updates($ProductImage,array('id','parent','created','modified'));
			$Image->parent = $this->id;
			$Image->save();
		}

	}

	function taxrule ($rule) {
		switch ($rule['p']) {
			case "product-name": return ($rule['v'] == $this->name); break;
			case "product-tags":
				if (!isset($this->tagskey)) return false;
				else return (in_array($rule['v'],array_keys($this->tagskey)));
				break;
			case "product-category":
				if (!isset($this->categorieskey)) return false;
				else return (in_array($rule['v'],array_keys($this->categorieskey))); break;
		}
		return false;
	}

	function tag ($property,$options=array()) {
		global $Ecart;

		$select_attrs = array('title','required','class','disabled','required','size','tabindex','accesskey');
		$submit_attrs = array('title','class','value','disabled','tabindex','accesskey');

		switch ($property) {
			case "link":
			case "url":
				return ecarturl(ECART_PRETTYURLS?$this->slug:array('ecart_pid'=>$this->id));
				break;
			case "found":
				if (empty($this->id)) return false;
				$load = array('prices','images','specs','tags','categories');
				if (isset($options['load'])) $load = explode(",",$options['load']);
				$this->load_data($load);
				return true;
				break;
			case "relevance": return (string)$this->score; break;
			case "id": return $this->id; break;
			case "name": return apply_filters('ecart_product_name',$this->name); break;
			case "slug": return $this->slug; break;
			case "summary": return apply_filters('ecart_product_summary',$this->summary); break;
			case "description":
				return apply_filters('ecart_product_description',$this->description);
			case "isfeatured":
			case "is-featured":
				return ($this->featured == "on"); break;
			case "price":
			case "saleprice":
				if (empty($this->prices)) $this->load_data(array('prices'));
				$defaults = array(
					'taxes' => null,
					'starting' => ''
				);
				$options = array_merge($defaults,$options);
				extract($options);

				if (!is_null($taxes)) $taxes = value_is_true($taxes);

				$min = $this->min[$property];
				$mintax = $this->min[$property.'_tax'];

				$max = $this->max[$property];
				$maxtax = $this->max[$property.'_tax'];

				$taxrate = ecart_taxrate($taxes,$this->prices[0]->tax,$this);

				if ("saleprice" == $property) $pricetag = $this->prices[0]->promoprice;
				else $pricetag = $this->prices[0]->price;

				if (count($this->options) > 0) {
					$taxrate = ecart_taxrate($taxes,true,$this);
					$mintax = $mintax?$min*$taxrate:0;
					$maxtax = $maxtax?$max*$taxrate:0;

					if ($min == $max) return money($min+$mintax);
					else {
						if (!empty($starting)) return "$starting ".money($min+$mintax);
						return money($min+$mintax)." &mdash; ".money($max+$maxtax);
					}
				} else return money($pricetag+($pricetag*$taxrate));

				break;
			case "taxrate":
				return ecart_taxrate(null,true,$this);
				break;
			case "weight":
				if(empty($this->prices)) $this->load_data(array('prices'));
				$defaults = array(
					'unit' => $Ecart->Settings->get('weight_unit'),
					'min' => $this->min['weight'],
					'max' => $this->max['weight'],
					'units' => true,
					'convert' => false
				);
				$options = array_merge($defaults,$options);
				extract($options);

				if(!isset($this->min['weight'])) return false;

				if ($convert !== false) {
					$min = convert_unit($min,$convert);
					$max = convert_unit($max,$convert);
					if (is_null($units)) $units = true;
					$unit = $convert;
				}

				$range = false;
				if ($min != $max) {
					$range = array($min,$max);
					sort($range);
				}

				$string = ($min == $max)?round($min,3):round($range[0],3)." - ".round($range[1],3);
				$string .= value_is_true($units) ? " $unit" : "";
				return $string;
				break;
			case "onsale":
				if (empty($this->prices)) $this->load_data(array('prices'));
				if (empty($this->prices)) return false;
				return $this->onsale;
				break;
			case "has-savings": return ($this->onsale && $this->min['saved'] > 0); break;
			case "savings":
				if (empty($this->prices)) $this->load_data(array('prices'));
				if (!isset($options['taxes'])) $options['taxes'] = null;

				$taxrate = ecart_taxrate($options['taxes']);
				$range = false;

				if (!isset($options['show'])) $options['show'] = '';
				if ($options['show'] == "%" || $options['show'] == "percent") {
					if ($this->options > 1) {
						if (round($this->min['savings']) != round($this->max['savings'])) {
							$range = array($this->min['savings'],$this->max['savings']);
							sort($range);
						}
						if (!$range) return percentage($this->min['savings'],array('precision' => 0)); // No price range
						else return percentage($range[0],array('precision' => 0))." &mdash; ".percentage($range[1],array('precision' => 0));
					} else return percentage($this->max['savings'],array('precision' => 0));
				} else {
					if ($this->options > 1) {
						if (round($this->min['saved']) != round($this->max['saved'])) {
							$range = array($this->min['saved'],$this->max['saved']);
							sort($range);
						}
						if (!$range) return money($this->min['saved']+($this->min['saved']*$taxrate)); // No price range
						else return money($range[0]+($range[0]*$taxrate))." &mdash; ".money($range[1]+($range[1]*$taxrate));
					} else return money($this->max['saved']+($this->max['saved']*$taxrate));
				}
				break;
			case "freeshipping":
				if (empty($this->prices)) $this->load_data(array('prices'));
				return $this->freeshipping;
			case "hasimages":
			case "has-images":
				if (empty($this->images)) $this->load_data(array('images'));
				return (!empty($this->images));
				break;
			case "images":
				if (!$this->images) return false;
				if (!isset($this->_images_loop)) {
					reset($this->images);
					$this->_images_loop = true;
				} else next($this->images);

				if (current($this->images) !== false) return true;
				else {
					unset($this->_images_loop);
					return false;
				}
				break;
			case "coverimage":
				// Force select the first loaded image
				unset($options['id']);
				$options['index'] = 0;
			case "thumbnail": // deprecated
			case "image":
				if (empty($this->images)) $this->load_data(array('images'));
				if (!(count($this->images) > 0)) return "";

				// Compatibility defaults
				$_size = 96;
				$_width = $Ecart->Settings->get('gallery_thumbnail_width');
				$_height = $Ecart->Settings->get('gallery_thumbnail_height');
				if (!$_width) $_width = $_size;
				if (!$_height) $_height = $_size;

				$defaults = array(
					'img' => false,
					'id' => false,
					'index' => false,
					'class' => '',
					'width' => false,
					'height' => false,
					'size' => false,
					'fit' => false,
					'sharpen' => false,
					'quality' => false,
					'bg' => false,
					'alt' => '',
					'title' => '',
					'zoom' => '',
					'zoomfx' => 'ecart-zoom',
					'property' => false
				);
				$options = array_merge($defaults,$options);
				extract($options);

				// Select image by database id
				if ($id !== false) {
					for ($i = 0; $i < count($this->images); $i++) {
						if ($img->id == $id) {
							$img = $this->images[$i]; break;
						}
					}
					if (!$img) return "";
				}

				// Select image by index position in the list
				if ($index !== false && isset($this->images[$index]))
					$img = $this->images[$index];

				// Use the current image pointer by default
				if (!$img) $img = current($this->images);

				if ($size !== false) $width = $height = $size;
				if (!$width) $width = $_width;
				if (!$height) $height = $_height;

				$scale = $fit?array_search($fit,$img->_scaling):false;
				$sharpen = $sharpen?min($sharpen,$img->_sharpen):false;
				$quality = $quality?min($quality,$img->_quality):false;
				$fill = $bg?hexdec(ltrim($bg,'#')):false;

				list($width_a,$height_a) = array_values($img->scaled($width,$height,$scale));
				if ($size == "original") {
					$width_a = $img->width;
					$height_a = $img->height;
				}
				if ($width_a === false) $width_a = $width;
				if ($height_a === false) $height_a = $height;

				$alt = esc_attr(empty($alt)?(empty($img->alt)?$img->name:$img->alt):$alt);
				$title = empty($title)?$img->title:$title;
				$titleattr = empty($title)?'':' title="'.esc_attr($title).'"';
				$classes = empty($class)?'':' class="'.esc_attr($class).'"';

				$src = ecarturl($img->id,'images');
				if (ECART_PERMALINKS) $src = trailingslashit($src).$img->filename;

				if ($size != "original")
					$src = add_query_string($img->resizing($width,$height,$scale,$sharpen,$quality,$fill),$src);

				switch (strtolower($property)) {
					case "id": return $img->id; break;
					case "url":
					case "src": return $src; break;
					case "title": return $title; break;
					case "alt": return $alt; break;
					case "width": return $width_a; break;
					case "height": return $height_a; break;
					case "class": return $class; break;
				}

				$imgtag = '<img src="'.$src.'"'.$titleattr.' alt="'.$alt.'" width="'.$width_a.'" height="'.$height_a.'" '.$classes.' />';

				if (value_is_true($zoom))
					return '<a href="'.ecarturl($img->id,'images').'/'.$img->filename.'" class="'.$zoomfx.'" rel="product-'.$this->id.'">'.$imgtag.'</a>';

				return $imgtag;
				break;
			case "gallery":
				if (empty($this->images)) $this->load_data(array('images'));
				if (empty($this->images)) return false;
				$styles = '';
				$_size = 240;
				$_width = $Ecart->Settings->get('gallery_small_width');
				$_height = $Ecart->Settings->get('gallery_small_height');

				if (!$_width) $_width = $_size;
				if (!$_height) $_height = $_size;

				$defaults = array(

					// Layout settings
					'margins' => 20,
					'rowthumbs' => false,
					// 'thumbpos' => 'after',

					// Preview image settings
					'p.size' => false,
					'p.width' => false,
					'p.height' => false,
					'p.fit' => false,
					'p.sharpen' => false,
					'p.quality' => false,
					'p.bg' => false,
					'p.link' => true,
					'rel' => '',

					// Thumbnail image settings
					'thumbsize' => false,
					'thumbwidth' => false,
					'thumbheight' => false,
					'thumbfit' => false,
					'thumbsharpen' => false,
					'thumbquality' => false,
					'thumbbg' => false,

					// Effects settings
					'zoomfx' => 'ecart-zoom',
					'preview' => 'click',
					'colorbox' => '{}'


				);
				$optionset = array_merge($defaults,$options);

				// Translate dot names
				$options = array();
				$keys = array_keys($optionset);
				foreach ($keys as $key)
					$options[str_replace('.','_',$key)] = $optionset[$key];
				extract($options);

				if ($p_size > 0)
					$_width = $_height = $p_size;

				$width = $p_width > 0?$p_width:$_width;
				$height = $p_height > 0?$p_height:$_height;

				$preview_width = $width;

				$previews = '<ul class="previews">';
				$firstPreview = true;

				// Find the max dimensions to use for the preview spacing image
				$maxwidth = $maxheight = 0;
				foreach ($this->images as $img) {
					$scale = $p_fit?false:array_search($p_fit,$img->_scaling);
					$scaled = $img->scaled($width,$height,$scale);
					$maxwidth = max($maxwidth,$scaled['width']);
					$maxheight = max($maxheight,$scaled['height']);
				}

				if ($maxwidth == 0) $maxwidth = $width;
				if ($maxheight == 0) $maxheight = $height;

				$p_link = value_is_true($p_link);

				foreach ($this->images as $img) {

					$scale = $p_fit?array_search($p_fit,$img->_scaling):false;
					$sharpen = $p_sharpen?min($p_sharpen,$img->_sharpen):false;
					$quality = $p_quality?min($p_quality,$img->_quality):false;
					$fill = $p_bg?hexdec(ltrim($p_bg,'#')):false;
					$scaled = $img->scaled($width,$height,$scale);

					if ($firstPreview) { // Adds "filler" image to reserve the dimensions in the DOM
						$href = ecarturl(ECART_PERMALINKS?trailingslashit('000'):'000','images');
						$previews .= '<li id="preview-fill"'.(($firstPreview)?' class="fill"':'').'>';
						$previews .= '<img src="'.add_query_string("$maxwidth,$maxheight",$href).'" alt=" " width="'.$maxwidth.'" height="'.$maxheight.'" />';
						$previews .= '</li>';
					}
					$title = !empty($img->title)?' title="'.esc_attr($img->title).'"':'';
					$alt = esc_attr(!empty($img->alt)?$img->alt:$img->filename);

					$previews .= '<li id="preview-'.$img->id.'"'.(($firstPreview)?' class="active"':'').'>';

					$href = ecarturl(ECART_PERMALINKS?trailingslashit($img->id).$img->filename:$img->id,'images');
					if ($p_link) $previews .= '<a href="'.$href.'" class="gallery product_'.$this->id.' '.$options['zoomfx'].'"'.(!empty($rel)?' rel="'.$rel.'"':'').'>';
					// else $previews .= '<a name="preview-'.$img->id.'">'; // If links are turned off, leave the <a> so we don't break layout
					$previews .= '<img src="'.add_query_string($img->resizing($width,$height,$scale,$sharpen,$quality,$fill),ecarturl($img->id,'images')).'"'.$title.' alt="'.$alt.'" width="'.$scaled['width'].'" height="'.$scaled['height'].'" />';
					if ($p_link) $previews .= '</a>';
					$previews .= '</li>';
					$firstPreview = false;
				}
				$previews .= '</ul>';

				$thumbs = "";
				$twidth = $preview_width+$margins;

				if (count($this->images) > 1) {
					$default_size = 64;
					$_thumbwidth = $Ecart->Settings->get('gallery_thumbnail_width');
					$_thumbheight = $Ecart->Settings->get('gallery_thumbnail_height');
					if (!$_thumbwidth) $_thumbwidth = $default_size;
					if (!$_thumbheight) $_thumbheight = $default_size;

					if ($thumbsize > 0) $thumbwidth = $thumbheight = $thumbsize;

					$width = $thumbwidth > 0?$thumbwidth:$_thumbwidth;
					$height = $thumbheight > 0?$thumbheight:$_thumbheight;

					$firstThumb = true;
					$thumbs = '<ul class="thumbnails">';
					foreach ($this->images as $img) {
						$scale = $thumbfit?array_search($thumbfit,$img->_scaling):false;
						$sharpen = $thumbsharpen?min($thumbsharpen,$img->_sharpen):false;
						$quality = $thumbquality?min($thumbquality,$img->_quality):false;
						$fill = $thumbbg?hexdec(ltrim($thumbbg,'#')):false;
						$scaled = $img->scaled($width,$height,$scale);

						$title = !empty($img->title)?' title="'.esc_attr($img->title).'"':'';
						$alt = esc_attr(!empty($img->alt)?$img->alt:$img->name);

						$thumbs .= '<li id="thumbnail-'.$img->id.'" class="preview-'.$img->id.(($firstThumb)?' first':'').'">';
						$thumbs .= '<img src="'.add_query_string($img->resizing($width,$height,$scale,$sharpen,$quality,$fill),ecarturl($img->id,'images')).'"'.$title.' alt="'.$alt.'" width="'.$scaled['width'].'" height="'.$scaled['height'].'" />';
						$thumbs .= '</li>'."\n";
						$firstThumb = false;
					}
					$thumbs .= '</ul>';

				}
				if ($rowthumbs > 0) $twidth = ($width+$margins+2)*(int)$rowthumbs;

				$result = '<div id="gallery-'.$this->id.'" class="gallery">'.$previews.$thumbs.'</div>';
				$script = "\t".'EcartGallery("#gallery-'.$this->id.'","'.$preview.'"'.($twidth?",$twidth":"").');';
				add_storefrontjs($script);

				return $result;

				break;
			case "has-categories":
				if (empty($this->categories)) $this->load_data(array('categories'));
				if (count($this->categories) > 0) return true; else return false; break;
			case "categories":
				if (!isset($this->_categories_loop)) {
					reset($this->categories);
					$this->_categories_loop = true;
				} else next($this->categories);

				if (current($this->categories) !== false) return true;
				else {
					unset($this->_categories_loop);
					return false;
				}
				break;
			case "in-category":
				if (empty($this->categories)) $this->load_data(array('categories'));
				if (isset($options['id'])) $field = "id";
				if (isset($options['name'])) $field = "name";
				if (isset($options['slug'])) $field = "slug";
				foreach ($this->categories as $category)
					if ($category->{$field} == $options[$field]) return true;
				return false;
			case "category":
				$category = current($this->categories);
				if (isset($options['show'])) {
					if ($options['show'] == "id") return $category->id;
					if ($options['show'] == "slug") return $category->slug;
				}
				return $category->name;
				break;
			case "hastags":
			case "has-tags":
				if (empty($this->tags)) $this->load_data(array('tags'));
				if (count($this->tags) > 0) return true; else return false; break;
			case "tags":
				if (!isset($this->_tags_loop)) {
					reset($this->tags);
					$this->_tags_loop = true;
				} else next($this->tags);

				if (current($this->tags) !== false) return true;
				else {
					unset($this->_tags_loop);
					return false;
				}
				break;
			case "tagged":
				if (empty($this->tags)) $this->load_data(array('tags'));
				if (isset($options['id'])) $field = "id";
				if (isset($options['name'])) $field = "name";
				foreach ($this->tags as $tag)
					if ($tag->{$field} == $options[$field]) return true;
				return false;
			case "tag":
				$tag = current($this->tags);
				if (isset($options['show'])) {
					if ($options['show'] == "id") return $tag->id;
				}
				return $tag->name;
				break;
			case "hasspecs":
			case "has-specs":
				if (empty($this->specs)) $this->load_data(array('specs'));
				if (count($this->specs) > 0) {
					$this->merge_specs();
					return true;
				} else return false; break;
			case "specs":
				if (!isset($this->_specs_loop)) {
					reset($this->specs);
					$this->_specs_loop = true;
				} else next($this->specs);

				if (current($this->specs) !== false) return true;
				else {
					unset($this->_specs_loop);
					return false;
				}
				break;
			case "spec":
				$string = "";
				$separator = ": ";
				$delimiter = ", ";
				if (isset($options['separator'])) $separator = $options['separator'];
				if (isset($options['delimiter'])) $separator = $options['delimiter'];

				$spec = current($this->specs);
				if (is_array($spec->value)) $spec->value = join($delimiter,$spec->value);

				if (isset($options['name'])
					&& !empty($options['name'])
					&& isset($this->specskey[$options['name']])) {
						$spec = $this->specskey[$options['name']];
						if (is_array($spec)) {
							if (isset($options['index'])) {
								foreach ($spec as $index => $entry)
									if ($index+1 == $options['index'])
										$content = $entry->value;
							} else {
								foreach ($spec as $entry) $contents[] = $entry->value;
								$content = join($delimiter,$contents);
							}
						} else $content = $spec->value;
					$string = apply_filters('ecart_product_spec',$content);
					return $string;
				}

				if (isset($options['name']) && isset($options['content']))
					$string = "{$spec->name}{$separator}".apply_filters('ecart_product_spec',$spec->value);
				elseif (isset($options['name'])) $string = $spec->name;
				elseif (isset($options['content'])) $string = apply_filters('ecart_product_spec',$spec->value);
				else $string = "{$spec->name}{$separator}".apply_filters('ecart_product_spec',$spec->value);
				return $string;
				break;
			case "has-variations":
				return ($this->variations == "on" && (!empty($this->options['v']) || !empty($this->options))); break;
			case "variations":

				$string = "";

				if (!isset($options['mode'])) {
					if (!isset($this->_prices_loop)) {
						reset($this->prices);
						$this->_prices_loop = true;
					} else next($this->prices);
					$price = current($this->prices);

					if ($price && ($price->type == 'N/A' || $price->context != 'variation'))
						next($this->prices);

					if (current($this->prices) !== false) return true;
					else {
						unset($this->_prices_loop);
						return false;
					}
					return true;
				}

				if ($this->outofstock) return false; // Completely out of stock, hide menus
				if (!isset($options['taxes'])) $options['taxes'] = null;

				$defaults = array(
					'defaults' => '',
					'disabled' => 'show',
					'pricetags' => 'show',
					'before_menu' => '',
					'after_menu' => '',
					'label' => 'on',
					'required' => __('You must select the options for this item before you can add it to your shopping cart.','Ecart')
					);
				$options = array_merge($defaults,$options);

				if ($options['mode'] == "single") {
					if (!empty($options['before_menu'])) $string .= $options['before_menu']."\n";
					if (value_is_true($options['label'])) $string .= '<label for="product-options'.$this->id.'">'. __('Options').': </label> '."\n";

					$string .= '<select name="products['.$this->id.'][price]" id="product-options'.$this->id.'">';
					if (!empty($options['defaults'])) $string .= '<option value="">'.$options['defaults'].'</option>'."\n";

					foreach ($this->prices as $pricetag) {
						if ($pricetag->context != "variation") continue;

						if (!isset($options['taxes']))
							$taxrate = ecart_taxrate(null,$pricetag->tax);
						else $taxrate = ecart_taxrate(value_is_true($options['taxes']),$pricetag->tax);
						$currently = ($pricetag->sale == "on")?$pricetag->promoprice:$pricetag->price;
						$disabled = ($pricetag->inventory == "on" && $pricetag->stock == 0)?' disabled="disabled"':'';

						$price = '  ('.money($currently).')';
						if ($pricetag->type != "N/A")
							$string .= '<option value="'.$pricetag->id.'"'.$disabled.'>'.$pricetag->label.$price.'</option>'."\n";
					}
					$string .= '</select>';
					if (!empty($options['after_menu'])) $string .= $options['after_menu']."\n";

				} else {
					if (!isset($this->options)) return;

					$menuoptions = $this->options;
					if (!empty($this->options['v'])) $menuoptions = $this->options['v'];

					$baseop = $Ecart->Settings->get('base_operations');
					$precision = $baseop['currency']['format']['precision'];

					if (!isset($options['taxes']))
						$taxrate = ecart_taxrate(null,true,$this);
					else $taxrate = ecart_taxrate(value_is_true($options['taxes']),true,$this);

					$pricekeys = array();
					foreach ($this->pricekey as $key => $pricing) {
						$filter = array('');
						$_ = new StdClass();
						if ($pricing->type != "Donation")
							$_->p = ((isset($pricing->onsale)
										&& $pricing->onsale == "on")?
											(float)$pricing->promoprice:
											(float)$pricing->price);
						$_->i = ($pricing->inventory == "on");
						$_->s = ($pricing->inventory == "on")?$pricing->stock:false;
						$_->tax = ($pricing->tax == "on");
						$_->t = $pricing->type;
						$pricekeys[$key] = $_;
					}

					ob_start();
?><?php if (!empty($options['defaults'])): ?>
	sjss.opdef = true;
<?php endif; ?>
<?php if (!empty($options['required'])): ?>
	sjss.opreq = "<?php echo $options['required']; ?>";
<?php endif; ?>
	pricetags[<?php echo $this->id; ?>] = <?php echo json_encode($pricekeys); ?>;
	new ProductOptionsMenus('select<?php if (!empty($Ecart->Category->slug)) echo ".category-".$Ecart->Category->slug; ?>.product<?php echo $this->id; ?>.options',{<?php if ($options['disabled'] == "hide") echo "disabled:false,"; ?><?php if ($options['pricetags'] == "hide") echo "pricetags:false,"; ?><?php if (!empty($taxrate)) echo "taxrate:$taxrate,"?>prices:pricetags[<?php echo $this->id; ?>]});
<?php
					$script = ob_get_contents();
					ob_end_clean();

					add_storefrontjs($script);

					foreach ($menuoptions as $id => $menu) {
						if (!empty($options['before_menu'])) $string .= $options['before_menu']."\n";
						if (value_is_true($options['label'])) $string .= '<label for="options-'.$menu['id'].'">'.$menu['name'].'</label> '."\n";
						$category_class = isset($Ecart->Category->slug)?'category-'.$Ecart->Category->slug:'';
						$string .= '<select name="products['.$this->id.'][options][]" class="'.$category_class.' product'.$this->id.' options" id="options-'.$menu['id'].'">';
						if (!empty($options['defaults'])) $string .= '<option value="">'.$options['defaults'].'</option>'."\n";
						foreach ($menu['options'] as $key => $option)
							$string .= '<option value="'.$option['id'].'">'.$option['name'].'</option>'."\n";

						$string .= '</select>';
					}
					if (!empty($options['after_menu'])) $string .= $options['after_menu']."\n";
				}

				return $string;
				break;
			case "variation":
				$variation = current($this->prices);

				if (!isset($options['taxes'])) $options['taxes'] = null;
				else $options['taxes'] = value_is_true($options['taxes']);
				$taxrate = ecart_taxrate($options['taxes'],$variation->tax,$this);

				$weightunit = (isset($options['units']) && !value_is_true($options['units']) ) ? false : $Ecart->Settings->get('weight_unit');

				$string = '';
				if (array_key_exists('id',$options)) $string .= $variation->id;
				if (array_key_exists('label',$options)) $string .= $variation->label;
				if (array_key_exists('type',$options)) $string .= $variation->type;
				if (array_key_exists('sku',$options)) $string .= $variation->sku;
				if (array_key_exists('price',$options)) $string .= money($variation->price+($variation->price*$taxrate));
				if (array_key_exists('saleprice',$options)) {
					if (isset($options['promos']) && !value_is_true($options['promos'])) {
						$string .= money($variation->saleprice+($variation->saleprice*$taxrate));
					} else $string .= money($variation->promoprice+($variation->promoprice*$taxrate));
				}
				if (array_key_exists('stock',$options)) $string .= $variation->stock;
				if (array_key_exists('weight',$options)) $string .= round($variation->weight, 3) . ($weightunit ? " $weightunit" : false);
				if (array_key_exists('shipfee',$options)) $string .= money(floatvalue($variation->shipfee));
				if (array_key_exists('sale',$options)) return ($variation->sale == "on");
				if (array_key_exists('shipping',$options)) return ($variation->shipping == "on");
				if (array_key_exists('tax',$options)) return ($variation->tax == "on");
				if (array_key_exists('inventory',$options)) return ($variation->inventory == "on");
				return $string;
				break;
			case "has-addons":
				return ($this->addons == "on" && !empty($this->options['a'])); break;
				break;
			case "addons":

				$string = "";

				if (!isset($options['mode'])) {
					if (!$this->priceloop) {
						reset($this->prices);
						$this->priceloop = true;
					} else next($this->prices);
					$thisprice = current($this->prices);

					if ($thisprice && $thisprice->type == "N/A")
						next($this->prices);

					if ($thisprice && $thisprice->context != "addon")
						next($this->prices);

					if (current($this->prices) !== false) return true;
					else {
						$this->priceloop = false;
						return false;
					}
					return true;
				}

				if ($this->outofstock) return false; // Completely out of stock, hide menus
				if (!isset($options['taxes'])) $options['taxes'] = null;

				$defaults = array(
					'defaults' => '',
					'disabled' => 'show',
					'before_menu' => '',
					'after_menu' => ''
					);

				$options = array_merge($defaults,$options);

				if (!isset($options['label'])) $options['label'] = "on";
				if (!isset($options['required'])) $options['required'] = __('You must select the options for this item before you can add it to your shopping cart.','Ecart');
				if ($options['mode'] == "single") {
					if (!empty($options['before_menu'])) $string .= $options['before_menu']."\n";
					if (value_is_true($options['label'])) $string .= '<label for="product-options'.$this->id.'">'. __('Options').': </label> '."\n";

					$string .= '<select name="products['.$this->id.'][price]" id="product-options'.$this->id.'">';
					if (!empty($options['defaults'])) $string .= '<option value="">'.$options['defaults'].'</option>'."\n";

					foreach ($this->prices as $pricetag) {
						if ($pricetag->context != "addon") continue;

						if (isset($options['taxes']))
							$taxrate = ecart_taxrate(value_is_true($options['taxes']),$pricetag->tax,$this);
						else $taxrate = ecart_taxrate(null,$pricetag->tax,$this);
						$currently = ($pricetag->sale == "on")?$pricetag->promoprice:$pricetag->price;
						$disabled = ($pricetag->inventory == "on" && $pricetag->stock == 0)?' disabled="disabled"':'';

						$price = '  ('.money($currently).')';
						if ($pricetag->type != "N/A")
							$string .= '<option value="'.$pricetag->id.'"'.$disabled.'>'.$pricetag->label.$price.'</option>'."\n";
					}

					$string .= '</select>';
					if (!empty($options['after_menu'])) $string .= $options['after_menu']."\n";

				} else {
					if (!isset($this->options['a'])) return;

					$taxrate = ecart_taxrate($options['taxes'],true,$this);

					// Index addon prices by option
					$pricing = array();
					foreach ($this->prices as $pricetag) {
						if ($pricetag->context != "addon") continue;
						$pricing[$pricetag->options] = $pricetag;
					}

					foreach ($this->options['a'] as $id => $menu) {
						if (!empty($options['before_menu'])) $string .= $options['before_menu']."\n";
						if (value_is_true($options['label'])) $string .= '<label for="options-'.$menu['id'].'">'.$menu['name'].'</label> '."\n";
						$category_class = isset($Ecart->Category->slug)?'category-'.$Ecart->Category->slug:'';
						$string .= '<select name="products['.$this->id.'][addons][]" class="'.$category_class.' product'.$this->id.' addons" id="addons-'.$menu['id'].'">';
						if (!empty($options['defaults'])) $string .= '<option value="">'.$options['defaults'].'</option>'."\n";
						foreach ($menu['options'] as $key => $option) {

							$pricetag = $pricing[$option['id']];

							if (isset($options['taxes']))
								$taxrate = ecart_taxrate(value_is_true($options['taxes']),$pricetag->tax,$this);
							else $taxrate = ecart_taxrate(null,$pricetag->tax,$this);

							$currently = ($pricetag->sale == "on")?$pricetag->promoprice:$pricetag->price;
							if ($taxrate > 0) $currently = $currently+($currently*$taxrate);
							$string .= '<option value="'.$option['id'].'">'.$option['name'].' (+'.money($currently).')</option>'."\n";
						}

						$string .= '</select>';
					}
					if (!empty($options['after_menu'])) $string .= $options['after_menu']."\n";

				}

				return $string;
				break;

			case "donation":
			case "amount":
			case "quantity":
				if ($this->outofstock) return false;

				$inputs = array('text','menu');
				$defaults = array(
					'value' => 1,
					'input' => 'text', // accepts text,menu
					'labelpos' => 'before',
					'label' => '',
					'options' => '1-15,20,25,30,40,50,75,100',
					'size' => 3
				);
				$options = array_merge($defaults,$options);
				$_options = $options;
				extract($options);

				unset($_options['label']); // Interferes with the text input value when passed to inputattrs()
				$labeling = '<label for="quantity-'.$this->id.'">'.$label.'</label>';

				if (!isset($this->_prices_loop)) reset($this->prices);
				$variation = current($this->prices);
				$_ = array();

				if ("before" == $labelpos) $_[] = $labeling;
				if ("menu" == $input) {
					if ($this->inventory && $this->max['stock'] == 0) return "";

					if (strpos($options,",") !== false) $options = explode(",",$options);
					else $options = array($options);

					$qtys = array();
					foreach ((array)$options as $v) {
						if (strpos($v,"-") !== false) {
							$v = explode("-",$v);
							if ($v[0] >= $v[1]) $qtys[] = $v[0];
							else for ($i = $v[0]; $i < $v[1]+1; $i++) $qtys[] = $i;
						} else $qtys[] = $v;
					}
					$_[] = '<select name="products['.$this->id.'][quantity]" id="quantity-'.$this->id.'">';
					foreach ($qtys as $qty) {
						$amount = $qty;
						$selection = (isset($this->quantity))?$this->quantity:1;
						if ($variation->type == "Donation" && $variation->donation['var'] == "on") {
							if ($variation->donation['min'] == "on" && $amount < $variation->price) continue;
							$amount = money($amount);
							$selection = $variation->price;
						} else {
							if ($this->inventory && $amount > $this->max['stock']) continue;
						}
						$selected = ($qty==$selection)?' selected="selected"':'';
						$_[] = '<option'.$selected.' value="'.$qty.'">'.$amount.'</option>';
					}
					$_[] = '</select>';
				} elseif (valid_input($input)) {
					if ($variation->type == "Donation" && $variation->donation['var'] == "on") {
						if ($variation->donation['min']) $_options['value'] = $variation->price;
						$_options['class'] .= " currency";
					}
					$_[] = '<input type="'.$input.'" name="products['.$this->id.'][quantity]" id="quantity-'.$this->id.'"'.inputattrs($_options).' />';
				}

				if ("after" == $labelpos) $_[] = $labeling;
				return join("\n",$_);
				break;
			case "input":
				if (!isset($options['type']) ||
					($options['type'] != "menu" && $options['type'] != "textarea" && !valid_input($options['type']))) $options['type'] = "text";
				if (!isset($options['name'])) return "";
				if ($options['type'] == "menu") {
					$result = '<select name="products['.$this->id.'][data]['.$options['name'].']" id="data-'.$options['name'].'-'.$this->id.'"'.inputattrs($options,$select_attrs).'>';
					if (isset($options['options']))
						$menuoptions = preg_split('/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/',$options['options']);
					if (is_array($menuoptions)) {
						foreach($menuoptions as $option) {
							$selected = "";
							$option = trim($option,'"');
							if (isset($options['default']) && $options['default'] == $option)
								$selected = ' selected="selected"';
							$result .= '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
						}
					}
					$result .= '</select>';
				} elseif ($options['type'] == "textarea") {
					if (isset($options['cols'])) $cols = ' cols="'.$options['cols'].'"';
					if (isset($options['rows'])) $rows = ' rows="'.$options['rows'].'"';
					$result .= '<textarea name="products['.$this->id.'][data]['.$options['name'].']" id="data-'.$options['name'].'-'.$this->id.'"'.$cols.$rows.inputattrs($options).'>'.$options['value'].'</textarea>';
				} else {
					$result = '<input type="'.$options['type'].'" name="products['.$this->id.'][data]['.$options['name'].']" id="data-'.$options['name'].'-'.$this->id.'"'.inputattrs($options).' />';
				}

				return $result;
				break;
			case "outofstock":
				if ($this->outofstock) {
					$label = isset($options['label'])?$options['label']:$Ecart->Settings->get('outofstock_text');
					$string = '<span class="outofstock">'.$label.'</span>';
					return $string;
				} else return false;
				break;
			case "buynow":
				if (!isset($options['value'])) $options['value'] = __("Buy Now","Ecart");
			case "addtocart":

				if (!isset($options['class'])) $options['class'] = "addtocart";
				else $options['class'] .= " addtocart";
				if (!isset($options['value'])) $options['value'] = __("Add to Cart","Ecart");
				$string = "";

				if ($this->outofstock) {
					$string .= '<span class="outofstock">'.$Ecart->Settings->get('outofstock_text').'</span>';
					return $string;
				}
				if (isset($options['redirect']) && !isset($options['ajax']))
					$string .= '<input type="hidden" name="redirect" value="'.$options['redirect'].'" />';

				$string .= '<input type="hidden" name="products['.$this->id.'][product]" value="'.$this->id.'" />';

				if (!empty($this->prices[0]) && $this->prices[0]->type != "N/A")
					$string .= '<input type="hidden" name="products['.$this->id.'][price]" value="'.$this->prices[0]->id.'" />';

				if (!empty($Ecart->Category)) {
					if (ECART_PRETTYURLS)
						$string .= '<input type="hidden" name="products['.$this->id.'][category]" value="'.$Ecart->Category->uri.'" />';
					else
						$string .= '<input type="hidden" name="products['.$this->id.'][category]" value="'.((!empty($Ecart->Category->id))?$Ecart->Category->id:$Ecart->Category->slug).'" />';
				}

				$string .= '<input type="hidden" name="cart" value="add" />';
				if (isset($options['ajax'])) {
					if ($options['ajax'] == "html") $options['class'] .= ' ajax-html';
					else $options['class'] .= " ajax";
					$string .= '<input type="hidden" name="ajax" value="true" />';
					$string .= '<input type="button" name="addtocart" '.inputattrs($options).' />';
				} else {
					$string .= '<input type="submit" name="addtocart" '.inputattrs($options).' />';
				}

				return $string;
		}


	}

} // END class Product

class Spec extends MetaObject {

	function __construct ($id=false) {
		$this->init(self::$table);
		$this->load($id);
		$this->context = 'product';
		$this->type = 'spec';
	}

	function updates ($data,$ignores=array()) {
		parent::updates($data,$ignores);
		if (preg_match('/^.*?(\d+[\.\,\d]*).*$/',$this->value))
			$this->numeral = preg_replace('/^.*?(\d+[\.\,\d]*).*$/','$1',$this->value);
	}

} // END class Spec

?>