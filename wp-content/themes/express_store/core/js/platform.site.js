/**
 * Platform Javascript Functions - SITE Scope
 * Copyright (c) EPANEL_WP 2008 - 2011
 *
 * Written By Andrew Powers
 */


/*
 * ################################
 *   Feature Slider / Jquery Cycle
 * ################################
 */

function PlayPauseControl(){
	// Play Pause
	$j('.playpause').click(function() { 
		if ($j('.playpause').hasClass('pause')) {
			$j('#cycle').cycle('pause');
		 	$j('.playpause').removeClass('pause').addClass('resume');
		} else {
		   	$j('.playpause').removeClass('resume').addClass('pause');
		    $j('#cycle').cycle('resume', true); 	
		}
	});
}
