<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

if(isset($_GET['notif']) && is_numeric($_GET['notif'])) {
	$notification = Notif::get_specific_id($db->escape_value($_GET['notif']));
	if($notification && $notification->user_id == $current_user->id) {
		$notification->read();
	}
}
$curpage = MiscFunction::get_function("terms");
$title = $lang['pages-terms-title'];

require_once(VIEW_PATH.'pages/header.php');
require_once(VIEW_PATH.'pages/navbar.php');

?>
<div class="container">		

<div class="row">
	
	<div class="col-md-9">
		<div class="card post-item"><div class="card-body">
			<?php 
				echo "<h5 class='page-header'>{$title}</h5><hr>";
				
				$content = str_replace('\\','', $curpage->value);
				$content = str_replace('<script','', $content);
				$content = str_replace('</script>','', $content);
				echo $content;
			?>
		
		</div></div>
	</div>
	<?php require_once(VIEW_PATH.'pages/rt_sidebar.php'); ?>
</div>
    </div> <!-- /container -->
    <?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
	<?php require_once(VIEW_PATH.'pages/footer.php'); ?>
	
	<script>
    $(document).ready(function() {
        
	if(window.location.hash) {
	  scrollToId(window.location.hash);
	}
	
	$(document).ready(function(){
		$("img").addClass("img-fluid");
	});
	</script>
	
<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>