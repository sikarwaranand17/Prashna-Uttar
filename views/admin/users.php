<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");

if (isset($_POST['add_user'])) {
		if(!$current_user->can_see_this("adminusers.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$db_fields = Array('f_name','l_name', 'mobile', 'address' ,  'comment' , 'about' , 'disabled' , 'upload_files');
			
			$upload_key = array_search('upload_files' , $db_fields);
			if($upload_key) {
				unset($db_fields[$upload_key]);
				$upload_present = true;
			}
			
			foreach($db_fields as $field) {
				if(isset($_POST[$field])) {
					$$field = $db->escape_value($_POST[$field]);
				}
			}
			
			$new_entry = new User();
			
			foreach($db_fields as $field) {
				if(isset($$field)) {
					$new_entry->$field = $$field;
				}
			}
			
			$password = $db->escape_value($_POST['password']);
			$email = $db->escape_value($_POST['email']);
			
			$email_exists = User::check_existance("email", $email);
			
			if($email_exists) {
				$msg = $lang['alert-email_exists'];
				redirect_to("{$url_mapper['admin/users']}?type=new&edit=fail&msg={$msg}");
			}
			
			$new_entry->email = $email;
			
			$username = $db->escape_value(trim(str_replace(' ' , '' , $_POST['username'])));
			
			$username_exists = User::check_existance("username", $username );
			
			if($username_exists) {
				$msg = $lang['alert-username_exists'];
				redirect_to("{$url_mapper['admin/users']}?type=new&edit=fail&msg={$msg}");
			}
			
			$new_entry->username = $username;
			
			$prvlg_group = $db->escape_value($_POST['prvlg_group']);
			$new_entry->prvlg_group= $prvlg_group;
			
			$phpass = new PasswordHash(8, true);
			$hashedpassword = $phpass->HashPassword($password);
			$new_entry->password = $hashedpassword;
			
			if(isset($upload_present) && $upload_present == true) {
				$files = '';
				$f = 0;
				$images = array();
				$num_pics = 1;
				$target = $_FILES['upload_files'];
				$upload_problems = 0;
				for ($f ; $f < $num_pics ; $f++) :
					$file = "file";
					$string = $$file . "{$f}";
					$$string = new File();	
						if(!empty($_FILES['upload_files']['name'][$f])) {
							$$string->attach_file($_FILES['upload_files'], $f);
							if ($$string->save()) {
								$images[$f] = $$string->id;
							} else {
								$upl_msg = "{$lang['alert-upload_error']}: ";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
				
				if(!empty($images)) {
					$final_string = implode("," , $images);
					
					$new_entry->avatar = $final_string;
				}
			}
			
			$new_entry->joined = strftime("%Y-%m-%d %I:%M %p", time());
			
			if ($new_entry->create()) {
				Log::log_action($current_user->id , "Add New User" , "Add New User ({$new_entry->f_name} {$new_entry->l_name}) - id #({$new_entry->id})" );
				$msg = $lang['alert-create_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/users']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-create_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/users']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/users']}?edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_user'])) {
		if(!$current_user->can_see_this("adminusers.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			$db_fields = Array('f_name','l_name', 'mobile', 'address' ,  'comment' , 'about' , 'upload_files');
			
			$upload_key = array_search('upload_files' , $db_fields);
			if($upload_key) {
				unset($db_fields[$upload_key]);
				$upload_present = true;
			}
			
			foreach($db_fields as $field) {
				if(isset($_POST[$field])) {
					$$field = $db->escape_value($_POST[$field]);
				}
			}
			
			$edited_entry = User::get_specific_id($edit_id);
			
			foreach($db_fields as $field) {
				if(isset($$field)) {
					$edited_entry->$field = $$field;
				}
			}
			
			$password = $db->escape_value($_POST['password']);
			
			if($current_user->can_see_this('adminusers.changemail' , $group) ) {
			$email = $db->escape_value($_POST['email']);
			
			$current_email = $edited_entry->email;
			$email_exists = User::check_existance_except("email", $email , $edit_id);
			
			if($email_exists) {
				$msg = $lang['alert-email_exists'];
				redirect_to("{$url_mapper['admin/users']}?id={$edit_id}&hash={$_POST['hash']}&type=edit&edit=fail&msg={$msg}");
			}
			
			if($email != '' && $email != $current_email) {
			$edited_entry->email = $email;
			}
			}
			
			if($current_user->can_see_this('adminusers.changeusername' , $group) ) {
			$username = $db->escape_value(trim(str_replace(' ' , '' , $_POST['username'])));
			
			$current_username = $edited_entry->username;
			$username_exists = User::check_existance_except("username", $username , $edit_id);
			
			if($username_exists) {
				$msg = $lang['alert-username_exists'];
				redirect_to("{$url_mapper['admin/users']}?id={$edit_id}&hash={$_POST['hash']}&type=edit&edit=fail&msg={$msg}");
			}
			
			if($username != '' && $username != $current_username) {
			$edited_entry->username = $username;
			}
			}
			
			if(isset($_POST['prvlg_group']) && is_numeric($_POST['prvlg_group']) && $current_user->can_see_this('adminusers.power' , $group)  && $edited_entry->id != '1' ) {
				$prvlg_group = $db->escape_value($_POST['prvlg_group']);
				$edited_entry->prvlg_group= $prvlg_group;
			}
			
			
			if($current_user->can_see_this('adminusers.changepass' , $group) ) {
			$current_password = $edited_entry->password;
			if($password !='' && $password != $current_password ) {
			$phpass = new PasswordHash(8, true);
			$hashedpassword = $phpass->HashPassword($password);
			
			$edited_entry->password = $hashedpassword;
			}
			}
			
			if(isset($upload_present) && $upload_present == true) {
				$files = '';
				$f = 0;
				$images = array();
				$num_pics = 1;
				$target = $_FILES['upload_files'];
				$upload_problems = 0;
				for ($f ; $f < $num_pics ; $f++) :
					$file = "file";
					$string = $$file . "{$f}";
					$$string = new File();	
						if(!empty($_FILES['upload_files']['name'][$f])) {
							$$string->attach_file($_FILES['upload_files'], $f);
							if ($$string->save()) {
								$images[$f] = $$string->id;
							} else {
								$upl_msg = "{$lang['alert-upload_error']}: ";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
				
				if(!empty($images)) {
					$final_string = implode("," , $images);
					//if($edited_entry->files != NULL) {
						//$edited_entry->files .= ",". $final_string;
					//} else {
						//$edited_entry->files .= $final_string;
					//}
					$edited_entry->avatar = $final_string;
				}
			}
			
			if(isset($_POST['disabled']) && $_POST['disabled'] == '1' && $edited_entry->id != "1" ) {
				$edited_entry->disabled = 1;
			} else {
				$edited_entry->disabled = 0;
			}
			
			if ($edited_entry->update()) {
				Log::log_action($current_user->id , "Edit User" , "Edit User ({$new_entry->f_name} {$new_entry->l_name}) - id #({$edited_entry->id})" );
				$msg = $lang['alert-update_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/users']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/users']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/users']}?edit=fail&msg={$msg}");
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
				<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/users']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-users']; ?></a></li><?php } ?>
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
			
			
			<h5><?php echo $lang['admin-users-title']; ?>&nbsp;
				
				<?php if(isset($_GET['type']) && $_GET['type'] != '' ) { ?>
				<a href="<?php echo $url_mapper['admin/users']; ?>" class="btn btn-sm btn-primary"><i class="fe fe-arrow-<?php echo $lang['direction-left']; ?>"></i>&nbsp;<?php echo $lang['btn-back']; ?></a>
				<?php } else { ?>
				<a href="<?php echo $url_mapper['admin/users']; ?>?type=new" class="btn btn-sm btn-success"><i class="fe fe-plus"></i>&nbsp;<?php echo $lang['btn-add']; ?></a>
				<?php } ?>
				</h5>
				<hr>
				
				<?php if(isset($_GET['type']) && $_GET['type'] == 'new' ) {
				?>
					
				<form method="post" action="<?php echo $url_mapper['admin/users']; ?>" enctype="multipart/form-data">
				
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="f_name"><?php echo $lang['admin-users-f_name']; ?></label>
								<input type="text" class="form-control" name="f_name" id="f_name" placeholder="First Name.." required value="">
							</div>
							<div class="form-group">	
								<label for="l_name"><?php echo $lang['admin-users-l_name']; ?></label>
								<input type="text" class="form-control" name="l_name" id="l_name" placeholder="Last Name.." required value="">
							</div>
							<div class="form-group">	
								<label for="mobile"><?php echo $lang['admin-users-phone']; ?></label>
								<input type="text" class="form-control" name="mobile" id="mobile" placeholder="Phone.." value="">
							</div>
							<div class="form-group">	
								<label for="address"><?php echo $lang['admin-users-address']; ?></label>
								<input type="text" class="form-control" name="address" id="address" placeholder="Address.." value="">
							</div>
								
							<hr>
								
								<div class="form-group" >
									<label for="prvlg_group"><?php echo $lang['admin-users-group']; ?></label>&nbsp;&nbsp;
									<select id="prvlg_group" name="prvlg_group" class="form-control" style="width:200px" >
										<?php 
											$groups = Group::get_everything(" AND deleted = 0 ");
											foreach($groups as $g) {
												if($g->id != '1' || $g->id == '1' && $current_user->prvlg_group == '1' ) {
													echo "<option value='{$g->id}' ";
													echo " >{$g->name}</option>";
												}
											}
										?>
									</select>
								</div>
								
								<div class="form-group">
									<label for="comment"><?php echo $lang['admin-users-comment']; ?></label>
									<input type="text" class="form-control" name="comment" id="comment" placeholder="Short Description.." value="">
								</div>
								<div class="form-group">
									<label for="about"><?php echo $lang['admin-users-about']; ?></label>
									<textarea name="about" class="form-control" rows="3"></textarea>
								</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								
								
								<label class="col-form-label" for="img1_upl"><?php echo $lang['admin-users-avatar']; ?></label>
								<div class="controls">
									
									<img src="<?php echo WEB_LINK.'public/img/avatar.png'; ?>" class="img-polaroid rounded-circle" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; width:64px; height:64px" id="img1">
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
							</div><br style="clear:both"><br>
								
				<div class="form-group">
				
				  <label for="username" class="col-form-label"><?php echo $lang['admin-users-username']; ?></label>
				  
					<div class="input-group">
					  <div class="input-group-prepend" id="basic-addon1"><span class="input-group-text" id="basic-addon1">@</span></div>
					  <input type="text" class="form-control " id="username" name="username" placeholder="" value="" required >
					</div>
				  
				  </div>
				  
				  <div class="form-group">
				  <label for="email" class="col-form-label"><?php echo $lang['admin-users-email']; ?></label>
				  <input type="email" class="form-control " id="email" name="email" placeholder="" required></div>
				  
					
				  
				  <div class="form-group"><label for="password" class="col-form-label"><?php echo $lang['admin-users-pass']; ?></label>
				  <input type="password" class="form-control " id="password" name="password" placeholder="" required></div>
					<br style='clear:both'>
					<div id="messages"></div>
					<br>
					
								</div>
							
						</div>
						
					
					
							
								<input class="btn btn-success pull-<?php echo $lang['direction-right']; ?>" type="submit" name="add_user" value="<?php echo $lang['btn-submit']; ?>">
							
							<br style="clear: both">
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
					
				<?php } elseif(isset($_GET['type']) && $_GET['type'] == 'edit'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					if(!User::check_id_existance($db->escape_value($_GET['id']))) {
						redirect_to($url_mapper['error/404/']);
					}
					$user = User::get_specific_id($db->escape_value($_GET['id']));
					if($user->avatar) {
						$img = File::get_specific_id($user->avatar);
						$quser_avatar= WEB_LINK."public/".$img->image_path();
						
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'public/img/avatar.png';
						}
						
					} else {
						$quser_avatar = WEB_LINK.'public/img/avatar.png';
					}
				?>
				
				
				
				<form method="post" action="<?php echo $url_mapper['admin/users']; ?>" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="f_name"><?php echo $lang['admin-users-f_name']; ?></label>
								<input type="text" class="form-control" name="f_name" id="f_name" placeholder="First Name.." required value="<?php echo $user->f_name; ?>">
							</div>
							<div class="form-group">	
								<label for="l_name"><?php echo $lang['admin-users-l_name']; ?></label>
								<input type="text" class="form-control" name="l_name" id="l_name" placeholder="Last Name.." required value="<?php echo $user->l_name; ?>">
							</div>
							<div class="form-group">	
								<label for="mobile"><?php echo $lang['admin-users-phone']; ?></label>
								<input type="text" class="form-control" name="mobile" id="mobile" placeholder="Phone.." value="<?php echo $user->mobile; ?>">
							</div>
							<div class="form-group">	
								<label for="address"><?php echo $lang['admin-users-address']; ?></label>
								<input type="text" class="form-control" name="address" id="address" placeholder="Address.." value="<?php echo $user->address; ?>">
							</div>
								
							<hr>
								<?php if($current_user->can_see_this('adminusers.power' , $group) ) { ?>
								<div class="form-group" <?php if($user->id == '1') { echo " style='display:none' ";  } ?>>
									<label for="prvlg_group"><?php echo $lang['admin-users-group']; ?></label>&nbsp;&nbsp;
									<select id="prvlg_group" name="prvlg_group" class="form-control" style="width:200px" <?php if($user->id == '1') { echo ' readonly'; } ?> >
										<?php 
											$groups = Group::get_everything(" AND deleted = 0 "); 
											foreach($groups as $g) {
												if($g->id != '1' || $g->id == '1' && $current_user->prvlg_group == '1' ) {
													echo "<option value='{$g->id}' ";
														if($g->id == $user->prvlg_group) { echo ' selected'; }
													echo " >{$g->name}</option>";
												}
											}
										?>
									</select>
								</div>
								<?php } ?>
								<div class="form-group">
									<label for="comment"><?php echo $lang['admin-users-comment']; ?></label>
									<input type="text" class="form-control" name="comment" id="comment" placeholder="Short Description.." value="<?php echo $user->comment; ?>">
								</div>
								<div class="form-group">
									<label for="about"><?php echo $lang['admin-users-about']; ?></label>
									<textarea name="about" class="form-control" rows="3"><?php echo $user->about; ?></textarea>
								</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								
								
								<label class="col-form-label" for="img1_upl"><?php echo $lang['admin-users-avatar']; ?></label>
								<div class="controls">
									
									<img src="<?php echo $quser_avatar; ?>" class="img-polaroid rounded-circle" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; width:64px; height:64px" id="img1">
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
							</div><br style="clear:both"><br>
								
				<div class="form-group">
				
				  <label for="username" class="col-form-label"><?php echo $lang['admin-users-username']; ?></label>
				  
					<div class="input-group">
					  <div class="input-group-prepend" id="basic-addon1"><span class="input-group-text" id="basic-addon1">@</span></div>
					  <input type="text" class="form-control " id="username" name="username" placeholder="" value="<?php echo $user->username; ?>"  <?php if(!$current_user->can_see_this('adminusers.changeusername' , $group) ) { ?> disabled readonly <?php } ?> >
					</div>
				  
				  </div>
				  
				  <?php if($current_user->can_see_this('adminusers.changemail' , $group) ) { ?>
				  <div class="form-group">
				  <label for="username" class="col-form-label"><?php echo $lang['admin-users-email']; ?></label>
				  <input type="email" class="form-control " id="username" name="email" placeholder="Unchanged" ></div>
				  <?php } ?>
					
				  <?php if($current_user->can_see_this('adminusers.changepass' , $group) ) { ?>
				  <div class="form-group"><label for="password" class="col-form-label"><?php echo $lang['admin-users-pass']; ?></label>
				  <input type="text" class="form-control " id="password" name="password" placeholder="Unchanged" ></div>
				  <?php } ?>
				   <div id="messages"></div>
					<br>
					<?php if($current_user->can_see_this('adminusers.suspend' , $group) ) { ?>
					<div class="form-group">
					<label><input type="checkbox" data-checkbox="form-control" value="1" name="disabled" <?php if($user->disabled == '1') { echo ' checked'; } ?>> <?php echo $lang['admin-users-suspend']; ?></label>
					</div><?php } ?>
								
								</div>
							
						</div>
						
					
							
								<input class="btn btn-success pull-<?php echo $lang['direction-right']; ?>" type="submit" name="edit_user" value="<?php echo $lang['btn-submit']; ?>">
							
							<br style="clear: both">
							
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$user->id."\" readonly/>";
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
				
				
				
				<?php
				} elseif(isset($_GET['type']) && $_GET['type'] == 'delete'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					$id = $db->escape_value($_GET['id']);
					
					if(!User::check_id_existance($id)) {
						redirect_to("{$url_mapper['error/404/']}");
					}
					
					$this_obj = User::get_specific_id($id);
					
					if(!$current_user->can_see_this("adminusers.delete",$group)) {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/users']}?edit=fail&msg={$msg}");
					}
					if($id == "1") {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/users']}?edit=fail&msg={$msg}");
					}
					$this_obj->deleted = 1;
					if($this_obj->update()) {
						$msg = $lang['alert-delete_success'];
						Log::log_action($current_user->id , "Delete User" , "Delete user named ({$new_entry->f_name} {$new_entry->l_name}) - id #({$this_obj->id})");
						redirect_to("{$url_mapper['admin/users']}?edit=success&msg={$msg}");
					} else {
						$msg = $lang['alert-delete_failed'];
						redirect_to("{$url_mapper['admin/users']}?edit=fail&msg={$msg}");
					}
					
					
				} else { ?>
				
					<table class="table table-bordered table-striped table-responsive">
                      <thead>
                        <tr>
                          <th style='width:10px'>#</th>
                          <th><?php echo $lang['admin-users-name']; ?></th><th><?php echo $lang['admin-users-group']; ?></th>
						  <th><?php echo $lang['admin-users-phone']; ?></th><th><?php echo $lang['admin-users-email']; ?></th>
						  <th><?php echo $lang['admin-users-questions']; ?></th><th><?php echo $lang['admin-users-answers']; ?></th>
                          <th style='width:150px'><i class="fe fe-settings"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
							
							if (isset($_GET['per_page']) && is_numeric($_GET['per_page']) ) {
								$per_page= $_GET['per_page'];
							} else {
								$per_page=20;
							}
							
							if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
								$page= $_GET['page'];
							} else {
								$page=1;
							}
							
							
							$query = ' AND id != 1000 ';
							if(isset($_GET['search']) && $_GET['search'] == true && isset($_GET['data']) ) {
								$query .= " AND (f_name LIKE '%" . $db->escape_value($_GET['data']) .  "%' OR l_name LIKE '%" . $db->escape_value($_GET['data']) .  "%') ";
							}
							
							$total_count = User::count_everything(" AND id != 1000 AND deleted = 0 {$query} ");
							$pagination = new Pagination($page, $per_page, $total_count);
							$all_obj= User::get_users($query," LIMIT {$per_page} OFFSET {$pagination->offset()} ");
						
							
							$i= (($page-1) * $per_page) + 1;
							
							
							foreach($all_obj as $obj) :
							
							if($obj->avatar) {
								$pic = "<a href=\"#modal-image-{$obj->id}\" data-toggle='modal' class='btn btn-sm btn-icon btn-rounded btn-warning' data-rel='tooltip' data-placement='top' title='Profile Picture' data-original-title='Profile Picture'><i class='fa fa-search'></i></a>";
							} else {
								$pic = "";
							}
							
							if($current_user->can_see_this("users.update",$group)) {
								$edit = "<a href=\"{$url_mapper['admin/users']}/&id={$obj->id}&type=edit&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-primary' data-toggle='tooltip' data-rel='tooltip' data-placement='top' title='Edit' data-original-title='Edit'  ><i class='fa fa-pencil'></i></a>";
							} else {
								$edit = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-rel='tooltip' data-toggle='tooltip' data-placement='top' title='Edit (unavailable)' data-original-title='Edit (unavailable)'  ><i class='fa fa-pencil'></i></a>";
							}
							
							if($current_user->can_see_this("users.delete",$group)) {
								$delete = "<a href=\"{$url_mapper['admin/users']}/&id={$obj->id}&type=delete&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-danger' data-rel='tooltip' data-toggle='tooltip' data-placement='top' title='delete' data-original-title='delete'   onclick=\"return confirm('Are you sure you want to delete this record?');\" ><i class='fa fa-times'></i></a>";
							} else {
								$delete = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-rel='tooltip' data-toggle='tooltip' data-placement='top' title='delete (unavailable)' data-original-title='delete (unavailable)'  ><i class='fa fa-times'></i></a>";
							}
							
							$usergroup = Group::get_specific_id($obj->prvlg_group);
							$questions = Question::count_questions_for($obj->id," ");
							$answers = Answer::count_answers_for_user($obj->id," ");
							
						?>
						<tr <?php if($obj->disabled) { echo ' style="color:red" '; } ?> >
                          <td><?php echo $i; ?></td>
                          <td><?php echo $obj->f_name. ' ' . $obj->l_name;?></td>
						  <td><?php echo $usergroup->name; ?></td>
						  <td><?php echo $obj->mobile; ?></td>
						  <td><?php echo $obj->email; ?></td>
						  <td><?php echo $questions; ?></td>
						  <td><?php echo $answers; ?></td>
                          <td><div class="btn-group"><?php echo $pic .$edit . $delete; ?></div></td>
						  
						<?php 
							if(isset($obj->avatar) && $obj->avatar) {
								$img = File::get_specific_id($obj->avatar);
								$link = UPL_FILES."/".$img->image_path();
						?>
						<div class="modal fade modal-image" id="modal-image-<?php echo $obj->id; ?>" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
							<div class="modal-dialog">
							  <div class="modal-content">
								<div class="modal-header">
								  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								</div>
								<div class="modal-body">
								  <img src="<?php echo $link; ?>" alt="picture 1" class="img-fluid">
								</div>
								<div class="modal-footer">
								  <p><?php echo $obj->f_name . ' ' . $obj->l_name; ?></p>
								</div>
							  </div>
							</div>
						  </div>
						<?php 
							}
						?>
                        </tr>
                        
						<?php 
							$i++;
							endforeach;
						?>
                      </tbody>
                    </table>
					
					<?php
					
					if(isset($pagination) && $pagination->total_pages() > 1) {
					?>
					<div class="pagination btn-group">
					
							<?php
							if ($pagination->has_previous_page()) {
								$page_param = $url_mapper['admin/users'];
								$page_param .= "?page=";
								$page_param .= $pagination->previous_page();

							echo "<a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\"><i class=\"fa fa-chevron-{$lang['direction-left']}\"></i></a>";
							} else {
							?>
							<a class="btn btn-secondary" type="button"><i class="fa fa-chevron-<?php echo $lang['direction-left']; ?>"></i></a>
							<?php
							}
							
							for($p=1; $p <= $pagination->total_pages(); $p++) {
								if($p == $page) {
									echo "<a class=\"btn btn-secondary active\" type=\"button\">{$p}</a>";
								} else {
									$page_param = $url_mapper['admin/users'];
									$page_param .= "?page=";
									$page_param .= $p;

									echo "<a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\">{$p}</a>";
								}
							}
							if($pagination->has_next_page()) {
								$page_param = $url_mapper['admin/users'];
								$page_param .= "?page=";
								$page_param .= $pagination->next_page();

							echo " <a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\"><i class=\"fa fa-chevron-{$lang['direction-right']}\"></i></a> ";
							} else {
							?>
							<a class="btn btn-secondary" type="button"><i class="fa fa-chevron-<?php echo $lang['direction-right']; ?>"></i></a>
							<?php
							}
							?>
					
					</div>
					<?php
					}
					?>
				
				
				<?php } ?>
			
		</div></div>
	</div>
</div>
</div>

<?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
<script src="<?php echo WEB_LINK; ?>public/plugins/strongpass/StrongPass.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
	
	var options = {
		onKeyUp: function (evt) {
			$(evt.target).pwstrength("outputErrorList");
		}
	};
	$('#password').pwstrength(options);
		
	function readURL(input,targetid) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$("#" + targetid).attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#img1_upl").change(function(){
		readURL(this, 'img1');
	});
		
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>