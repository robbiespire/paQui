<div id="receipt" class="ecart">

<div class="one_half">
<table class="transaction">
	<tr><th><?php _e('Your Order ID:','Ecart'); ?></th><td><?php ecart('purchase','id'); ?></td></tr>
	<tr><th><?php _e('Order Date:','Ecart'); ?></th><td><?php ecart('purchase','date'); ?></td></tr>
	<tr><th><?php _e('Order Billed To: ','Ecart'); ?></th><td><?php ecart('purchase','card'); ?> (<?php ecart('purchase','cardtype'); ?>)</td></tr>
	<tr><th><?php _e('Transaction Info: ','Ecart'); ?></th><td><?php ecart('purchase','transactionid'); ?> (<strong><?php ecart('purchase','payment'); ?></strong>)</td></tr>
</table>
</div>

<div class="one_half last" id="shopping-cart-subtotal-history">

	<div class="cart-subtotal">
		
		<span id="subtotal-heading"><?php _e('Order Summary','Ecart'); ?></span>
			
		<div class="inner-form" id="total-form">
			
			<p><?php _e('Order Subtotal','Ecart'); ?> <span><?php ecart('purchase','subtotal'); ?></span></p>
			
			<?php if (ecart('purchase','hasdiscount')): ?>
			<p><?php _e('Discount','Ecart'); ?> - <span><?php ecart('purchase','discount'); ?></span></p>
			<?php endif; ?>	
			
			<?php if (ecart('purchase','hasfreight')): ?>
			<p><?php _e('Shipping','Ecart'); ?> <span><?php ecart('purchase','freight'); ?></span></p>
			<?php endif; ?>
			
			<?php if (ecart('purchase','hastax')): ?>
			<p><?php _e('Tax','Ecart'); ?> <span><?php ecart('purchase','tax'); ?></span></p>
			<?php endif; ?>
			
			<p><?php _e('Total','Ecart'); ?> <span><?php ecart('purchase','total'); ?></span></p>
			
		</div>
		
	</div>

</div>

<div class="clearfix"></div>

<div class="one_half" id="shopping-cart-billed">

	<div class="cart-billed">
		
		<span id="shipping-heading"><?php _e('Order Billing Address','Ecart'); ?></span>
			
		<div class="inner-form" id="billing-form">
			
			<p><?php ecart('purchase','firstname'); ?> <?php ecart('purchase','lastname'); ?></p>
			<p><?php ecart('purchase','company'); ?></p>
			<p><?php ecart('purchase','address'); ?></p>
			<p><?php ecart('purchase','xaddress'); ?></p>
			<p><?php ecart('purchase','city'); ?>, <?php ecart('purchase','state'); ?> <?php ecart('purchase','postcode'); ?></p>
			<p><?php ecart('purchase','country'); ?></p>
			
		</div>
		
	</div>
	

</div>

<div class="one_half last">
<div id="shopping-cart-sent">
	
	<div class="cart-billed">
		
		<span id="shipped-heading"><?php _e('Order Billing Address','Ecart'); ?></span>
		
		<div class="inner-form">
			<p><?php ecart('purchase','firstname'); ?> <?php ecart('purchase','lastname'); ?></p>
			<p><?php ecart('purchase','shipaddress'); ?></p>
			<p><?php ecart('purchase','shipxaddress'); ?></p>
			<p><?php ecart('purchase','shipcity'); ?>, <?php ecart('purchase','shipstate'); ?> <?php ecart('purchase','shippostcode'); ?></p>
			<p><?php ecart('purchase','shipcountry'); ?></p>
			
			<strong><p><?php _e('Shipping:','Ecart'); ?> <?php ecart('purchase','shipmethod'); ?></p></strong>
		</div>
		
	</div>

</div>
</div>

<div class="clearfix"></div>
<div id="shopping-cart-wrapper">
<?php if (ecart('purchase','hasitems')): ?>
<table class="productcart" id="shopping-table">
	<tr class="firstrow">
		<th scope="col" class="firstcol"><?php _e('Items Ordered','Ecart'); ?></th>
		<th scope="col"><?php _e('Quantity','Ecart'); ?></th>
		<th scope="col" class="money"><?php _e('Item Price','Ecart'); ?></th>
		<th scope="col" class="money"><?php _e('Item Total','Ecart'); ?></th>
	</tr>

	<?php while(ecart('purchase','items')): ?>
		<tr class="product_row">
			<td class="item-column"><?php ecart('purchase','item-name'); ?><?php ecart('purchase','item-options','before= – '); ?><br />
				<?php ecart('purchase','item-download'); ?>
				<?php ecart('purchase','item-addons-list'); ?>
				</td>
			<td class="right-row"><?php ecart('purchase','item-quantity'); ?></td>
			<td class="money right-row"><?php ecart('purchase','item-unitprice'); ?></td>
			<td class="money right-row"><?php ecart('purchase','item-total'); ?></td>
		</tr>
	<?php endwhile; ?>
	
</table>
</div>
<?php if(ecart('purchase','has-data')): ?>
	<ul>
	<?php while(ecart('purchase','orderdata')): ?>
		<?php if (ecart('purchase','data','echo=0') == '') continue; ?>
		<li><strong><?php ecart('purchase','data','name'); ?>:</strong> <?php ecart('purchase','data'); ?></li>
	<?php endwhile; ?>
	</ul>
<?php endif; ?>
	

<?php else: ?>
	<p class="warning"><?php _e('There were no items found for this purchase.','Ecart'); ?></p>
<?php endif; ?>
</div>
