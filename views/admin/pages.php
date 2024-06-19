<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");
$contact_us = MiscFunction::get_function("contact-us");
$about_us = MiscFunction::get_function("about-us");
$privacy_policy = MiscFunction::get_function("privacy-policy");
$terms = MiscFunction::get_function("terms");

if (isset($_POST['edit_pages'])) {
		if(!$current_user->can_see_this("pages.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/pages']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$contact_us = MiscFunction::get_function("contact-us");
			$about_us = MiscFunction::get_function("about-us");
			$privacy_policy = MiscFunction::get_function("privacy-policy");	
			$terms = MiscFunction::get_function("terms");	
			
			$about_us_value = strip_tags($_POST['about-us'],'<a><img><b><i><p><u><ol><ul><li><iframe>');
			$privacy_policy_value = strip_tags($_POST['privacy-policy'],'<a><img><b><i><p><u><ol><ul><li><iframe>');
			$contact_us_value = strip_tags($_POST['contact-us'],'<a><img><b><i><p><u><ol><ul><li><iframe>');
			$contact_us_msg = strip_tags($_POST['contact-us-msg'],'<a><img><b><i><p><u><ol><ul><li><iframe>');
			$terms_value =  strip_tags($_POST['terms'],'<a><img><b><i><p><u><ol><ul><li><iframe>');
			
			$about_us->value = $about_us_value;
			$contact_us->msg = $contact_us_msg;
			$contact_us->value = $contact_us_value;
			$privacy_policy->value = $privacy_policy_value;
			$terms->value = $terms_value;
			
			$updated = false;
			
			if($contact_us->update()) {
				Log::log_action($current_user->id , "Update page" , "Update (Contact Us) page" );
				$updated = true;
			}
			if($about_us->update()) {
				Log::log_action($current_user->id , "Update page" , "Update (About Us) page" );
				$updated = true;
			}
			if($privacy_policy->update()) {
				Log::log_action($current_user->id , "Update page" , "Update (Privacy Policy) page" );
				$updated = true;
			}
			if($terms->update()) {
				Log::log_action($current_user->id , "Update page" , "Update (Terms & Conditions) page" );
				$updated = true;
			}
			
			if($updated) {
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/pages']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				redirect_to("{$url_mapper['admin/pages']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/pages']}?edit=fail&msg={$msg}");
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
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/reports']; ?>" class="col-md-12 "><?php echo $lang['admin-section-reports'] . ' ' . $pending_reports_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/users']; ?>" class="col-md-12"><?php echo $lang['admin-section-users']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('groups.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/groups']; ?>" class="col-md-12"><?php echo $lang['admin-section-groups']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pages.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pages']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-pages']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admintopics.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/topics']; ?>" class="col-md-12"><?php echo $lang['admin-section-topics']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admanager.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/ads']; ?>" class="col-md-12"><?php echo $lang['admin-section-admanager']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('profanity_filter.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/filter']; ?>" class="col-md-12"><?php echo $lang['admin-section-filter']; ?></a></li><?php } ?>
				
			</ul>
			
		</div></div>
	
	<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
	</div>
	
	
	<div class="col-md-9">
		<div class="card post-item"><div class="card-body">
			
			<form method="post" action="<?php echo $url_mapper['admin/pages']; ?>">
			
				<h5 class="page-header"><?php echo $lang['admin-pages-title']; ?></h5>
					<hr>
					<div class="row p-3 pt-0">
						
						<ul class="nav nav-pills pl-3 pr-3" >
						  <li class="nav-item"><a class="nav-link active"  data-toggle="tab" href="#about-us" href="javascript:void(0);"><?php echo $lang['pages-about-title']; ?></a></li>
						  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contact-us" href="javascript:void(0);" ><?php echo $lang['pages-contact-title']; ?></a></li>
						  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#privacy-policy" href="javascript:void(0);"><?php echo $lang['pages-privacy-title']; ?></a></li>
						  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#terms" href="javascript:void(0);"><?php echo $lang['pages-terms-title']; ?></a></li>
						</ul>
						<div class="tab-content col-12"  style='border-top:1px solid #b92b27' >
							<div id="about-us" class="tab-pane fade show active">
								<br>
								<textarea class="summernote" name="about-us" ><?php echo $about_us->value; ?></textarea>
								
							</div>
							<div id="contact-us" class="tab-pane fade ">
								<br>
								<div class="form-group">
									<label for="msg"><?php echo $lang['admin-pages-about-email']; ?></label>
									<input type="email" class="form-control" name="contact-us-msg" id="msg" placeholder="" value="<?php echo $contact_us->msg; ?>">
								</div>
								<hr>
								<textarea class="summernote" name="contact-us"><?php echo $contact_us->value; ?></textarea>
								
							</div>
							<div id="privacy-policy" class="tab-pane fade ">
								<br>
								<textarea class="summernote" name="privacy-policy"><?php echo $privacy_policy->value; ?></textarea>
								
							</div>
							<div id="terms" class="tab-pane fade ">
								<br>
								<textarea class="summernote" name="terms"><?php echo $terms->value; ?></textarea>
								
							</div>
							
							<br>
							<input class="btn btn-success pull-<?php echo $lang['direction-right']; ?>" type="submit" name="edit_pages" value="<?php echo $lang['btn-submit']; ?>">
							<br>
						</div>
						
					</div>
					
							
						
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
<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-bs4.js"></script>
<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-emoji/summernote-emoji.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.summernote').summernote({
			height: 400,
			callbacks : {
	            onImageUpload: function(image) {
					sendFile($(this), image[0]);
				}
			}
        });
		
		function sendFile(obj, image) {
            data = new FormData();
            data.append("data", 'summernote-inline-uploader');
            data.append("id", <?php echo $current_user->id; ?>);
            data.append("hash", '<?php echo $random_hash; ?>');
            data.append("img", image);
            $.ajax({
                data: data,
                type: "POST",
                url: "<?php echo WEB_LINK ?>public/includes/one_ajax.php?type=upl_img",
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                    obj.summernote("insertImage", url);
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>