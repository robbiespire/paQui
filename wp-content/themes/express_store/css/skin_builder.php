<?php header("Content-type: text/css; charset: UTF-8"); ?>
<?php require_once( '../../../../wp-load.php' );
?>

<?php if (!epanel_option('fontreplacement')); { ?>
@import "typography.php";
<?php } ?>

<?php if (epanel_option('epanel_theme') =='custom') { ?>
/******************************************************************
	Main Settings
******************************************************************/

body {
	background: <?php echo epanel_option('bodybg'); ?> <?php if(epanel_option('background_custom_img') !=='') { echo 'url('.epanel_option('background_custom_img').')'; } ?> <?php echo epanel_option('additional_bg_rules');?>;
}



h1 {
	font-size: 24px;
}

h2 {
	font-size: 22px;
}

h3 {
	font-size: 20px;
}

h4 {
	font-size: 18px;
}

h5 {
	font-size: 16px;
}

h6 {
	font-size: 14px;
}


.container { 
	text-align: left; 
	width: 960px; 
	margin: 0 auto; 
	position: relative; 
	height: 100%; 
}

.clearfix { 
	visibility: hidden; 
	display: block; 
	font-size: 0; 
	content: " "; 
	clear: both; 
	height: 0; 
}

#wrapper {
	margin: 0 auto 50px auto;
	padding: 0 0 0 0;
	width: 1090px;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
	-o-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
	-webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
	-moz-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
	background: white;
}

#header {
	border-top: 4px solid #686868;
}

#main-header{
	margin-top: 30px;
	margin-bottom: 30px;
	padding-bottom: 30px;
	border-bottom: 1px dotted #f0f0f0;
}

/******************************************************************
	Top Header Navigation Menus Settings
******************************************************************/

.left-content p.no-menu, .right-content p.no-menu {
	font-size: 11px;
	color: #c1c1c1;
	line-height: 55px;
}

.left-content p.no-menu a, .right-content p.no-menu a {
	color: #000;
	text-decoration: none;
}

.left-content {
	float:left;
}

.right-content {
	float:right;
}

#test {
	display:block;
	background: #000;
	height:50px;
}

/* General Stylings */

.top-menu li, #top-left-menu.epanel_mega li, #top-right-menu.epanel_mega li {
	display: block;
	float: left;
	margin-right: 10px;
	line-height: 53px;
}

.top-menu li a, #top-left-menu.epanel_mega li a, #top-right-menu.epanel_mega li a {
	color: #c1c1c1;
	font-size: 13px;
	text-transform: uppercase;
	text-decoration: none;
	text-shadow: none;
}

.top-menu li a:hover, #top-left-menu.epanel_mega li a:hover, #top-right-menu.epanel_mega li a:hover {
	color: #878787;
}

.custom-left-content {
	line-height:53px;
	color:#c1c1c1;
	font-size:12px;
}



/*-----------------------------------------------------------------------------------*/
/*	Navigation & Menus
/*-----------------------------------------------------------------------------------*/


#main-menu-wrapper {
	position: absolute;
	right: 0px;
	top: 15px;
}


#main-menu, #main-menu *, #store-home-menu, #store-home-menu * {
	margin:			0;
	padding:		0;
	list-style:		none;
}
#main-menu, #store-home-menu {
	line-height:	1.0;
}
#main-menu ul, #store-home-menu ul {
	position:		absolute;
	top:			-999em;
	width:			10em; /* left offset of submenus need to match (see below) */
}
#main-menu ul li, #store-home-menu ul li {
	width:			100%;
}
#main-menu li:hover, #store-home-menu li:hover {
	visibility:		inherit; /* fixes IE7 'sticky bug' */
}
#main-menu li, #store-home-menu li {
	float:			left;
	position:		relative;
}
#main-menu a, #store-home-menu a {
	display:		block;
	position:		relative;
}
#main-menu li:hover ul,
#main-menu li.sfHover ul, #store-home-menu li:hover ul, #store-home-menu li.sfHover ul {
	left:			0;
	top:			2.5em; /* match top ul list item height */
	z-index:		99;
}
ul#main-menu li:hover li ul, #store-home-menu li:hover li ul, #store-home-menu li.sfHover li ul,
ul#main-menu li.sfHover li ul {
	top:			-999em;
}
ul#main-menu li li:hover ul, #store-home-menu li li:hover ul, #store-home-menu li li.sfHover ul
ul#main-menu li li.sfHover ul {
	left:			10em; /* match ul width */
	top:			0;
}
ul#main-menu li li:hover li ul, #store-home-menu li li:hover li ul, #store-home-menu li li.sfHover li ul,
ul#main-menu li li.sfHover li ul {
	top:			-999em;
}
ul#main-menu li li li:hover ul, #store-home-menu li li li:hover ul, #store-home-menu li li li.sfHover ul,
ul#main-menu li li li.sfHover ul {
	left:			10em; /* match ul width */
	top:			0;
}

/*-----------------------------------------------------------------------------------*/
/*	Menu Skin
/*-----------------------------------------------------------------------------------*/

