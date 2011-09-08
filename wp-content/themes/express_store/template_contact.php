<?php
/*
Template Name: Contact Page
*/
get_header();
?>
<div id="breadcumb">

	<div class="container">
		<?php if (function_exists('dimox_breadcrumbs')) dimox_breadcrumbs();?>
	</div>

</div>

<div id="inner-page">

	<div class="container" id="inner-wrapper">
	
		<?php if (get_post_meta($post->ID, 'sidebar_position', true ) =='left') { ?>
		
			<div class="one_fourth" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
			<div class="three_fourth last" id="page-content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php the_content();?>
			
			<?php endwhile; endif; ?>
			
			<div class="contact-area">
			
			        <?php $success_msg  = epanel_option('contact_ok');?>
			
			        <div class="success-contact"><?php echo ($success_msg) ? $success_msg : "Your message has been sent successfully. Thank you!";?></div>                                
			
			        <div id="contactFormArea">                                  
			            <form action="#" id="contactform"> 
			                <fieldset>
			                    <label>Full Name</label>
			                    <input type="text" name="name" class="textfield" id="name" value="" />
			
			                    <label>Email Address</label>
			                    <input type="text" name="email" class="textfield" id="email" value="" />
			
			                    <label>Subject</label>
			                    <input type="text" name="subject" class="textfield" id="subject" value="" />
				
				                    <div class="contact-column-right">
				                        <label>Message</label>
				                        <textarea name="message" id="message" class="textarea" cols="50" rows="10"></textarea>
				
				                        <label>&nbsp;</label>
				
				                        <button type="submit" name="submit" id="buttonsend" class="submit buttoncontact">Send Message</button>
				                        <input type="hidden" name="siteurl" id="siteurl" value="<?php bloginfo('template_directory');?>" />
				                        <span class="loading" style="display: none;"><label>Sending message...</label></span>
				                    </div>
				              </fieldset> 
				         </form>    
				      </div>
				     </div>
				     <!-- end of contact-area -->
			
			</div>
		
		<?php } else if (get_post_meta($post->ID, 'sidebar_position', true ) =='right') { ?>
		
			<div class="three_fourth" id="page-content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<?php the_content();?>
			
			<?php endwhile; endif; ?>
			
			<div class="contact-area">
			
			        <?php $success_msg  = get_option('ep_success_msg');?>
			
			        <div class="success-contact"><?php echo ($success_msg) ? $success_msg : "Your message has been sent successfully. Thank you!";?></div>                                
			
			        <div id="contactFormArea">                                  
			            <form action="#" id="contactform"> 
			                <fieldset>
			                    <label>Full Name</label>
			                    <input type="text" name="name" class="textfield" id="name" value="" />
			
			                    <label>Email Address</label>
			                    <input type="text" name="email" class="textfield" id="email" value="" />
			
			                    <label>Subject</label>
			                    <input type="text" name="subject" class="textfield" id="subject" value="" />
				
				                    <div class="contact-column-right">
				                        <label>Message</label>
				                        <textarea name="message" id="message" class="textarea" cols="50" rows="10"></textarea>
				
				                        <label>&nbsp;</label>
				
				                        <button type="submit" name="submit" id="buttonsend" class="submit buttoncontact">Send Message</button>
				                        <input type="hidden" name="siteurl" id="siteurl" value="<?php bloginfo('template_directory');?>" />
				                        <span class="loading" style="display: none;"><label>Sending message...</label></span>
				                    </div>
				              </fieldset> 
				         </form>    
				      </div>
				     </div>
				     <!-- end of contact-area -->
			</div>
			
			<div class="one_fourth last" id="sidebar">
				<?php if ( !function_exists('dynamic_sidebar') || ! generated_dynamic_sidebar('Page Sidebar') ) ; ?>
			</div>
			
		<?php } ?>
		
		
		<div class="clearfix"></div>
		
	</div>

</div>

<?php get_footer();?>