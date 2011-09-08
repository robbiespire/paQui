
<div id="customer-registration" class="sreg-format_text-container">
    <?php if (isset($form_error)) { ?>
        <p class="sreg-form-error"><?php echo $form_error; ?></p>
    <?php } ?>
    <?php if (isset($form_success)) { ?>
        <p class="sreg-form-success"><?php echo $form_success; ?></p>
    <?php } ?>
    <?php if ($show_form) { ?>
        <form method="post" action="<?php the_permalink(); ?>" id="sreg-form" class="sreg-form">
            <fieldset>
                <h5><?php _e('Account Informations','Ecart');?></h5>
                
                <div class="one_half">
                
                <?php if ($ecart_account_type == 'wordpress') : ?>
                <span>
                    <label for="sreg-username">Username</label>
                    <input type="text" name="customer[loginname]" id="sreg-username" value="<?php echo (isset($_POST['customer']['loginname'])) ? $_POST['customer']['loginname'] : ''; ?>" />
                </span>            
                <?php endif; ?>
                <span>
                    <label for="sreg-email">Email</label>
                    <input type="text" name="customer[email]" id="sreg-email" value="<?php echo (isset($_POST['customer']['email'])) ? $_POST['customer']['email'] : ''; ?>" />
                </span>
                <span>
                    <label for="sreg-password">Password</label>
                    <input type="password" name="customer[password]" id="sreg-password" value="" />
                </span>
                <span>
                    <label for="sreg-confirm-password">Confirm Password</label>
                    <input type="password" name="customer[confirm-password]" id="sreg-confirm-password" value="" />
                </span>
                
                </div>
                
                <div class="one_half last">
                            
                <span>
                    <label for="sreg-firstname">Firstname</label>
                    <input type="text" name="customer[firstname]" id="sreg-firstname" value="<?php echo (isset($_POST['customer']['firstname'])) ? $_POST['customer']['firstname'] : ''; ?>" />
                </span>
                <span>
                    <label for="sreg-lastname">Lastname</label>
                    <input type="text" name="customer[lastname]" id="sreg-lastname" value="<?php echo (isset($_POST['customer']['lastname'])) ? $_POST['customer']['lastname'] : ''; ?>" />
                </span>
                <span>
                    <label for="sreg-phone">Phone</label>
                    <input type="text" name="customer[phone]" id="sreg-phone" value="<?php echo (isset($_POST['customer']['phone'])) ? $_POST['customer']['phone'] : ''; ?>" />
                </span> 
                <span>
                    <label for="sreg-company">Company</label>
                    <input type="text" name="customer[company]" id="sreg-company" value="<?php echo (isset($_POST['customer']['company'])) ? $_POST['customer']['company'] : ''; ?>" />
                </span> 
                
                </div>
                
                <div class="clearfix"></div>
                
                
                <span style="display:none">
                    <label style="display:none" for="sreg-marketing">Yes, I would like to receive e-mail updates and special offers!</label>
                    <input style="display:none" type="checkbox" name="customer[marketing]" id="sreg-marketing" value="yes" <?php echo (isset($_POST['customer']['marketing']) && $_POST['customer']['marketing'] == 'yes') ? 'checked="checked"' : ''; ?> />
                </span> 
                
            </fieldset>
            
            <fieldset>
                <h5><?php _e('Account Billing Address Informations','Ecart'); ?></h5>
                
                <div class="one_half">
                
                <span>
                    <label for="sreg-billing-address">Street Address</label>
                    <input type="text" name="billing[address]" id="sreg-billing-address" value="<?php echo (isset($_POST['billing']['address'])) ? $_POST['billing']['address'] : ''; ?>" />
                </span>
                <span>
                    <label for="sreg-billing-xaddress">Address Line 2</label>
                    <input type="text" name="billing[xaddress]" id="sreg-billing-xaddress" value="<?php echo (isset($_POST['billing']['xaddress'])) ? $_POST['billing']['xaddress'] : ''; ?>" />
                </span>
                <span>
                    <label for="sreg-billing-city">City</label>
                    <input type="text" name="billing[city]" id="sreg-billing-city" value="<?php echo (isset($_POST['billing']['city'])) ? $_POST['billing']['city'] : ''; ?>" />
                </span>
                
                </div>
                
                <div class="one_half last">
                
                <span>
                    <label for="sreg-billing-state">State / Province</label>
                    <input type="text" name="billing[state]" id="sreg-billing-state" value="<?php echo (isset($_POST['billing']['state'])) ? $_POST['billing']['state'] : ''; ?>" />
                </span>
                <span>
                    <label for="sreg-billing-postcode">Postal / Zip Code</label>
                    <input type="text" name="billing[postcode]" id="sreg-billing-postcode" value="<?php echo (isset($_POST['billing']['postcode'])) ? $_POST['billing']['postcode'] : ''; ?>" />
                </span>                
                <span>
                    <label for="sreg-billing-country">Country</label>
                    <select name="billing[country]" id="sreg-billing-country">
                    <?php echo $countries_select_html; ?>
                    </select>
                </span>
            	</div>
            	<div class="clearfix"></div>
            
            <span>
                <input type="submit" value="<?php _e('Register Now','Ecart');?>" />
                <input type="hidden" name="customer[type]" value="Retail" />
            </span>
        </form>
    <?php } ?>
</div>