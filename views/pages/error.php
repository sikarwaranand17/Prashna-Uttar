<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

$title="404 Page Not Found";
require_once(VIEW_PATH.'pages/header.php'); ?>
<?php require_once(VIEW_PATH.'pages/navbar.php'); ?>

<div class="container">		

<div class="row">
	<?php require_once(VIEW_PATH.'pages/lt_sidebar.php'); ?>
	<div class="col-md-7">
		
		<br><br><br>
		<center><img src="<?php echo WEB_LINK; ?>public/img/404.png" ><br>
		<h2>Page Not Found!</h2><hr>
		May be you'll find what you're looking for here:<br>
		
		<form class="has-search p-0 my-2" style="width:100%">
			<span class="fa fa-search form-control-feedback"></span>
			<input class="form-control searchbox-field typeahead" data-provide="typeahead" type="search" placeholder="Search" aria-label="Search" >
		</form>
		
		</center>
		
	</div>
	
	<?php require_once(VIEW_PATH.'pages/rt_sidebar.php') ?>
	
</div>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>
</div> <!-- /container -->
<?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>