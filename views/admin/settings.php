<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");

if (isset($_POST['update_settings'])) {
		if(!$current_user->can_see_this("general_settings.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/settings']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			$site_name= $db->escape_value(htmlspecialchars($_POST["site_name"],ENT_QUOTES));
			if(isset($_POST["change_logo"]) && $_POST["change_logo"] == "1" ) {
				$change_logo = true;
			} else {
				$change_logo = false;
			}
			
			if(isset($_POST["delete_logo"]) && $_POST["delete_logo"] == "1" ) {
				$delete_logo = true;
			} else {
				$delete_logo = false;
			}
			
			$site_description= $db->escape_value(htmlspecialchars($_POST["site_description"], ENT_QUOTES));
			$site_keywords= $db->escape_value($_POST["site_keywords"]);
			$site_status = $db->escape_value($_POST["site_status"]);
			$site_lang = $db->escape_value($_POST["site_lang"]);
			$closure_msg = $db->escape_value(htmlspecialchars($_POST["closure_msg"],ENT_QUOTES));
			$url_type = $db->escape_value($_POST["url_type"]);
			$q_approval = $db->escape_value($_POST["q_approval"]);
			$a_approval = $db->escape_value($_POST["a_approval"]);
			$spaces_classifications = $db->escape_value($_POST["spaces_classifications"]);
			$reg_group = $db->escape_value($_POST["reg_group"]);
			$social = $db->escape_value($_POST["social"]);
			$public_access = $db->escape_value($_POST["public_access"]);
			
			if(isset($settings['site_logo'])) { 
				$site_logo = $settings['site_logo'];
			} else {
				$site_logo = 0;
			}
			
			
			$upload_problems = 0;
			if(!empty($_FILES["site_logo"]) && $change_logo ) {
				
				$files = '';
				$f = 0;
				$num_pics = 1;
				$target = $_FILES['site_logo'];
				$upload_problems = 0;
				for ($f ; $f < $num_pics ; $f++) :
					$file = "file";
					$string = $$file . "{$f}";
					$$string = new File();	
						if(!empty($_FILES['site_logo']['name'][$f])) {
							$$string->attach_file($_FILES['site_logo'], $f);
							if ($$string->save()) {
								$site_logo = $$string->id;
							} else {
								$upl_msg = "{$lang['alert-upload_error']}:<br>";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
			}
			
			if($delete_logo) {
				$site_logo = 0;
			}
			
			$settings_arr = Array(
									"site_name" => $site_name,
									"site_logo" => $site_logo,
									"site_description" => $site_description,
									"site_keywords" => $site_keywords,
									"site_status" => $site_status,
									"site_lang" => $site_lang,
									"closure_msg" => $closure_msg,
									"url_type" => $url_type,
									"q_approval" => $q_approval,
									"a_approval" => $a_approval,
									"spaces_classifications" => $spaces_classifications,
									"reg_group" => $reg_group,
									"social" => $social,
									"public_access" => $public_access
								);
			$general_settings->value = serialize($settings_arr);
			
			if ($general_settings->update() ) {
				$msg = $lang['alert-update_success'];
				if($upload_problems == "1") {
					$msg .= '<hr>' . $upl_msg;
				}
				redirect_to("{$url_mapper['admin/settings']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if($upload_problems == "1") {
					$msg .= '<hr>' . $upl_msg;
				}
				redirect_to("{$url_mapper['admin/settings']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/settings']}?edit=fail&msg={$msg}");
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
				<?php if($current_user->can_see_this('general_settings.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/settings']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-general']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pending']; ?>" class="col-md-12"><?php echo $lang['admin-section-pending'] . ' ' . $pending_posts_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/reports']; ?>" class="col-md-12"><?php echo $lang['admin-section-reports'] . ' ' . $pending_reports_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/users']; ?>" class="col-md-12"><?php echo $lang['admin-section-users']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('groups.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/groups']; ?>" class="col-md-12"><?php echo $lang['admin-section-groups']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pages.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pages']; ?>" class="col-md-12"><?php echo $lang['admin-section-pages']; ?></a></li><?php } ?>
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
			<form method="post" action="<?php echo $url_mapper['admin/settings']; ?>" enctype="multipart/form-data">
			
				<h5 class="page-header"><?php echo $lang['admin-general-title']; ?></h5>
				<hr>
					<div class="row">
						<div class="col-md-6">
							<h5><center><?php echo $lang['admin-general-site-title']; ?></center></h5><br>
							<div class="form-group">
								<label for="site_name"><?php echo $lang['admin-general-site-name']; ?></label>
								<input type="text" class="form-control" name="site_name" id="site_name" value="<?php echo $settings['site_name']; ?>" placeholder="Site Name.." required>
								<br>
								
								<label for="site_logo"><?php echo $lang['admin-general-site-logo']; ?></label>
									<?php 
									if(!empty($settings['site_logo'])) {
										$file = File::get_specific_id($settings['site_logo']);
									?>
										<img src="<?php echo WEB_LINK."public/".$file->image_path(); ?>" class='img-fluid'/><br><div class='col-xs-6'><input type='checkbox' name='change_logo' value= '1' > Change Logo</div><div class='col-xs-6'><input type='checkbox' name='delete_logo' value= '1' > Delete Logo</div><br><br>
									<?php
										} else {
												echo "<input type=\"hidden\" name=\"change_logo\" value=\"1\" readonly/>";	
										}
									?>
									<input class="text-input " type="file" name="site_logo[]" id="img1_upl"/>
									<small class="text-muted">Max. Width: 140px, Max. Height: 30px</small><br/>
								<br>
								
								<label for="site_description"><?php echo $lang['admin-general-site-description']; ?></label>
								<input type="text" class="form-control" name="site_description" id="site_description" value="<?php echo $settings['site_description']; ?>" placeholder="Site Description.." required>
								<br>
								
								<label for="site_keywords"><?php echo $lang['admin-general-site-keywords']; ?></label>
								<input type="text" class="form-control tagsinput" name="site_keywords" id="site_keywords" value="<?php echo $settings['site_keywords']; ?>" placeholder="Keywords.." required style="width: 100%">
								
								<br><br>
								
								<?php $langs = scandir(LIBRARY_PATH.'/lang/'); unset($langs[0]); unset($langs[1]); unset($langs[2]); ?>
								
								<label for="site_lang"><?php echo $lang['admin-general-site-lang']; ?></label>&nbsp;&nbsp;
								<select id="site_lang" name="site_lang" class="form-control select2" style="width:200px" data-placeholder="Site Language..">
								<?php  foreach($langs as $l) {
									$l = explode('.' , $l);
									echo "<option value='{$l[1]}' ";
										if($l[1] == $settings['site_lang']) { echo ' selected'; }
									echo " >{$l[1]}</option>";
								} ?>	
								</select><br>
								
								<label for="site_status"><?php echo $lang['admin-general-site-status']; ?></label>&nbsp;&nbsp;
								<select id="site_status" name="site_status" class="form-control select2" style="width:200px" data-placeholder="Site Status..">
									<option value="1" <?php if($settings['site_status']== '1') { echo ' selected'; } ?>>Active</option>
									<option value="0" <?php if($settings['site_status']== '0') { echo ' selected'; } ?>>Closed</option>
								</select><br>
								<label for="closure_msg"><?php echo $lang['admin-general-site-status_msg']; ?></label>
								<input type="text" class="form-control" name="closure_msg" id="closure_msg" value="<?php echo $settings['closure_msg']; ?>" placeholder="Site Closed Message..">
							</div>
							
						</div>
						<div class="col-md-6"><h5><center><?php echo $lang['admin-general-url-title']; ?></center></h5><br>
							<div class="form-group">
								<label for="url_type"><?php echo $lang['admin-general-url-type']; ?></label>&nbsp;&nbsp;
								<select id="url_type" name="url_type" class="form-control select2" style="width:200px" data-placeholder="URL Type.."> 
									<option value="slug" <?php if($settings['url_type']== 'slug') { echo ' selected'; } ?>>By Subject/Slug</option>
									<option value="id" <?php if($settings['url_type']== 'id') { echo ' selected'; } ?>>By ID</option>
								</select>
							</div><hr>
							<h5><center><?php echo $lang['admin-general-posting-title']; ?></center></h5><br>
							<div class="form-group">
															
								<label for="q_approval"><?php echo $lang['admin-general-posting-questions']; ?></label>&nbsp;&nbsp;
								<select id="q_approval" name="q_approval" class="form-control select2" style="width:200px"> 
									<option value="0" <?php if($settings['q_approval']== '0') { echo ' selected'; } ?> >Immediately</option>
									<option value="1" <?php if($settings['q_approval']== '1') { echo ' selected'; } ?>>After Admin Approval</option>
								</select><br>
								
								<label for="a_approval"><?php echo $lang['admin-general-posting-answers']; ?></label>&nbsp;&nbsp;
								<select id="a_approval" name="a_approval" class="form-control select2" style="width:200px"> 
									<option value="0" <?php if($settings['a_approval']== '0') { echo ' selected'; } ?>>Immediately</option>
									<option value="1" <?php if($settings['a_approval']== '1') { echo ' selected'; } ?> >After Admin Approval</option>
								</select><br>
								
								<label for="spaces_classifications"><?php echo $lang['admin-general-posting-spaces_classifications']; ?></label>&nbsp;&nbsp;
								<input type="text" class="form-control tagsinput" name="spaces_classifications" id="spaces_classifications" value="<?php if(isset($settings['spaces_classifications'])) { echo $settings['spaces_classifications']; } ?>" placeholder="Classifications.." required>
							</div>
							<hr>
							<h5><center><?php echo $lang['admin-general-access-title']; ?></center></h5><br>
							<div class="form-group">
								<label for="public_access"><?php echo $lang['admin-general-access-login']; ?></label>&nbsp;&nbsp;
								<select id="public_access" name="public_access" class="form-control select2" style="width:200px"> 
									<option value="0" <?php if($settings['public_access']== '0') { echo ' selected'; } ?>>Disabled</option>
									<option value="1" <?php if($settings['public_access']== '1') { echo ' selected'; } ?> >Enabled</option>
								</select>
							</div>
							<hr>
							<h5><center><?php echo $lang['admin-general-reg-title']; ?></center></h5><br>
							<div class="form-group">
								<label for="reg_group"><?php echo $lang['admin-general-reg-group']; ?></label>&nbsp;&nbsp;
								<select id="reg_group" name="reg_group" class="form-control select2" style="width:200px"> 
									<?php 
										$groups = Group::get_users(); 
										foreach($groups as $g) {
											echo "<option value='{$g->id}' ";
												if($g->id == $settings['reg_group']) { echo ' selected'; }
											echo " >{$g->name}</option>";
										}
									?>
								</select>
							</div><div class="form-group">
								<label for="social"><?php echo $lang['admin-general-reg-social']; ?></label>&nbsp;&nbsp;
								<select id="social" name="social" class="form-control select2" style="width:200px"> 
									<option value="0" <?php if(isset($settings['social']) && $settings['social'] == '0') { echo ' selected'; } ?>>Disabled</option>
									<option value="1" <?php if(isset($settings['social']) && $settings['social'] == '1') { echo ' selected'; } ?> >Enabled</option>
								</select><br><br>
							</div>
							
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="update_settings" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
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
<script src="<?php echo WEB_LINK; ?>public/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.tagsinput').tagsinput({
			maxTags: 50,
			maxChars: 30,
			trimValue: true
		});
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>