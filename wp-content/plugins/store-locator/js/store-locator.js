//<![CDATA[
    var map;
    var geocoder;
	
	var theIcon = new GIcon(G_DEFAULT_ICON);
	theIcon.image = sl_map_end_icon;
	//theIcon.image = add_base + "/icons/red_flag1.png";
	if (sl_map_end_icon.indexOf('flag')!='-1') {theIcon.shadow = add_base + "/icons/flag_shadow.png";}
	else if (sl_map_end_icon.indexOf('arrow')!='-1') {theIcon.shadow = add_base + "/icons/arrow_shadow.png";}
	else if (sl_map_end_icon.indexOf('bubble')!='-1') {theIcon.shadow = add_base + "/icons/bubble_shadow.png";}
	else if (sl_map_end_icon.indexOf('marker')!='-1') {theIcon.shadow = add_base + "/icons/marker_shadow.png";}
	else if (sl_map_end_icon.indexOf('sign')!='-1') {theIcon.shadow = add_base + "/icons/sign_shadow.png";}
	else {theIcon.shadow = add_base + "/icons/blank.png";}
	theIcon.iconSize = new GSize(sl_map_end_icon_width, sl_map_end_icon_height);
	//theIcon.iconSize = new GSize(40, 68);

	// Added by Moyo 5/23/08 11:52 am
	//var sidebar1 = document.getElementById('sidebar');
    //sidebar1.innerHTML = '';
    //if (markers.length == 0) {
		//sidebar1.innerHTML = '<h1>Enter Your Address or Zip Code Above.</h2>';
	//}
	
    function sl_load() {
      if (GBrowserIsCompatible()) {
        geocoder = new GClientGeocoder();
        map = new GMap2(document.getElementById('map'));
		//map.addControl(new GSmallMapControl());
		//map.addControl(new GSmallZoomControl());
		if (sl_map_overview_control==1) {
			map.addControl(new GOverviewMapControl());
			}
		//map.addControl(new GLargeMapControl); //11/29/08 1:19am Moyo
        //map.addControl(new GMapTypeControl());
		//map.addControl(new GMapTypeControl());
		map.addMapType(G_PHYSICAL_MAP);
		geocoder.getLatLng(sl_google_map_country, function(latlng) {
			map.setCenter(latlng, sl_zoom_level, sl_map_type);
			map.setUIToDefault();
		});
      }
	  
	  //added by Moyo 1/25/09 to show locations by default
	if (sl_load_locations_default=="1") {
	var bounds = new GLatLngBounds();
	  markerOpts = { icon:theIcon };
      GDownloadUrl(add_base + "/data-xml.php", function(data, responseCode) {
		var xml = GXml.parse(data);
		var markers = xml.documentElement.getElementsByTagName("marker");
		for (var i = 0; i < markers.length; i++) {
		//	var point = new GLatLng(parseFloat(markers[i].getAttribute("lat")),
          //                parseFloat(markers[i].getAttribute("lng")));
		//start					
		var name = markers[i].getAttribute('name');
         var address = markers[i].getAttribute('address');
         var distance = parseFloat(markers[i].getAttribute('distance'));
         var point = new GLatLng(parseFloat(markers[i].getAttribute('lat')),
                                 parseFloat(markers[i].getAttribute('lng')));
		 var description = markers[i].getAttribute('description');
		 var url = markers[i].getAttribute('url');
		 var hours = markers[i].getAttribute('hours');
		 var phone = markers[i].getAttribute('phone');
		 var image = markers[i].getAttribute('image');
		 
		 // end
			var marker = createMarker(point, name, address, "", description, url, hours, phone, image);
			map.addOverlay(marker);
			bounds.extend(point);
		}
		map.setCenter(bounds.getCenter(), (map.getBoundsZoomLevel(bounds)-1));
		map.setUIToDefault();
	  });
     }
	}

   function searchLocations() {
     var address = document.getElementById('addressInput').value;
     geocoder.getLatLng(address, function(latlng) {
       if (!latlng) {
         alert(address + ' not found');
       } else {
         searchLocationsNear(latlng, address); // address param added by Moyo 5/23/08
       }
     });
   }

   function searchLocationsNear(center, homeAddress) { // homeAddress param added by Moyo 5/23/08
     var radius = document.getElementById('radiusSelect').value;
	 var searchUrl = add_base + '/generate-xml.php?lat=' + center.lat() + '&lng=' + center.lng() + '&radius=' + radius;
     GDownloadUrl(searchUrl, function(data) {
       var xml = GXml.parse(data);
       var markers = xml.documentElement.getElementsByTagName('marker');
       map.clearOverlays();
	   
	    //marker for searched location - Moyo Aluko: 5/14/08, 4 am
	   var theIcon = new GIcon(G_DEFAULT_ICON);
		theIcon.image = sl_map_home_icon;
		if (sl_map_home_icon.indexOf('flag')!='-1') {theIcon.shadow = add_base + "/icons/flag_shadow.png";}
		else if (sl_map_home_icon.indexOf('arrow')!='-1') {theIcon.shadow = add_base + "/icons/arrow_shadow.png";}
		else if (sl_map_home_icon.indexOf('bubble')!='-1') {theIcon.shadow = add_base + "/icons/bubble_shadow.png";}
		else if (sl_map_home_icon.indexOf('marker')!='-1') {theIcon.shadow = add_base + "/icons/marker_shadow.png";}
		else if (sl_map_home_icon.indexOf('sign')!='-1') {theIcon.shadow = add_base + "/icons/sign_shadow.png";}
		else {theIcon.shadow = add_base + "/icons/blank.png";}
		theIcon.iconSize = new GSize(sl_map_home_icon_width, sl_map_home_icon_height);
		//theIcon.shadowSize = new GSize(30,30);
		
		var bounds = new GLatLngBounds(); //added here 1/25/09 by Moyo to handle extending bounds to show searched location
		markerOpts = { icon:theIcon };
		point = new GLatLng (center.lat(), center.lng());
		bounds.extend(point); //added 1/25/09 to handle showing searched location within bounds everytime
		var homeMarker = new GMarker(point, markerOpts);
      var html = '<div id="sl_info_bubble"><span class="your_location_label">Your Location:</span> <br/>' + homeAddress + '</div>';
      GEvent.addListener(homeMarker, 'click', function() {
        homeMarker.openInfoWindowHtml(html);
      });
      map.addOverlay(homeMarker);
	  //end marker for searched location

       var sidebar = document.getElementById('map_sidebar');
       sidebar.innerHTML = '';
       if (markers.length == 0) {
         sidebar.innerHTML = '<div class="no_results_found"><h2>No results found.</h2></div>';
         geocoder = new GClientGeocoder();
       	geocoder.getLatLng(sl_google_map_country, function(latlng) {
			map.setCenter(point, sl_zoom_level);
		});
         return;
       }
	   
       //var bounds = new GLatLngBounds(); //removed from here 1/25/09 to handle showing searched location with bounds at all times
       for (var i = 0; i < markers.length; i++) {
         var name = markers[i].getAttribute('name');
         var address = markers[i].getAttribute('address');
         var distance = parseFloat(markers[i].getAttribute('distance'));
         var point = new GLatLng(parseFloat(markers[i].getAttribute('lat')),
                                 parseFloat(markers[i].getAttribute('lng')));
		 var description = markers[i].getAttribute('description');
		 var url = markers[i].getAttribute('url');
		 var hours = markers[i].getAttribute('hours');
		 var phone = markers[i].getAttribute('phone');
		 var image = markers[i].getAttribute('image');
         
         var marker = createMarker(point, name, address, homeAddress, description, url, hours, phone, image); // homeAddress param added by Moyo 5/23/08 **description through image added 12/2/08 by Moyo
         map.addOverlay(marker);
         var sidebarEntry = createSidebarEntry(marker, name, address, distance, homeAddress, url); // homeAddress param added by Moyo 5/23/08
         sidebar.appendChild(sidebarEntry);
         bounds.extend(point);
       }
	  map.setCenter(bounds.getCenter(), (map.getBoundsZoomLevel(bounds)-1)); //8/28/08: -1 to zoom out one step
	 });
	  
   }

    function createMarker(point, name, address, homeAddress, description, url, hours, phone, image) { // homeAddress param added by Moyo 5/23/08
	
	  markerOpts = { icon:theIcon };
      var marker = new GMarker(point, markerOpts);
	  
	  var more_html="";
	  if(url.indexOf("http://")==-1) {url="http://"+url;} //added by Moyo 10/19/2009 so that www.someurl.com will show up as http://www.someurl.com
	  if (url.indexOf("http://")!=-1 && url.indexOf(".")!=-1) {more_html+="| <a href='"+url+"' target='_blank' class='storelocatorlink'><nobr>" + sl_website_label +"</nobr></a>"} else {url=""}
	  if (image.indexOf(".")!=-1) {more_html+="<br/><img src='"+image+"' class='sl_info_bubble_main_image'>"} else {image=""}
	  if (description!="") {more_html+="<br/>"+description+"";} else {description=""}
	  if (hours!="") {more_html+="<br/><span class='location_detail_label'>Hours:</span> "+hours;} else {hours=""}
	  if (phone!="") {more_html+="<br/><span class='location_detail_label'>Phone:</span> "+phone;} else {phone=""}
	  
		var street = address.split(',')[0]; if (street.split(' ').join('')!=""){street+='<br/>';}else{street="";}
		var city = address.split(',')[1]; if (city.split(' ').join('')!=""){city+=', ';}else{city="";}
		var state_zip = address.split(',')[2]; 	  
		//address=street + city + state_zip;
	  
	  if (homeAddress.split(" ").join("")!="") {
		var html = '<div id="sl_info_bubble"><!--tr><td--><strong>' + name + '</strong><br>' + street + city + state_zip + '<br/> <a href="http://' + sl_google_map_domain + '/maps?saddr=' + encodeURIComponent(homeAddress) + '&daddr=' + encodeURIComponent(address) + '" target="_blank" class="storelocatorlink">Directions</a> ' + more_html + '<br/><!--/td></tr--></div>'; // Get Directions link added by Moyo 5/23/08
	  }
	  else {
		var html = '<div id="sl_info_bubble"><!--tr><td--><strong>' + name + '</strong><br>' + street + city + state_zip + '<br/> <a href="http://' + sl_google_map_domain + '/maps?q=' + encodeURIComponent(address) + '" target="_blank" class="storelocatorlink">Map</a> ' + more_html + '<!--/td></tr--></div>';
	  }
      GEvent.addListener(marker, 'click', function() {
        marker.openInfoWindowHtml(html);
		//t=GMap2.getInfoWindow();
		//t.reset(size:400);
      });
      return marker;
    }

	var resultsDisplayed=0;
	var bgcol="white";
	
    function createSidebarEntry(marker, name, address, distance, homeAddress, url) { // homeAddress param added by Moyo 5/23/08
	document.getElementById('map_sidebar_td').style.display='block';
      var div = document.createElement('div');
	  var street = address.split(',')[0]; 
	  var city = address.split(',')[1]; if (city.split(' ').join('')!=""){city+=', ';}else{city="";}
	  var state_zip = address.split(',')[2]; 
	  //var more = address.split(',')[3];
	  if(url.indexOf("http://")==-1) {url="http://"+url;} //added by Moyo 10/19/2009 so that www.someurl.com will show up as http://www.someurl.com
	  if (url.indexOf("http://")!=-1 && url.indexOf(".")!=-1) {link="<a href='"+url+"' target='_blank' class='storelocatorlink'><nobr>" + sl_website_label +"</nobr></a>&nbsp;|&nbsp;"} else {url=""; link="";}
	  
      var html = '<center><table width="96%" cellpadding="4px" cellspacing="0" class="searchResultsTable"><tr><td class="results_row_left_column"><span class="location_name">' + name + '</span><br>' + distance.toFixed(1) + ' ' + sl_distance_unit + '</td><td class="results_row_center_column">' + street + '<br/>' + city + state_zip +' </td><td class="results_row_right_column">' + link + '<a href="http://' + sl_google_map_domain + '/maps?saddr=' + encodeURIComponent(homeAddress) + '&daddr=' + encodeURIComponent(address) + '" target="_blank" class="storelocatorlink">Directions</a></td></tr></table></center>'; // Get Directions link added by Moyo 5/23/08
      /*if (resultsDisplayed==0) {
		div.innerHTML = "<table><tr><td>";
	  }*/
	  div.innerHTML = html;
	  div.className='results_entry';
      /*div.style.cursor = 'pointer';
      div.style.padding = '4px';
	  div.style.color = 'black'; //added by Moyo 11/2/08 10:43am
	  div.style.borderBottom = 'solid silver 1px' ; // added by Moyo 5/23/08 11:23am
	  div.style.backgroundColor = bgcol; //added 12/2/2208*/
	  resultsDisplayed++;
      GEvent.addDomListener(div, 'click', function() {
        GEvent.trigger(marker, 'click');
      }); /*
      GEvent.addDomListener(div, 'mouseover', function() {
        div.style.backgroundColor = 'salmon';
      });
      GEvent.addDomListener(div, 'mouseout', function() {
        div.style.backgroundColor = '#fff';
      });
	  if (bgcol=="white") {bgcol="#ffffff";} else {bgcol="white";}	  */
      return div;
    }
    //]]>

	//document.onload=load();
//	document.onunload=GUnload();