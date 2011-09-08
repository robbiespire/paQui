function init() {
	tinyMCEPopup.resizeToInnerSize();
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function epshortcodesubmit() {
	
	var tagtext;
	
	var ep_shortcode = document.getElementById('epshortcode_panel');
	var ep_shortcode2 = document.getElementById('epshortcode_panel2');
	
	// who is active ?
	if (ep_shortcode.className.indexOf('current') != -1) {
		var ep_shortcodeid = document.getElementById('epshortcode_tag').value;
		switch(ep_shortcodeid)
{
case 0:
 	tinyMCEPopup.close();
  break;

case "button":
	tagtext = "["+ ep_shortcodeid + "  url=\"#\" target=\"_self\" style=\"yellow\" position=\"left\"] Button text [/" + ep_shortcodeid + "]";
break;

case "alert":
	tagtext = "["+ ep_shortcodeid + " style=\"white\"] Alert text [/" + ep_shortcodeid + "]";
break;

case "video":
	tagtext = "["+ ep_shortcodeid + "  src=\"#\" type=\"youtube\" clip_id=\"j1x4-tcQO6E\"]";
break;

case "lightbox":
	tagtext = "["+ ep_shortcodeid + "  href=\"#\" title=\"title\" iframe=\"false\"] [/" + ep_shortcodeid + "]";
break;

case "imageright":
	tagtext = "["+ ep_shortcodeid + "]image_full_url.png [/" + ep_shortcodeid + "]";
break;

case "imageleft":
	tagtext = "["+ ep_shortcodeid + "]image_full_url.png [/" + ep_shortcodeid + "]";
break;

case "captionleft":
	tagtext = "["+ ep_shortcodeid + "  caption=\"this is a caption\" ]image_full_url.png [/" + ep_shortcodeid + "]";
break;


case "captionright":
	tagtext = "["+ ep_shortcodeid + "  caption=\"this is a caption\" ]image_full_url.png [/" + ep_shortcodeid + "]";
break;

case "captionleftlink":
	tagtext = "["+ ep_shortcodeid + "  caption=\"this is a caption\" href=\"#\" ]image_full_url.png [/" + ep_shortcodeid + "]";
break;

case "captionrightlink":
	tagtext = "["+ ep_shortcodeid + "  caption=\"this is a caption\" href=\"#\" ]image_full_url.png [/" + ep_shortcodeid + "]";
break;

case "note":
	tagtext = "["+ ep_shortcodeid + "  title=\"Title\" align=\"right\" width=\"250\" ] insert you content here [/" + ep_shortcodeid + "]";
break;

case "divider":
	tagtext = "["+ ep_shortcodeid + "]";
break;

case "toggle":
	tagtext = "["+ ep_shortcodeid + "  title=\"Toggle Title\"] insert you content here [/" + ep_shortcodeid + "]";
break;

case "tabs":
	tagtext = "["+ ep_shortcodeid + "] [tab title=\"title 1\"] contents	[/tab] [tab title=\"title 2\"] contents	[/tab] [/" + ep_shortcodeid + "]";
break;

case "accordions":
	tagtext = "["+ ep_shortcodeid + "] [accordion title=\"title 1\"] contents	[/accordion] [accordion title=\"title 2\"] contents	[/accordion] [/" + ep_shortcodeid + "]";
break;

case "checklist":
	tagtext = "["+ ep_shortcodeid + "] <li>list item</li> <li>list item</li> [/" + ep_shortcodeid + "]";
break;

case "nextlist":
	tagtext = "["+ ep_shortcodeid + "] <li>list item</li> <li>list item</li> [/" + ep_shortcodeid + "]";
break;

case "infolist":
	tagtext = "["+ ep_shortcodeid + "] <li>list item</li> <li>list item</li> [/" + ep_shortcodeid + "]";
break;

case "wronglist":
	tagtext = "["+ ep_shortcodeid + "] <li>list item</li> <li>list item</li> [/" + ep_shortcodeid + "]";
break;

case "doclist":
	tagtext = "["+ ep_shortcodeid + "] <li>list item</li> <li>list item</li> [/" + ep_shortcodeid + "]";
break;

case "dotlist":
	tagtext = "["+ ep_shortcodeid + "] <li>list item</li> <li>list item</li> [/" + ep_shortcodeid + "]";
break;

case "small-button-black":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "small-button-red":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "small-button-brown":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "small-button-blue":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "small-button-green":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "small-button-orange":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "small-button-violet":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "small-button-grey":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "big-button-black":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "big-button-red":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "big-button-blue":
	tagtext = "["+ ep_shortcodeid + " href=\"#\"]Button Text Here[/" + ep_shortcodeid + "]";
break;

case "framed-box":
	tagtext = "["+ ep_shortcodeid + " title=\"Plan Name Here\" price=\"$price\" fee=\"/mo\"] Inser you content here [/" + ep_shortcodeid + "]";
break;

default:
tagtext="["+ep_shortcodeid + "] Insert you content here [/" + ep_shortcodeid + "]";
}
}

if (ep_shortcode2.className.indexOf('current') != -1) {

	var ep_shortcodeid = document.getElementById('epshortcode_tag2').value;
			switch(ep_shortcodeid)
	{
	case 0:
	 	tinyMCEPopup.close();
	  break;
	  
	  case "5-columns-tab":
	  	tagtext = "["+ ep_shortcodeid + "] Insert your pricing tab shortcodes here [/" + ep_shortcodeid + "]";
	  break;
	  
	  case "plans-wrapper":
	  	tagtext = "["+ ep_shortcodeid + "] Delete this text and use the first plan shortcode and the plan shortcode to add new plans, the first plan shortcode is mandatory [/" + ep_shortcodeid + "]";
	  break;
	  
	  case "first-plan":
	  	tagtext = "["+ ep_shortcodeid + " name=\"Plan Name\" price=\"$40 per month\"]";
	  break;
	  
	  case "plan-wrap":
	  	tagtext = "["+ ep_shortcodeid + " name=\"Plan Name\" price=\"$40 per month\"]";
	  break;
	  
	  case "table-row":
	  	tagtext = "["+ ep_shortcodeid + " feature=\"Hey this is feature\"] Use the plan option shortcode and add options here [/" + ep_shortcodeid + "]";
	  break;
	 
	 default:
	 tagtext="["+ep_shortcodeid + "] Insert you content here [/" + ep_shortcodeid + "]";
	 }
	 }


if(window.tinyMCE) {
		//TODO: For QTranslate we should use here 'qtrans_textarea_content' instead 'content'
		window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
		//Peforms a clean up of the current editor HTML. 
		//tinyMCEPopup.editor.execCommand('mceCleanup');
		//Repaints the editor. Sometimes the browser has graphic glitches. 
		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.close();
	}
	return;
}