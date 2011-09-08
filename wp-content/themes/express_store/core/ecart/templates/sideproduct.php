<?php
/**
 ** WARNING! DO NOT EDIT!
 **
 ** UNLESS YOU KNOW WHAT YOU ARE DOING
 **
 **/
?>
<?php if (ecart('product','found')): ?>
	<div class="sideproduct">
	<a href="<?php ecart('product','url'); ?>"><?php ecart('product','thumbnail'); ?></a>

	<h3><a href="<?php ecart('product','url'); ?>"><?php ecart('product','name'); ?></a></h3>

	<?php if (ecart('product','onsale')): ?>
		<p class="original price"><?php ecart('product','price'); ?></p>
		<p class="sale price"><big><?php ecart('product','saleprice'); ?></big></p>
	<?php else: ?>
		<p class="price"><big><?php ecart('product','price'); ?></big></p>
	<?php endif; ?>
	</div>
<?php endif; ?>