#main-menu-wrapper ul {
	margin: 0;
	padding: 0;
	list-style: none;
	line-height: 35px;
}

#main-menu-wrapper ul a, #store-home-wrapper ul a {
	display: block;
	position: relative;
}

#main-menu-wrapper ul li, #store-home-wrapper ul li {
	float: left;
	position: relative;
	z-index: 40;
}

#main-menu-wrapper ul li:hover, #store-home-wrapper ul li:hover { visibility: inherit; /* fixes IE7 'sticky bug' */ }

#main-menu-wrapper ul ul, #store-home-wrapper ul ul {
	position: absolute;
	top: -9999em;
	width: 150px; /* left offset of submenus need to match (see below) */
}

#main-menu-wrapper ul ul li, #store-home-wrapper ul ul li { width: 100%; }

/*  Make sub menus appear */
#main-menu-wrapper ul li:hover ul, #store-home-wrapper ul li:hover ul, #store-home-wrapper ul li.sfHover ul, 
#main-menu-wrapper ul li.sfHover ul {
	left: -1px;
	top: 36px; /* match top ul list item height */
	z-index: 99;
}

/* Hide all subs subs (4 levels deep) */
#main-menu-wrapper ul li:hover li ul,
#main-menu-wrapper ul li.sfHover li ul,
#main-menu-wrapper ul li li:hover li ul,
#main-menu-wrapper ul li li.sfHover li ul,
#main-menu-wrapper ul li li li:hover li ul,
#main-menu-wrapper ul li li li.sfHover li ul,
#store-home-wrapper ul li:hover li ul,
#store-home-wrapper ul li.sfHover li ul,
#store-home-wrapper ul li li:hover li ul,
#store-home-wrapper ul li li.sfHover li ul,
#store-home-wrapper ul li li li:hover li ul,
#store-home-wrapper ul li li li.sfHover li ul { top: -9999em; }

/* Displays all subs subs (4 levels deep) */
#main-menu-wrapper ul li li:hover ul,
#main-menu-wrapper ul li li.sfHover ul,
#main-menu-wrapper ul li li li:hover ul,
#main-menu-wrapper ul li li li.sfHover ul,
#main-menu-wrapper ul li li li li:hover ul,
#main-menu-wrapper ul li li li li.sfHover ul,
#store-home-wrapper ul li li:hover ul,
#store-home-wrapper ul li li.sfHover ul,
#store-home-wrapper ul li li li:hover ul,
#store-home-wrapper ul li li li.sfHover ul,
#store-home-wrapper ul li li li li:hover ul,
#store-home-wrapper ul li li li li.sfHover ul {
	left: 180px; /* match .nav ul width */
	top: -1px;
}
	
/* top level skin */
#main-menu-wrapper ul a, #store-home-wrapper ul a {
	padding: 0 15px;
	color: <?php echo epanel_option('menu_link');?>;
	font-size: 14px;
	font-weight: bold;
	text-decoration: none;
}

#main-menu-wrapper > ul > li > a, #store-home-wrapper > ul > li > a {
	text-transform: uppercase;
	font-weight: normal;
}


#main-menu-wrapper ul li a:hover,
#main-menu-wrapper ul li:hover,
#main-menu-wrapper ul li.sfHover a,
#main-menu-wrapper ul li.current-cat a,
#main-menu-wrapper ul li.current_page_item a,
#main-menu-wrapper ul li.current-menu-item a,
#store-home-wrapper ul li a:hover,
#store-home-wrapper ul li:hover,
#store-home-wrapper ul li.sfHover a,
#store-home-wrapper ul li.current-cat a,
#store-home-wrapper ul li.current_page_item a,
#store-home-wrapper ul li.current-menu-item a {
	text-decoration: none;
	color: <?php echo epanel_option('menu_link_hover');?> !important;
}

/* 2nd level skin */
#main-menu-wrapper ul ul, #store-home-wrapper ul ul {
	padding: 10px 15px 10px 15px;
	margin: 10px 0 0 0;
	background: #fff;
	border: 1px solid #e0e0e0;
	-webkit-border-radius: 3px;
	   -moz-border-radius: 3px;
	   		border-radius: 3px;
	-webkit-box-shadow:0 0 4px rgba(0, 0, 0, 0.1);
	   -moz-box-shadow:0 0 4px rgba(0, 0, 0, 0.1);
    		box-shadow:0 0 4px rgba(0, 0, 0, 0.1);
}

#main-menu-wrapper ul ul li, #store-home-wrapper ul ul li {
	height: 35px;
	line-height: 35px;
	float: none;
	background: none;
	border-bottom: 1px solid #f0f0f0;
}

#main-menu-wrapper ul ul li:last-child, #store-home-wrapper ul ul li:last-child {	border-bottom: none; }

#main-menu-wrapper ul ul li a, #store-home-wrapper ul ul li a {
	line-height: 35px;
	height: 35px;
	font-size: 12px;
	padding:0;
	font-weight: normal;
}

#main-menu-wrapper ul li.sfHover ul a, #store-home-wrapper ul li.sfHover ul a { color: #999!important; }

