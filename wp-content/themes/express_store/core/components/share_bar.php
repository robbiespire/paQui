<ul id="share-bar">
		 			
	<?php
				 		
    $li = get_permalink($post->post_ID);   
      
     $link =  getLink("http://twitter.com/home",array("status"=> $li, "status"=> $li));
    if(get_option('ep_blogpost_share_twitter') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="twitter" src="'.get_bloginfo('template_url').'/images/social/big/twitter.png" /></a></li>';
    
        $link =  getLink("http://www.facebook.com/share.php",array( "u"=> $li, "t"=>$post->post_title));
    if(get_option('ep_blogpost_share_facebook') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="facebook" src="'.get_bloginfo('template_url').'/images/social/big/facebook.png" /></a></li>';
   
    $link =  getLink("mailto:",array( "body"=> $li, "subject"=>$post->post_title));
    if(get_option('ep_blogpost_share_email') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="mailto" src="'.get_bloginfo('template_url').'/images/social/big/email.png" /></a></li>'; 
     
    
    $link =  getLink("http://delicious.com/post",array( "url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_delicious') == "true") echo '<li><a rel="nofollow" href="'.$link.'"><img alt="delicious" src="'.get_bloginfo('template_url').'/images/social/big/delicious.png" /></a></li>';
    

    $link =  getLink("http://www.mixx.com/submit",array( "page_url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_mixx') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="mixx" src="'.get_bloginfo('template_url').'/images/social/big/mixx.png" /></a></li>';
    
    $link =  getLink("http://www.google.com/bookmarks/mark",array( "op"=> "edit", "bkmk"=> $li, "title"=>$post->post_title,  "annotation"=>$post_object->post_content));
    if(get_option('ep_blogpost_share_google') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="google" src="'.get_bloginfo('template_url').'/images/social/big/google.png" /></a></li>';
    
    $link =  getLink("http://www.designfloat.com/submit.php",array( "url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_designfloat') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="designfloat" src="'.get_bloginfo('template_url').'/images/social/big/designfloat.png" /></a></li>'; 

    $link =  getLink("http://www.friendfeed.com/share",array( "link"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_friendfeed') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="friendfeed" src="'.get_bloginfo('template_url').'/images/social/big/friendfeed.png" /></a></li>';   
    
    $link =  getLink("http://digg.com/submit",array( "url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_digg') =="true")  echo '<li><a rel="nofollow" href="'.$link.'"><img alt="digg" src="'.get_bloginfo('template_url').'/images/social/big/digg.png" /></a></li>';
    
    $link =  getLink("http://www.linkedin.com/shareArticle",array("mini"=> "true", "url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_linkedin') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="linkedin" src="'.get_bloginfo('template_url').'/images/social/big/linkedin.png" /></a></li>';               
    
    $link =  getLink("https://favorites.live.com/quickadd.aspx",array("marklet"=> "1", "url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_microsoft') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="microsoft" src="'.get_bloginfo('template_url').'/images/social/big/microsoft.png" /></a></li>';  
    
    $link =  getLink("http://reporter.nl.msn.com/",array("fn"=> "contribute", "URL"=> $li, "Title"=>$post->post_title));
    if(get_option('ep_blogpost_share_msn') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="msn" src="'.get_bloginfo('template_url').'/images/social/big/msn.png" /></a></li>';     
    
    $link =  getLink("http://www.myspace.com/Modules/PostTo/Pages/",array("u"=> $li, "t"=>$post->post_title));
    if(get_option('ep_blogpost_share_myspace') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="myspace" src="'.get_bloginfo('template_url').'/images/social/big/myspace.png" /></a></li>';  
    
    $link =  getLink("http://www.netvibes.com/share",array("url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_netvibes') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="netvibes" src="'.get_bloginfo('template_url').'/images/social/big/netvibes.png" /></a></li>';
    
    $link =  getLink("http://www.newsvine.com/_tools/seed&amp;save",array("u"=> $li, "h"=>$post->post_title));
    if(get_option('ep_blogpost_share_newsvine') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="newsvine" src="'.get_bloginfo('template_url').'/images/social/big/newsvine.png" /></a></i>';
    
    $link =  getLink("http://posterous.com/share",array("linkto"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_posterous') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="posterous" src="'.get_bloginfo('template_url').'/images/social/big/posterous.png" /></a></li>';  

    $link =  getLink("http://reddit.com/submit",array("url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_reddit') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="reddit" src="'.get_bloginfo('template_url').'/images/social/big/reddit.png" /></a></li>';    
    
    $link = get_bloginfo('url')."/feed/";
    if(get_option('ep_blogpost_share_rss') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="rss" src="'.get_bloginfo('template_url').'/images/social/big/rss.png" /></a></li>';     
    
    $link =  getLink("http://www.stumbleupon.com/submit",array("url"=> $li, "title"=>$post->post_title));
    if(get_option('ep_blogpost_share_stumbleupon') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="stumbleupon" src="'.get_bloginfo('template_url').'/images/social/big/stumbleupon.png" /></a></li>';  
    
    $link =  getLink("http://technorati.com/faves",array("add"=> $li));
    if(get_option('ep_blogpost_share_technorati') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="technorati" src="'.get_bloginfo('template_url').'/images/social/big/technorati.png" /></a></li>'; 
    
    $link =  getLink("http://www.tumblr.com/share",array("u"=> $li, "t"=>$post->post_title));
    if(get_option('ep_blogpost_share_tumblr') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="tumblr" src="'.get_bloginfo('template_url').'/images/social/big/tumblr.png" /></a></li>';
    
   
    $link =  getLink("http://buzz.yahoo.com/submit/",array("submitUrl"=> $li, "submitHeadline"=>$post->post_title));
    if(get_option('ep_blogpost_share_yahoo') =="true")echo '<li><a rel="nofollow" href="'.$link.'"><img alt="yahoo" src="'.get_bloginfo('template_url').'/images/social/big/yahoo-buzz.png" /></a></li>';           
                 
     ?>
</ul>