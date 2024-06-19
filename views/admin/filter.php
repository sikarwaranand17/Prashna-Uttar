<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");
$profanity_filter = MiscFunction::get_function("profanity_filter");

if (isset($_POST['edit_profanity_filter'])) {
		if(!$current_user->can_see_this("profanity_filter.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/filter']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$profanity_filter_value = $db->escape_value(strip_tags($_POST['filter']));
			
			$profanity_filter->value = $profanity_filter_value;
			
			if ($profanity_filter->update()) {
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/filter']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				redirect_to("{$url_mapper['admin/filter']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/filter']}?edit=fail&msg={$msg}");
		}
}

require_once(VIEW_PATH.'pages/navbar.php');
$pending_posts_badge = '';
$pending_reports_badge = '';
if($current_user->can_see_this('pending.read' , $group)) {
	$pending_q = Question::count_pending();
	$pending_a = Answer::count_pending();
	$pending_posts = $pending_q + $pending_a;
	$pending_posts_badge = '';
	if($pending_posts) {
		$pending_posts_badge = "&nbsp;&nbsp;<span class='badge badge-secondary'>{$pending_posts}</span>";
	}
	
	$pending_reports = Report::count_pending();
	$pending_reports_badge = '';
	if($pending_reports) {
		$pending_reports_badge = "&nbsp;&nbsp;<span class='badge badge-secondary'>{$pending_reports}</span>";
	}
	
}
?>

<div class="container">		

<div class="row">
	
	<div class="col-md-3 ">
		<div class="card post-item"><div class="card-body">
			<h5><i class="fe fe-settings"></i>&nbsp;&nbsp;<?php echo $lang['admin-title']; ?></h5>
			<hr>
			<ul class="nav-ul">
				<?php if($current_user->can_see_this('dashboard.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/']; ?>" class="col-md-12 "><?php echo $lang['admin-section-dashboard']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('general_settings.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/settings']; ?>" class="col-md-12 "><?php echo $lang['admin-section-general']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pending']; ?>" class="col-md-12 "><?php echo $lang['admin-section-pending'] . ' ' . $pending_posts_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/reports']; ?>" class="col-md-12"><?php echo $lang['admin-section-reports'] . ' ' . $pending_reports_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/users']; ?>" class="col-md-12"><?php echo $lang['admin-section-users']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('groups.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/groups']; ?>" class="col-md-12"><?php echo $lang['admin-section-groups']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pages.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pages']; ?>" class="col-md-12"><?php echo $lang['admin-section-pages']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admintopics.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/topics']; ?>" class="col-md-12"><?php echo $lang['admin-section-topics']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admanager.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/ads']; ?>" class="col-md-12"><?php echo $lang['admin-section-admanager']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('profanity_filter.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/filter']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-filter']; ?></a></li><?php } ?>
				
			</ul>
			
		</div></div>
	
	<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
	</div>
	
	
	<div class="col-md-9">
		<div class="card post-item"><div class="card-body">
		<form method="post" action="<?php echo $url_mapper['admin/filter']; ?>">
				
				<h5 class="page-header"><?php echo $lang['admin-filter-title']; ?></h5>
				<hr>
					<div class="row">
						<textarea name="filter" class="form-control" rows="15"><?php echo $profanity_filter->value; ?></textarea>
					</div>
					<br>
							
								<input class="btn btn-success pull-<?php echo $lang['direction-right']; ?>" type="submit" name="edit_profanity_filter" value="<?php echo $lang['btn-submit']; ?>">
							
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
		</div></div>
	</div>
</div>
</div>
<?php require_once(VIEW_PATH.'pages/preloader.php'); ?>

<script type="text/javascript">
	$(document).ready(function() {
		
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>