#main-menu-wrapper ul li.sfHover ul a:hover, #store-home-wrapper ul li.sfHover ul a:hover { color: #444!important; }


/*-----------------------------------------------------------------------------------*/
/*	Homepage Settings
/*-----------------------------------------------------------------------------------*/

#slider-wrapper {
	display: block;
	min-height: 360px;
	height: auto;
	width: auto;
	padding-bottom: 30px;
	background: url(../images/slider_bottom.png) bottom no-repeat;
	position: relative;
}

.nivo-nextNav {
	width: 86px;
	height: 106px;
	background: transparent url(../images/arr-next.png) no-repeat;
	display: block;
	font-size: 0px;
	text-indent: -9999px;
	
}

#nivo-slider {
	background:url(../images/ajax-loader.gif) no-repeat 50% 50%;
	width: 960px;
	min-height: 360px;
}

#nivo-slider img {
    position:absolute;
    top:0px;
    left:0px;
    display:none;
}



.nivo-prevNav {
	width: 86px;
	height: 106px;
	background: transparent url(../images/arr-prev.png) no-repeat;
	display: block;
	font-size: 0px;
	text-indent: -9999px;
	
}

#intro-content {
	text-align: center;
	padding: 0 0 20px 0;
	font-size: 22px;
	line-height: 28px;
	font-style: italic;
	color: #4f4f4f;
	margin-top: 30px;
	border-bottom: 1px dotted #f0f0f0;
}

#intro-content h2 {
	font-weight: normal !important;
}

#my-pager {
	position: absolute;
	bottom: -40px;
	left: 437.5px;
}

#my-pager a {
	position:relative;
	z-index:9;
	cursor:pointer;
	width: 15px;
	height: 15px;
	background: url(../images/page_nav.png) no-repeat;
	float: left;
	list-style: none;
	margin: 0 1px;
	display: block;
	font-size: 0px;
	line-height: 0px;
	text-indent: -9999px;
}

#my-pager a.pager-active {
	background: url(../images/page_nav_active_black.png) no-repeat !important;
}

/* Bx slider settings */

.bx-caption {
	position: absolute;
	background: transparent url(../images/bx_caption.png) repeat;
	color: #fff !important;
	z-index: 9999 !important;
	display: block !important;
	font-size: 12px;
	padding: 10px;
	line-height: 1.6em;
}

.slide-caption-right {
	position: absolute;
	background: transparent url(../images/bx_caption.png) repeat;
	height: 100px;
	width: 300px;
	-webkit-border-top-left-radius: 5px;
	-moz-border-radius-topleft: 5px;
	border-top-left-radius: 5px;
	margin-top: -127px;
	margin-left: 635px;
	
}

.slide-caption-left {
	position: absolute;
	background: transparent url(../images/bx_caption.png) repeat;
	height: 100px;
	width: 300px;
	-webkit-border-top-right-radius-radius: 5px;
	-moz-border-radius-topright: 5px;
	border-top-right-radius: 5px;
	margin-top: -127px;
	margin-left: 5px;
	
}

.slide-title-left {
	width: 450px;
	min-height: 50px;
	font-size: 20px;
	margin-top: -127px;
	margin-left: 5px;
	font-weight: normal;
	text-align: center;
	line-height: 50px;
}

.slide-title-right {
	width: 450px;
	min-height: 50px;
	font-size: 20px;
	font-weight: normal;
	text-align: center;
	line-height: 50px;
	margin-top: -127px;
	margin-left: 485px;
}

.slide-title-center {
	width: 450px;
	min-height: 50px;
	font-size: 20px;
	font-weight: normal;
	text-align: center;
	line-height: 50px;
	margin-top: -127px;
	margin-left: 230px;
}


/*-----------------------------------------------------------------------------------*/
/*	Homepage Product Scroller
/*-----------------------------------------------------------------------------------*/


h5.container {
	font-style: italic;
	font-size: 15px;
	height: 50px;
	line-height: 50px;
	font-weight: normal;
	color: #686868;
	border-bottom: 1px dotted #f0f0f0;
}

#products-scroller li {
	display: block;
	float: left;
	padding: 20px 15px 0px 15px;
}

#products-scroller a {
	color: #7193B2;
	text-decoration: none;
}

#products-scroller img {
	padding: 4px;
	border: 1px solid #f0f0f0;
}

#scroller-wrapper p {
	text-align: center;
	font-size: 12px;
	text-transform: uppercase;
	color: #616161;
	margin-top: 10px;
}

#scroller-wrapper {
	padding-bottom: 15px;
	border-bottom: 1px dotted #f0f0f0;
}

/*-----------------------------------------------------------------------------------*/
/*	Homepage Content
/*-----------------------------------------------------------------------------------*/

#homepage-content {
	margin-top: 30px;
	font-size: 12px;
	color: #717171;
	line-height: 1.6em;
}

#homepage-content p {
	margin-bottom: 15px;
}

#homepage-content h1, #homepage-content h2, #homepage-content h3, #homepage-content h4, #homepage-content h5, #homepage-content h6 {
	color: #000;
	font-weight: normal;
	margin-bottom: 15px;
} 

/* Product Scroller */

