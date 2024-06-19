<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");

if (isset($_POST['add_group'])) {
		if(!$current_user->can_see_this("groups.create",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			$privileges_raw= $_POST["privileges"];
			$name= $db->escape_value($_POST["name"]);
			
			//$privileges_danger = "index,dashboard,".implode("," , $privileges_raw);
			$privileges_danger = implode("," , $privileges_raw);
			$privileges = $db->escape_value($privileges_danger);
			
			$new_entry = New Group();
			$new_entry->name = $name;
			$new_entry->privileges = $privileges;
			
			if ($new_entry->create()) {
				
				Log::log_action($current_user->id , "Add Group" , "Add new Group to application ({$new_entry->name})" );
				
				$msg = $lang['alert-create_success'];
				if($upload_problems == '1') {
					$msg .= "<hr>{$upl_msg}";
				}
				redirect_to("{$url_mapper['admin/groups']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-create_failed'];
				redirect_to("{$url_mapper['admin/groups']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/groups']}?edit=fail&msg={$msg}");
		}
}
if (isset($_POST['edit_group'])) {
		if(!$current_user->can_see_this("groups.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			$privileges_raw= $_POST["privileges"];
			$name= $db->escape_value($_POST["name"]);
			
			$privileges_danger = implode("," , $privileges_raw);
			$privileges = $db->escape_value($privileges_danger);

			$edited_entry = Group::get_specific_id($edit_id);
			$edited_entry->name = $name;
			$edited_entry->privileges = $privileges;
			
			if ($edited_entry->update()) {
				Log::log_action($current_user->id , "Update Group" , "Update Group ({$edited_entry->name}) - id #({$edited_entry->id})" );
				
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/groups']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				redirect_to("{$url_mapper['admin/groups']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/groups']}?edit=fail&msg={$msg}");
		}
}

$sections = Array('index.read,pages.read,error-404.read|Home' => 
					Array(
								'index.notifications|Show Notifications list' => Array(),
								'index.post|Show Post Question form' => Array(),
								'index.feed|Show Feeds list' => Array('feed.follow|Follow Feeds list'),
							),
					'questions.read|Questions' => 
					Array(
								'questions.interact|Like/Dislike/Follow Questions' => array(),
								'post.read,questions.create|Post Questions' => array('questions.power|Post Immediately (without admin approval)'),
								'questions.update|Update Questions' => array(),
								'questions.delete|Delete Questions' => array(),
							),
					'spaces.read|Spaces' => 
					Array(
								'spaces.interact|Like/Dislike/Follow Space' => array(),
								'spaces.read,spaces.create|Add Space' => array('spaces.power|Post Immediately (without admin approval)'),
								'spaces.update|Update Space' => array(),
								'spaces.delete|Delete Space' => array(),
							),
					'answers.read|Answers' => 
					Array(
								'answers.create|Post Answers' => array('answers.power|Post Immediately (without admin approval)'),
								'answers.update|Update Answers' => array(),
								'answers.delete|Delete Answers' => array(),
							),
							
					'users.read|Profiles' => 
					Array(
								'users.follow|Follow Users' => array(),
								'users.update|Update Account' => array('users.changepass|Change Password' , 'users.changemail|Change Email'),
								'users.delete|Delete Account' => array(),
							),
					'admin.read|Admin Section' => 
					Array(
								'dashboard.read|Show dashboard' => Array(),
								'general_settings.update|Update General Site Settings' => Array(),
								'profanity_filter.update|Update Profanity Filter' => Array(),
								'pending.read|Show Pending Posts' => Array(
																						'pending.update|Approve questions & answers'
																					),
								'pages.read|Show Pages section' => Array(
																						'pages.update|Update pages'
																					),
								'adminusers.read|Show Users profiles' => Array(
																						'adminusers.update|Edit users profiles',
																						'adminusers.changepass|Edit users password',
																						'adminusers.changemail|Edit users email',
																						'adminusers.changeusername|Edit users username',
																						'adminusers.power|Change users privileges',
																						'adminusers.suspend|Suspend accounts',
																						'adminusers.delete|Delete users profiles'
																					),
								'admintopics.read|Show topics page' => Array(
																						'admintopics.update|Edit Topic',
																						'admintopics.delete|Delete Topic'
																					),
								'admanager.read|Show AdManager page' => Array(
																						'admanager.create|Add new ads',
																						'admanager.update|Edit Ads',
																						'admanager.delete|Delete Ads'
																					),
								'groups.read|Show User Group Privileges page' => Array(
																								'groups.create|Create new privilege groups', 
																								'groups.update|Update privilege groups', 
																								'groups.delete|Delete privilege groups'
																							),
								
							)
				);


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
				<?php if($current_user->can_see_this('dashboard.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/groups']; ?>" class="col-md-12 "><?php echo $lang['admin-section-dashboard']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('general_settings.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/settings']; ?>" class="col-md-12 "><?php echo $lang['admin-section-general']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pending']; ?>" class="col-md-12 "><?php echo $lang['admin-section-pending'] . ' ' . $pending_posts_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/reports']; ?>" class="col-md-12"><?php echo $lang['admin-section-reports'] . ' ' . $pending_reports_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/users']; ?>" class="col-md-12"><?php echo $lang['admin-section-users']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('groups.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/groups']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-groups']; ?></a></li><?php } ?>
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
			<h5><?php echo $lang['admin-groups-title']; ?>&nbsp;&nbsp;<?php if( isset($_GET['type']) && $_GET['type'] == 'new' || isset($_GET['type']) && $_GET['type'] == 'edit' ) { ?><a href='<?php echo $url_mapper['admin/groups']; ?>' class="btn btn-sm btn-primary"><i class='fe fe-arrow-<?php echo $lang['direction-left']; ?>'></i>&nbsp;<?php echo $lang['btn-back']; ?></a><?php } else {  ?><a href='<?php echo $url_mapper['admin/groups'] . '?type=new'; ?>' class="btn btn-sm btn-success"><i class='fe fe-plus'></i> <?php echo $lang['btn-add']; ?></a><?php } ?></h5><hr>
			
				<?php if( isset($_GET['type']) && $_GET['type'] == 'new' ) {
				?>
				
				<form id="form-validation" action="<?php echo $url_mapper['admin/groups']; ?>" method="post" class="">
						
						<div class="col-md-6">
						  <div class="form-group">
							<label for="name" class="col-form-label"><?php echo $lang['admin-groups-name']; ?></label>
							<input type="text" class="form-control form-white" id="name" name="name" placeholder="Name" value="" required >
						  </div>
						</div>
						
						<br style="clear:both"/>
						
						<div class="row">
							
							<?php 
							foreach($sections as $section_title => $section_privileges) {
							?>
							<div class="col-md-6">
							  <div class="card card-default mb-3">
								<div class="card-header">
								  <h5 class="card-title"><?php 
									$parent_data = explode('|' , $section_title);
								  echo '<input type="checkbox" class="liParent" name="privileges[]" ';
								  echo '  value="'.$parent_data[0].'" />&nbsp;' . $parent_data[1]; ?></h5>
								</div>
								<div class="card-body">
									
									<ul class="privileges_menu" >
										<?php 
											foreach($section_privileges as $parent => $child) {
												$parent_data = explode('|' , $parent);
												echo '<li><input type="checkbox" class="liParent" name="privileges[]" ';
												echo ' value="'.$parent_data[0].'" />&nbsp;'.$parent_data[1];
												if(is_array($child) && !empty($child) ) {	//Has submenu!
													echo '<ul class="privileges_menu">';
													foreach($child as $grandchild) {
														$grandchild_data = explode('|' , $grandchild);
														echo '<li><input type="checkbox" class="liChild" name="privileges[]" '; 
														echo ' value="'.$grandchild_data[0].'" />&nbsp;'.$grandchild_data[1] .'</li>';
													}
													echo '</ul>';
												}
												echo '</li>';
											}
										?>
									</ul>
									
								</div>
							  </div>
							</div>
							<?php } ?>
						
						
						</div>
					
					<?php	
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>"				
					?>
					
					<center>
						<button class="btn btn-success" name="add_group" type="submit" ><?php echo $lang['btn-submit']; ?></button>
						<a href='<?php echo $url_mapper['admin/groups'] . '?section=groups'; ?>' class="btn btn-danger" ><?php echo $lang['btn-cancel']; ?></a>
					</center>		
					
					</form>
				<?php
				} elseif( isset($_GET['type']) && $_GET['type'] == 'edit'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					if(!Group::check_id_existance($db->escape_value($_GET['id']))) {
						redirect_to($url_mapper['error/404/']);
					}
					$this_obj = Group::get_specific_id($db->escape_value($_GET['id']));
				?>
				<form id="form-validation" action="<?php echo $url_mapper['admin/groups']; ?>" method="post" class="">
						
						<div class="col-md-6">
						  <div class="form-group">
							<label for="name" class="col-form-label"><?php echo $lang['admin-groups-name']; ?></label>
							<input type="text" class="form-control form-white" id="name" name="name" placeholder="Name" value="<?php echo $this_obj->name; ?>" required >
						  </div>
						</div>
						
						<br style="clear:both"/>
						
						<div class="row">
							
							<?php 
							foreach($sections as $section_title => $section_privileges) {
							?>
							<div class="col-md-6">
							  <div class="card card-default mb-3">
								<div class="card-header">
								  <h5 class="card-title"><?php 
									$parent_data = explode('|' , $section_title);
								  echo '<input type="checkbox" class="liParent" name="privileges[]" ';  if($current_user->can_see_this( $parent_data[0] ,$this_obj->id)) { echo "checked=\"checked\""; }  echo '  value="'.$parent_data[0].'" />&nbsp;' . $parent_data[1]; ?></h5>
								</div>
								<div class="card-body">
									
									<ul class="privileges_menu" >
										<?php 
											foreach($section_privileges as $parent => $child) {
												$parent_data = explode('|' , $parent);
												echo '<li><input type="checkbox" class="liParent" name="privileges[]" ';  if($current_user->can_see_this( $parent_data[0] ,$this_obj->id)) { echo "checked=\"checked\""; } echo ' value="'.$parent_data[0].'" />&nbsp;'.$parent_data[1];
												if(is_array($child) && !empty($child) ) {	//Has submenu!
													echo '<ul class="privileges_menu">';
													foreach($child as $grandchild) {
														$grandchild_data = explode('|' , $grandchild);
														echo '<li><input type="checkbox" class="liChild" name="privileges[]" ';  if($current_user->can_see_this( $grandchild_data[0] ,$this_obj->id)) { echo "checked=\"checked\""; } echo ' value="'.$grandchild_data[0].'" />&nbsp;'.$grandchild_data[1] .'</li>';
													}
													echo '</ul>';
												}
												echo '</li>';
											}
										?>
									</ul>
									
								</div>
							  </div>
							</div>
							<?php } ?>
						
						
						</div>
					
					<?php	
					echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$this_obj->id."\" readonly/>";
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>"	;
					?>
					
					<center>
						<button class="btn btn-success" name="edit_group" type="submit" ><?php echo $lang['btn-submit']; ?></button>
						<a href='<?php echo $url_mapper['admin/groups'] . '?section=groups'; ?>' class="btn btn-danger" ><?php echo $lang['btn-cancel']; ?></a>
					</center>		
					
					</form>
				<?php } elseif(isset($_GET['type']) && $_GET['type'] == 'delete'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					$id = $db->escape_value($_GET['id']);
					
					if(!Group::check_id_existance($id)) {
						redirect_to("{$url_mapper['error/404/']}");
					}
					
					$this_obj = Group::get_specific_id($id);
					
					if(!$current_user->can_see_this("groups.delete",$group)) {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/groups']}?edit=fail&msg={$msg}");
					}
					if($id <= "3") {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/groups']}?edit=fail&msg={$msg}");
					}
					$this_obj->deleted = 1;
					if($this_obj->update()) {
						$msg = $lang['alert-delete_success'];
						Log::log_action($current_user->id , "Delete Group" , "Delete Group named ({$this_obj->name}) - id #({$this_obj->id})");
						redirect_to("{$url_mapper['admin/groups']}?edit=success&msg={$msg}");
					} else {
						$msg = $lang['alert-delete_failed'];
						redirect_to("{$url_mapper['admin/groups']}?edit=fail&msg={$msg}");
					}
					
					
				} else { ?>
				
				
				<table class="table table-striped table-bordered custom_table">
                      <thead>
                        <tr>
                          <th style='width:10px'>#</th>
                          <th><?php echo $lang['admin-groups-name']; ?></th><th><?php echo $lang['admin-groups-users']; ?></th>
                          <th style='width:150px'><i class="fe fe-settings"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
							
							$query = ' AND deleted = 0 ';
							
							$all_obj= Group::get_everything($query," ");
							
							$i= 1;
							foreach($all_obj as $obj) :
							
							if($current_user->can_see_this("groups.update",$group)) {
								$edit = "<a href=\"{$url_mapper['admin/groups']}?id={$obj->id}&type=edit&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-primary' data-toggle='tooltip' data-placement='top' title='Edit' data-original-title='Edit'  ><i class='fa fa-pencil'></i></a>";
							} else {
								$edit = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-toggle='tooltip' data-placement='top' title='Edit (Unavailable)' data-original-title='Edit (Unavailable)'  ><i class='fa fa-pencil'></i></a>";
							}
							
							if($current_user->can_see_this("groups.delete",$group)) {
								$delete = "<a href=\"{$url_mapper['admin/groups']}?id={$obj->id}&type=delete&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-danger' data-toggle='tooltip' data-placement='top' title='Delete' data-original-title='Delete'   onclick=\"return confirm('Are you sure you want to delete this group?');\" ><i class='fa fa-times'></i></a>";
							} else {
								$delete = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-toggle='tooltip' data-placement='top' title='Delete (Unavailable)' data-original-title='Delete (Unavailable)'  ><i class='fa fa-times'></i></a>";
							}
							
							if($obj->id <= 2) {
								$delete = '';
							}
							
							$related_users = User::get_users_for_group($obj->id);
								$names = array();
								foreach ($related_users as $user ) {
									$names[] = $user->f_name . ' ' . $user->l_name;
								}
								
								if (!empty($names) ) { $names_string = implode(" - " , $names); } else { $names_string = "None"; }

								$count = "<span data-toggle=\"tooltip\" style=\"cursor:pointer\" data-rel=\"tooltip\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"{$names_string}	\" data-original-title=\"{$names_string}\">".count($related_users)."</span>";
						?>
						<tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $obj->name; ?></td><td><?php echo $count; ?></td>
                          <td><div class="btn-group"><?php echo $edit . " " . $delete; ?></div></td>
						  
						
                        </tr>
                        
						<?php 
							$i++;
							endforeach;
						?>
                      </tbody>
                    </table>
				
				<?php } ?>
			
		</div></div>
	</div>
</div>
</div>
<?php require_once(VIEW_PATH.'pages/preloader.php'); ?>

<script type="text/javascript">
	$(document).ready(function() {
		$('.custom_table').DataTable({
			"dom" : "rtip"
		});
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>