.scroller-product-details {
	width: 205px;
	margin-top: -1px;
	text-transform: none !important;
	line-height: 1.6em;
}

#homepage-content a, .homepage-content a:visited {
	color: #7193B2;
	text-decoration: none;
}

.footer-widget a:hover {
	text-decoration: underline;
}


.home-product-details {
	font-size: 11px;
	margin-bottom: 15px;
}

.home-product-details .product-name {
	font-size: 13px;
}

.home-product img {
	padding: 4px;
	border: 1px solid #f0f0f0;
	margin-bottom: 10px;
	background: #fff;
}

.home-product {
	margin-bottom: 40px;
}

.home-product span.price {
	color: #000;
}

.home-product span.read-more {
	float: right;
}

.home-product span.sale-price {
	position: absolute;
	left: 0px;
	margin-top: 25px;
	color: green;
}

/*-----------------------------------------------------------------------------------*/
/*	Innerpage Content
/*-----------------------------------------------------------------------------------*/

#inner-page {
	margin-top: 30px;
	font-size: 12px;
	color: #717171;
	line-height: 1.6em;
}

#inner-page p {
	margin-bottom: 15px;
}

#inner-page h1, #inner-page h2, #inner-page h3, #inner-page h4, #inner-page h5, #inner-page h6 {
	color: #000;
	font-weight: normal;
	margin-bottom: 15px;
} 

#inner-page a, #inner-page a:visited, #crumbs a, #crumbs a:visited, ul.breadcrumb a, ul.breadcrumb a:visited {
	color: #7193B2;
	text-decoration: none;
}

#inner-page a:hover, #crubms a:hover, ul.breadcrumb a:hover {
	text-decoration: underline;
}

.page-widget {
	margin-bottom: 30px;
}

#crumbs, ul.breadcrumb {
	border-bottom: 1px dotted #f0f0f0;
	font-size: 12px;
	padding-bottom: 20px;
	margin-top: -10px;
}

ul.breadcrumb {
	margin-top: -15px;
	padding-bottom: 25px;
}

ul.breadcrumb li {
	float: left;
	display: block;
	margin-right: 5px;
}

/*-----------------------------------------------------------------------------------*/
/*	Catalog Settings
/*-----------------------------------------------------------------------------------*/


.alignright {
	float: right;
	display: block;
}

.catalog-cat-name {
	display: block;
	float: left !important;
	margin-bottom: 0px;
}

.catalog-dropdown {
	display: block;
	float: right;
}

.catalog-product img {
	padding: 4px;
	border: 1px solid #f0f0f0;
}

.product-details .product-name {
	font-size: 13px;
	font-weight: normal;
}

div.catalog-product {
	margin-bottom: 30px;
}

.catalog-price {
	font-weight: bold;
	color: #000;
}

.old-price {
	font-weight: bold;
	text-decoration:line-through;
}

div.discounted-price p {
	float: left;
	margin-right: 5px;
}

.new-price ~ br {
	display: none !important;
}

div.new-price {
	position: absolute;
	left: 0px;
	margin-top: 20px;
	font-weight: bold;
	color: green;
}

a.catalog-link {
	float: right;
	margin-top: -19px;
}

a.catalog-link-discount {
	float: right;
}

ul.paging li {
	display: inline;
	border: 1px solid #EFEFEF;
	margin-right: 7px;
	padding: 4px 9px !important;
	float: left;
}

li.previous.disabled, li.next.disabled {
	display: none !important;
}

/*-----------------------------------------------------------------------------------*/
/*	Singular product
/*-----------------------------------------------------------------------------------*/

#main-picture img {
	border: 1px solid #f0f0f0;
	padding: 4px;
}

.pictures-wrapper {
	float: left;
	margin-right: 40px;
}

#singular-product {
	width: 426px;
	margin-left: 100px;
}

#thumbs-list {
	padding-left: 4px !important;
	width: 253px;
}

#thumbs-list li {
	margin-right: 7px !important;
	margin-bottom: 5px !important;
}

#thumbs-list li img{
	padding: 4px;
	border: 1px solid #f0f0f0;
}

.singular-price {
	font-size: 14px !important;
	font-weight: bold;
	color: #000;
}

.old-price {
	text-decoration: line-through;
}

.newdiscounted {
	color: green;
	font-weight: bold;
	margin-top: -10px;
}

div.product-prices {
	border-top: 1px dotted #f0f0f0;
	padding-top: 20px;
	padding-bottom: 45px;
	border-bottom:1px dotted #f0f0f0;
}

div.purchase-fields {
	position: absolute;
}

div.purchase-fields span {
	float: left;
}

div.purchase-fields input[type=text] {
	width: 100px;
	background: #fafafa;
	border: 1px solid #f0f0f0;
	position: relative;
	top: -19px;
	margin-left: 10px;
	padding: 5px 10px 5px 10px;
}

.purchase-fields input[type=button], .inner-form-coupon ul input[type=submit], #update-cart input[type=submit], #submit-login-checkout, #checkout-button, #confirm-button, #save-button, #searchsubmit, #customer-registration input[type=submit], #submit-login, #recover-button {
	display: inline-block;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	white-space: nowrap;
	line-height:1em;
	position:relative;
	outline: none;
	overflow: visible; /* removes extra side padding in IE */
	cursor: pointer;
	border: 1px solid #999;/* IE */
	border: rgba(0, 0, 0, .2) 1px solid;/* Saf4+, Chrome, FF3.6 */
	border-bottom:rgba(0, 0, 0, .4) 1px solid;
	-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
	-moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
	box-shadow: 0 1px 2px rgba(0,0,0,.2);
	background: -moz-linear-gradient(
	    center top,
	    rgba(255, 255, 255, .1) 0%,
	    rgba(0, 0, 0, .1) 100%
	);/* FF3.6 */
	background: -webkit-gradient(
	    linear,
	    center bottom,
	    center top,
	    from(rgba(0, 0, 0, .1)),
	    to(rgba(255, 255, 255, .1))
	);/* Saf4+, Chrome */
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='#19FFFFFF', EndColorStr='#19000000'); /* IE6,IE7 */
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr='#19FFFFFF', EndColorStr='#19000000')"; /* IE8 */
	-moz-user-select: none;
	-webkit-user-select:none;
	-khtml-user-select: none;
	user-select: none;
	padding: 5px 10px 5px 10px;
	position: relative;
	top: -22px;
	margin-left: 10px;
}

.purchase-fields input[type=button]::-moz-focus-inner, .inner-form-coupon ul input[type=submit]::moz-focus-inner, #update-cart input[type=submit]::-moz-focus-inner, #submit-login-checkout::-moz-focus-inner, #checkout-button::moz-focus-inner, #confirm-button::-moz-focus-inner, #save-button::-moz-focus-inner, #searchsubmit::-moz-focus-inner, #customer-registration input[type=submit]::-moz-focus-inner, #submit-login::-moz-focus-inner, #recover-button::-moz-focus-inner {
	border: none;
}

.purchase-fields input[type=button]:hover, .inner-form-coupon ul input[type=submit]:hover, #update-cart input[type=submit]:hover, #submit-login-checkout:hover, #checkout-button:hover, #confirm-button:hover, #save-button:hover, #searchsubmit:hover, #customer-registration input[type=submit]:hover, #submit-login:hover, #recover-button:hover {
	background: -moz-linear-gradient(
	    center top,
	    rgba(255, 255, 255, .2) 0%,
	    rgba(255, 255, 255, .1) 100%
	);/* FF3.6 */
	background: -webkit-gradient(
	    linear,
	    center bottom,
	    center top,
	    from(rgba(255, 255, 255, .1)),
	    to(rgba(255, 255, 255, .2))
	);/* Saf4+, Chrome */
	filter:  progid:DXImageTransform.Microsoft.gradient(startColorStr='#33FFFFFF', EndColorStr='#19FFFFFF'); /* IE6,IE7 */
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorStr='#33FFFFFF', EndColorStr='#19FFFFFF')"; /* IE8 */
}

.product-settings {
	border-bottom: 1px dotted #f0f0f0;
	padding-bottom: 20px;
}

.product-settings label{
	color: #000;
	font-size: 13px !important;
}

.product-settings ul li {
	float: left;
	display: block;
	margin-right: 20px !important;
}

#ecart-cart-ajax img {
	display: none;
}

/*-----------------------------------------------------------------------------------*/
/*	Shopping Cart Page
/*-----------------------------------------------------------------------------------*/

#shopping-table {
	margin-bottom: 30px;
	width: 960px;
}

table.productcart, div.shippingcart, div.promocart, div.cart-subtotal, div#shipping-container, #shopping-cart-billed, #shopping-cart-sent {
	border: 1px solid #F2F2F2 !important;
}

table.productcart tr.firstrow, #shipping-heading, #promo-heading, #subtotal-heading, #shipped-heading {
	background-color: #F2F2F2 !important;
	color: #666 !important;
	font-weight: normal !important;
	font-size: 13px !important;
}

tr.firstrow th, #shipping-heading, #promo-heading, #subtotal-heading, #shipped-heading {
	padding: 5px;
	text-align: center;
}

tr.product_row {
	border-bottom: 1px solid #F2F2F2 !important;
}

.right-row {
	text-align: center;
}

.item-column {
	padding: 10px 10px 10px 30px;
}

.item-column p {
	display: none;
}

.item-column select {
	float: right;
}

.right-row input[type=text] {
	background: #fff;
	border: 1px solid #f2f2f2;
	padding: 5px;
}

.remove-product {
	color: #ff6464 !important;
}

#shopping-total {
	width: 100%;
}

.shipping-row span br, .shipping-row span p {
	display: none;
}

.shipping-country {
	position: absolute !important;
	margin-left: 150px !important;
}

#shipping-heading, #promo-heading, #subtotal-heading, #shipped-heading {
	position: relative;
	top: 0px;
	display: block;
	width: 98% !important;
	font-weight: bold !important;
}

.inner-form, .inner-form-coupon {
	padding: 0px 10px 0px 10px;
}

.inner-form ul {
	position: absolute;
	margin-top: -35px !important;
	margin-left: 180px !important;
}

.shippingcart {
	margin-bottom: 30px;
}

.inner-form-coupon ul {
	position: absolute;
	margin-top: -38px !important;
	margin-left: 100px !important;
}

.inner-form-coupon ul input[type=text] {
	background: #fff;
	border: 1px solid #f0f0f0;
	padding: 5px;
	width: 150px;
}

.inner-form-coupon ul input[type=submit] {
	margin-top: 22px !important;
	margin-left: 20px !important;
} 

.inner-form-coupon p.error {
	position: absolute;
	margin-top: 50px;
}

div.promocart {
	padding-bottom: 20px;
}

#total-form p {
	color: #000;
}

#total-form p:last-child {
	border-bottom: none !important;
}

#total-form p span{
	float: right;
	font-weight: bold;
}

.last-total ~ p {
	display: none !important;
}

#update-cart input[type=submit] {
	margin-top: 40px;
}

.link-left {
	float: left;
}

.link-right {
	float: right;
}

#cart-navigation br {
	display: none !important;
}

#shopping-cart-wrapper {
	margin-top: 20px;
}

/*-----------------------------------------------------------------------------------*/
/*	Checkout Page
/*-----------------------------------------------------------------------------------*/

#customer-form li span {
	float: left !important;
}

#customer-form li label {
	color: #525252;
}

#customer-form input[type=text], #customer-form input[type=password] {
	background: #fff;
	border: 1px solid #f0f0f0;
	padding: 5px;
}

#customer-form input[type=text]:focus, ##customer-form input[type=password]:focus {
	border: 1px solid #ddd;
}

.reg-input {
	margin-bottom: 30px !important;
}

.reg-input input[type=text], #customer-form input[type=password]{
	width: 150px;
}

.reg-input br {
	display: none;
}

.space-head {
	margin-top: 30px;
}

#shipping-address-fields {
	margin-right: 12px !important;
}

#submit-login-checkout, #checkout-button, #confirm-button {
	top: 0px;
}

#shopping-cart-billed, #shopping-cart-sent {
	margin-top: 30px;
	margin-bottom: 30px;
}

#shopping-cart-subtotal-history {
	margin-top: -30px;
}

.non-fullwidth #shopping-table {
	width: 710px !important;
}

/* CUSTOMER AREA MENU */

#ecart ul.ecart.account li {
	display: block;
	float: left;
	padding-left: 45px;
	height: 40px;
	width: 300px;
	margin-bottom: 20px;
}

#ecart ul li .area-description {
	padding-top: 10px;
}

#ecart ul li.My.Account {
	background: transparent url(../images/account_userinfo.png) no-repeat  0px 15px !important;
}

#ecart ul li.Downloads {
	background: transparent url(../images/account_download.png) no-repeat  0px 20px !important;
}

#ecart ul li.Order.History {
	background: transparent url(../images/account_orders.png) no-repeat  0px 20px !important;
}

/* Custom link settings */
#ecart ul li.FAQs {
	background: transparent url(../images/account_faq.png) no-repeat  0px 20px !important;
}

#ecart ul li.Logout {
	background: transparent url(../images/account_logout.png) no-repeat  0px 20px !important;
}


#profile-update-form br {
	display: none;
}

#profile-update-form label {
	color: #525252 !important;
}

#profile-update-form input[type=text], #profile-update-form input[type=password] {
	width: 200px;
	background: #fff;
	border: 1px solid #f0f0f0;
	padding: 5px;
}

#profile-update-form input[type=text]:focus, #profile-update-form input[type=password]:focus {
	border: 1px solid #dfdfdf;
}

#save-button {
	top: 0px !important;
	left: -10px;
}
#account-table {
	width: 710px;
}

#account-table tr td {
	text-align: center;
}

/* register page */

#customer-registration input[type=text], #customer-registration input[type=password]{
	background: #fff;
	border: 1px solid #f0f0f0;
	width: 330px;
	padding: 5px;
}

#customer-registration input[type=text]:focus, #customer-registration input[type=password]:focus {
	border: 1px solid #dfdfdf;
}

#customer-registration input[type=submit] {
	top: 0px;
	left: -10px;
}

#sidebar form#login {
	margin-bottom: 100px !important;
}

#sidebar input[type=text], #sidebar input[type=password] {
	background: #fff;
	border: 1px solid #f0f0f0;
	width: 200px;
	padding: 5px;
}

#login-inputs label {
	display: block;
	margin-bottom: -5px;
}

#login-inputs {
	border: none !important;
}

/*-----------------------------------------------------------------------------------*/
/*	Custom Widget Settings
/*-----------------------------------------------------------------------------------*/

.screen-reader-text {
	display: none;
}

#searchform input[type=text] {
	background: #fff;
	border: 1px solid #f0f0f0;
	width: 200px;
	padding: 5px;
}

#searchsubmit {
	top: 10px;
	left: -10px;
}

#sidebar div.area-description {
	display:none;
}

#sidebar ul li {
	height: 35px;
	line-height: 35px;
	border-bottom: 1px solid #f0f0f0;
}

#submit-login {
	top: 0px;
	left: -10px;
}

#page-content form#login ul#side-login-form {
	display: none;
}

#page-content input[type=text] {
	background: #fff;
	border: 1px solid #f0f0f0;
	padding: 5px;
}

#page-content #recover-button {
	top: -12px;
	left: 20px;
}

/*-----------------------------------------------------------------------------------*/
/*	Blog Settings
/*-----------------------------------------------------------------------------------*/

.post-image img{
	background: #fff;
	border: 1px solid #f0f0f0;
	padding: 4px;
	display: block;
}

.post-image {
	margin-bottom: 15px;
}

ul.post-meta-info {
	height: 35px;
	line-height: 35px;
	border: 1px dotted #f0f0f0;
	margin-bottom: 20px;
}

ul.post-meta-info li {
	display: block;
	float: left;
	padding-left: 15px;
	padding-right: 15px;
	border-right:1px dotted #f0f0f0;
}

.post-layout {
	margin-bottom: 50px;
}

div.wp-pagenavi span, div.wp-pagenavi a {
	background: #fff;
	border: 1px solid #f0f0f0;
	padding:5px;
	margin-right: 5px;
}

/*----------------------- COMMENTS ---------------------------*/
/* comment */

#comments{margin-top:50px;}

#comments .date{
	padding-left:2px;
	color:#BBBBBB;
	font-size:9px;
	line-height:15px;
	text-transform:uppercase;
}

.comment-text{
	color:#888888;
	left:25px;
	margin:10px 0;
	min-height:90px;
	overflow:hidden;
	padding-right:40px;
	padding-top:8px;
	position:relative;
	text-shadow:1px 1px 1px #FFFFFF;
}

.comment-author{
	float:left;
	overflow:hidden;
	width:90px;
}


.small_frame img {
	background: #fff;
	padding: 5px;
	border: 1px solid #eaeaea;
}

h4#comments { 
	clear: both;
	margin: 45px 0 5px 0;
	font-size:20px;
}


.commentlist cite {
	display:inline-block;
	font-style:normal;
	line-height:16px;
	padding-left:2px;
	padding-top:5px;
	text-transform:capitalize;
}

.commentlist cite , .commentlist cite a:link, .commentlist cite a:visited {color: #666;}
.commentmetadata ,.commentlist .reply {
	float:left;
	font-size:10px;
	margin-right:5px;
	text-transform:lowercase;
}

.commentlist {margin: 0 0 20px 0;}

.commentlist li {
	list-style-image:none;
	list-style-position:outside;
	list-style-type:none;
	margin-bottom: 10px;
	overflow:hidden;
	clear:both;
}

.commentlist > li {
	border-bottom: 1px solid #ccc;
	padding-bottom: 10px;
}

.commentlist li ul li { margin-left: 20px;}

.cancel-comment-reply a{ 
	color:#bbb;	
	font-size:9px;
	padding-left:2px;
	text-transform:uppercase;
	line-height:15px;
}

.comms-navigation, .navigation { 
	clear: both;
	display: block;
	margin-bottom:0px;
	overflow: hidden;
	font-size: 12px;
}

.children {padding: 0;}

.nocomments {
	text-align: center;
	margin: 0;
	padding: 0;
}

#commentform { padding-top:20px; }

#respond{
	clear:both;
	padding-bottom:20px;
	margin-top: 20px;
}

#respond h3{margin-bottom:0px;}

.date { 
	color:#555 !important;
	font-size:10px;
	text-transform:uppercase;
	line-height:15px;
}

.awaiting_moderation{
	background:#FFFFFF none repeat scroll 0 0;
	border:1px solid #EEEEEE;
	display:inline-block;
	font-size:10px;
	margin-bottom:10px;
	padding:0 10px;
}
	
.logged{margin-bottom:5px;}

#comments a{
	color: #6183A2 !important;
	text-decoration: none;
}

#comments a:hover {
	text-decoration: underline;
}

#respond input[type="text"]{
	background: #fff;
	border: 1px solid #f0f0f0;
	width: 200px;
	padding: 5px;
	margin-right: 10px;
}

#respond input[type="text"]:focus {
	border: 1px solid #dfdfdf;
}

#respond textarea {
	background: #fff;
	border: 1px solid #f0f0f0;
	width: 500px;
	padding: 5px;
	margin-right: 10px;
	height: 150px;	
}

 #respond input[type="text"], #respond textarea {
 	color: #555 !important;
 }

#respond input[type="submit"] {
	padding: 5px;
}

/* author box */ 

.authorbox { 
	font-size:13px;	
	line-height:20px; 
	padding:20px 20px 0 20px;	
	margin:3px 0 50px 0; 
	border-top:1px solid #d7d7d7; 
	border-bottom:1px solid #d7d7d7;
	 background:#efefef;
}

.authorbox .avatar { 
	float:left; 
	margin:0 20px 20px 0; 
	-moz-border-radius: 5px; 
	-webkit-border-radius: 5px; 
	border-radius: 5px; 
}

.authorbox .author_name { 
	padding:5px 0 0px 0; 
	font-size:15px;
}

.authorbox .author_name a {
	color: #6183A2;
	text-decoration: none;
}

.authorbox .author_name a:hover {
	text-decoration: underline;
}

.authorbox .author_desc	{ margin:0;}

.authorbox .author_links	{ 
	margin:-12px 0 0 0; 
	text-align:right;
}

.authorbox .author_links a	{ 
	font-size:12px; 
	padding:0 20px 0 0;
}

/* share bar */ 

#share-bar { 
	background: #e4e4e4;
	border: 1px solid #d7d7d7;
	-webkit-border-radius: 5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	width: 605px;
	padding-left: 25px;
	padding-bottom: 10px;
	padding-top: 10px;
}

#share-bar li {
	margin-right: 13px;
	display: inline;
}


/*-----------------------  END COMMENTS ---------------------------*/

.contact-area {
	margin-top: 30px;
	font-family: "Lucida Sans Unicode","Lucida Grande","Trebuchet MS",Helvetica,Arial,sans-serif;
}

#contactFormArea {
	padding-bottom: 50px;
}

#contactFormArea{
	margin:0px 0px 0px 0px;
	position:relative;  
}

.contact-area label {
	margin-bottom:5px;
	display:block;
	font-size: 12px;
	color: #555;
}

.contact-area .textfield {
	background-color:#ffffff;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:2px solid #d6d6d6;
	font-size:12px; 
	padding:7px 5px; 
	margin:0px 0px 10px 0px; 
	display:block;
	color:#939393;
	width:280px;
}

.contact-area .input{
	background-color:#ffffff;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:2px solid #d6d6d6;
	font-size:12px; 
	padding:7px 5px; 
	margin:0px 0px 10px 0px; 
	display:block;
	color:#939393;
	width:300px !important;
}

.contact-area .textarea{
	background-color:#ffffff;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:2px solid #d6d6d6;
	font-size:12px;
	overflow:hidden;
	padding:10px 10px;
	color:#939393;
	width:330px;
	height: 153px;
}

#contactFormArea button{
	top:220px;
	right:0px;
	position:absolute
}

.contact-area .submit {
	font-size:11px;
	background: #fff;
	color: grey;
	cursor: pointer;
	border:1px solid #cccccc;
	border-radius:5px;
	-moz-border-radius: 5px;
	-webkit-border-radius:5px;
	padding: 5px 10px 5px 10px;
	margin-top: 0px;
}

.contact-area .submit:hover {
	color: black;
}

.contact-area .buttoncontact{
  	font-weight:bold;
	margin-right:0px;
}

.contact-column-right{
	position:absolute;
	top:0px;
	right:10px;
}

.contact-area .loading{
  background:url(../images/ajax-loader.gif) top left no-repeat;
  padding-left:25px;
  color:#797979;
  margin-top:-10px;
  float:left;
}

.contact-area .success-contact {
  text-align:center;
  color:#3F9153;
  margin-bottom:20px;
  padding:8px 10px 8px 10px;
  width:680px;
  border:1px solid #A3F7B8;
  border-radius:5px;
  -webkit-border-radius:5px;
  -moz-border-radius: 5px;
  display: none;
  background: #A3F7B8;
 
} 


/*-----------------------------------------------------------------------------------*/
/*	Footer Settings
/*-----------------------------------------------------------------------------------*/

#footer-wrapper {
	background: #fafafa;
	border-top:1px solid #f0f0f0;
	margin-top: 30px;
}

#footer-wrapper a, #footer-wrapper a:visited {
	color: #7193B2;
	text-decoration: none;
}

#footer-wrapper a:hover {
	text-decoration: underline;
}

#footer-inner {
	padding-top:30px;
	padding-bottom: 30px;
}

.footer-widget h4 {
	margin-bottom: 15px;
	font-weight: normal;
}

.footer-widget {
	font-size: 12px;
}

.footer-widget p {
	font-size: 12px;
	color: #929292;
	line-height: 1.6em;
	margin-bottom: 15px;
}

#recentcomments li, #footer-inner ul li {
	border-bottom: 1px solid #f0f0f0;
	height: 35px;
	line-height: 35px;
}

#footer-bottom {
	margin-top: 30px;
	color: #525252;
	font-size: 12px;
	margin-bottom: 40px;
}

#footer-bottom div {
	float: left;
	display: block;
}

.footer-right {
	float: right !important;
}

ul#bottom-left-menu li, ul#bottom-right-menu li {
	display: block !important;
	float: left !important;
	border-bottom: none;
	margin-top: -10px;
}

ul#bottom-right-menu li {
	margin-left: 20px;
}

ul#bottom-left-menu li {
	margin-right:20px;
}

/* Skin dynamic settings */

/* Font override */

h1, h2, h3, h4, h5, h6 {
	color:<?php echo epanel_option('headings_color');?> !important;
}

#page-content a, #homepage-content a, #products-scroller a, #sidebar a {
	color:<?php echo epanel_option('content_link');?> !important;
} 

#page-content, #page-content p, #homepage-content, #homepage-content p {
	color:<?php echo epanel_option('content_text');?> !important; 
}

<?php } ?>