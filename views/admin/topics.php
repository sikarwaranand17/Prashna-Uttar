<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");

if (isset($_POST['add_topic'])) {
		if(!$current_user->can_see_this("admintopics.read",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$db_fields = Array('name', 'description', 'upload_files');
			
			$upload_key = array_search('upload_files' , $db_fields);
			if($upload_key) {
				unset($db_fields[$upload_key]);
				$upload_present = true;
			}
			
			foreach($db_fields as $field) {
				if(isset($_POST[$field])) {
					$$field = $db->escape_value(str_replace('?','',$_POST[$field]));
				}
			}
			
			$edited_entry = New Tag();
			
			foreach($db_fields as $field) {
				if(isset($$field)) {
					$edited_entry->$field = $$field;
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
								$upl_msg = "{$lang['alert-upload_error']}:<br>";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
				
				if(!empty($images)) {
					$final_string = implode("," , $images);
					
					$edited_entry->avatar = $final_string;
				}
			}
			
			if ($edited_entry->create()) {
				
				$msg = $lang['alert-create_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/topics']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/topics']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/topics']}?edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_topic'])) {
		if(!$current_user->can_see_this("admintopics.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			$db_fields = Array('name', 'description', 'upload_files');
			
			$upload_key = array_search('upload_files' , $db_fields);
			if($upload_key) {
				unset($db_fields[$upload_key]);
				$upload_present = true;
			}
			
			foreach($db_fields as $field) {
				if(isset($_POST[$field])) {
					$$field = $db->escape_value(str_replace('?','',$_POST[$field]));
				}
			}
			
			$edited_entry = Tag::get_specific_id($edit_id);
			$old_name = $edited_entry->name;
			
			foreach($db_fields as $field) {
				if(isset($$field)) {
					$edited_entry->$field = $$field;
				}
			}
			
			//echo $name;
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
								$upl_msg = "{$lang['alert-upload_error']}:<br>";	
								$upl_msg .= join("<br />" , $$string->errors);							
								$upload_problems = 1;
							}
						}
				endfor;
				
				if(!empty($images)) {
					$final_string = implode("," , $images);
				
					$edited_entry->avatar = $final_string;
				}
			}
			
			if ($edited_entry->update()) {
				if($old_name != $name) {
					$query = " AND feed LIKE '%{$old_name}%' ";
					$questions = Question::get_feed_for($current_user->id ,$query,"" );
					if($questions) {
						foreach($questions as $q) {
							$tags = explode(',' , $q->feed);
							
							foreach($tags as $k => $v) {
								if(strtolower($v) == strtolower($old_name)) {
									unset($tags[$k]);
									$tags[] = $name;
								}
							}
							$q->feed = implode(',' , $tags);
							$q->update();
						}
					}
				}
				$msg = $lang['alert-update_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/topics']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['admin/topics']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/topics']}?edit=fail&msg={$msg}");
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
				<?php if($current_user->can_see_this('admintopics.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/topics']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-topics']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admanager.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/ads']; ?>" class="col-md-12"><?php echo $lang['admin-section-admanager']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('profanity_filter.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/filter']; ?>" class="col-md-12"><?php echo $lang['admin-section-filter']; ?></a></li><?php } ?>
				
			</ul>
			
		</div></div>
	
	<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
	</div>
	
	
	<div class="col-md-9">
		<div class="card post-item"><div class="card-body">
			<h5><?php echo $lang['admin-topics-title']; ?>
				<?php
					$back = $url_mapper['admin/topics'];
					$add = $url_mapper['admin/topics'].'?type=new&hash='.$random_hash;
					if(isset($_GET['ref']) && $_GET['ref'] != '' ) { $back = $url_mapper['feed/']."{$_GET['ref']}/"; } 
				?>
				<?php if(isset($_GET['type']) && $_GET['type'] != '' ) { ?>
				<a href="<?php echo $back; ?>" class="btn btn-sm btn-primary"><i class="fe fe-arrow-<?php echo $lang['direction-left']; ?>"></i>&nbsp;<?php echo $lang['btn-back']; ?></a>
				<?php } else { ?>
				<a href="<?php echo $add; ?>" class="btn btn-sm btn-success"><i class="fe fe-plus"></i>&nbsp;<?php echo $lang['btn-add']; ?></a>
				<?php } ?>
				</h5>
				<hr>
				
				<?php if(isset($_GET['type']) && $_GET['type'] == 'new' ) {
				?>
				
				
				<form method="post" action="<?php echo $url_mapper['admin/topics']; ?>" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-md-6">
								<div class="form-group">
									<label for="name"><?php echo $lang['admin-topics-name']; ?></label>
									<input type="text" class="form-control" name="name" id="name" placeholder="Topic Name.." required value="">
								</div>
							<div class="form-group">
									<label class="col-form-label" for="img1_upl"><?php echo $lang['admin-topics-avatar']; ?></label><br>
									<img src="<?php echo WEB_LINK.'public/img/topic.png'; ?>" class="img-polaroid rounded-circle" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; width:64px; height:64px" id="img1">
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
								
							
						</div>
						<div class="col-md-6">
							<div class="form-group">
								
								<label for="description"><?php echo $lang['admin-topics-description']; ?></label>
								<textarea class="form-control" rows='5' name="description" id="description" placeholder="Topic Description.."></textarea>
								<br>
								
								
								</div>
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="add_topic" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
				
				
				<?php } elseif(isset($_GET['type']) && $_GET['type'] == 'edit'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					if(!Tag::check_id_existance($db->escape_value($_GET['id']))) {
						redirect_to($url_mapper['error/404/']);
					}
					$topic = Tag::get_specific_id($db->escape_value($_GET['id']));
					if($topic->avatar) {
						$img = File::get_specific_id($topic->avatar);
						$quser_avatar= WEB_LINK."public/".$img->image_path();
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'public/img/topic.png';
						}
					} else {
						$quser_avatar = WEB_LINK.'public/img/topic.png';
					}
				?>
				
				
				<form method="post" action="<?php echo $url_mapper['admin/topics']; ?>" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-md-6">
								<div class="form-group">
									<label for="name"><?php echo $lang['admin-topics-name']; ?></label>
									<input type="text" class="form-control" name="name" id="name" placeholder="Topic Name.." required value="<?php echo $topic->name; ?>">
								</div>
								<div class="form-group">
								<label class="col-form-label" for="img1_upl"><?php echo $lang['admin-topics-avatar']; ?></label><br>
									<img src="<?php echo $quser_avatar; ?>" class="img-polaroid rounded-circle" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; width:64px; height:64px" id="img1">
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
								
							
						</div>
						<div class="col-md-6">
							<div class="form-group">
								
								<label for="description"><?php echo $lang['admin-topics-description']; ?></label>
								<textarea class="form-control" rows='5' name="description" id="description" placeholder="Topic Description.."><?php echo strip_tags($topic->description); ?></textarea>
								<br>
								
								
								</div>
						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="edit_topic" value="<?php echo $lang['btn-submit']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$topic->id."\" readonly/>";
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
				
				
				
				
				<?php
				} elseif(isset($_GET['type']) && $_GET['type'] == 'delete'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					$id = $db->escape_value($_GET['id']);
					
					if(!Tag::check_id_existance($id)) {
						redirect_to("{$url_mapper['error/404/']}");
					}
					
					$this_obj = Tag::get_specific_id($id);
					
					if(!$current_user->can_see_this("admintopics.delete",$group)) {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/topics']}?edit=fail&msg={$msg}");
					}
					if($id == "1") {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/topics']}?edit=fail&msg={$msg}");
					}
					
					//Check Subscriptions!!
					$tags = FollowRule::get_subscriptions('tag',$this_obj->id , 'obj_id' , '');
					if($tags) {
						foreach($tags as $tag) {
							$tag->delete();
						}
					}
					if($this_obj->delete()) {
						$msg = $lang['alert-delete_success'];
						if(isset($_GET['ref']) && $_GET['ref'] == 'index' ) {
							redirect_to("{$url_mapper['index/']}?edit=success&msg={$msg}");
						} else {
							redirect_to("{$url_mapper['admin/topics']}?edit=success&msg={$msg}");
						}
					} else {
						$msg = $lang['alert-delete_failed'];
						redirect_to("{$url_mapper['admin/topics']}?edit=fail&msg={$msg}");
					}
					
					
				} else { ?>
				
					<table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th style='width:10px'>#</th>
                          <th><?php echo $lang['admin-topics-name']; ?></th><th><?php echo $lang['admin-topics-description']; ?></th>
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
							
							
							$query = '';
							if(isset($_GET['search']) && $_GET['search'] == true && isset($_GET['data']) ) {
								$query = " AND (name LIKE '%" . $db->escape_value($_GET['data']) .  "%' OR description LIKE '%" . $db->escape_value($_GET['data']) .  "%') ";
							}
							
							$total_count = Tag::count_everything(" AND deleted = 0 {$query} ");
							$pagination = new Pagination($page, $per_page, $total_count);
							$all_obj= Tag::get_everything(" AND deleted = 0 {$query} "," LIMIT {$per_page} OFFSET {$pagination->offset()} ");
						
							
							$i= (($page-1) * $per_page) + 1;
							
							
							foreach($all_obj as $obj) :
							
							if($obj->avatar) {
								$pic = "<a href=\"#modal-image-{$obj->id}\" data-toggle='modal' class='btn btn-sm btn-icon btn-rounded btn-warning' data-rel='tooltip' data-placement='top' title='Avatar' data-original-title='Avatar'><i class='fa fa-search'></i></a>";
							} else {
								$pic = "";
							}
							
							if($current_user->can_see_this("admintopics.update",$group)) {
								$edit = "<a href=\"{$url_mapper['admin/topics']}/?id={$obj->id}&type=edit&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-primary' data-toggle='tooltip' data-rel='' data-placement='top' title='Edit' data-original-title='Edit'  ><i class='fa fa-pencil'></i></a>";
							} else {
								$edit = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-rel='' data-toggle='tooltip' data-placement='top' title='Edit (unavailable)' data-original-title='Edit (unavailable)'  ><i class='fa fa-pencil'></i></a>";
							}
							
							if($current_user->can_see_this("admintopics.delete",$group)) {
								$delete = "<a href=\"{$url_mapper['admin/topics']}/?id={$obj->id}&type=delete&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-danger' data-rel='' data-placement='top' data-toggle='tooltip' title='delete' data-original-title='delete'   onclick=\"return confirm('Are you sure you want to delete this topic?');\" ><i class='fa fa-times'></i></a>";
							} else {
								$delete = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-toggle='tooltip' data-rel='' data-placement='top' title='delete (unavailable)' data-original-title='delete (unavailable)'  ><i class='fa fa-times'></i></a>";
							}
							
							
						?>
						<tr >
                          <td><?php echo $i; ?></td>
                          <td><?php echo $obj->name;?></td>
						  <td><?php echo $obj->description; ?></td>
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
								  <p><?php echo $obj->name; ?></p>
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
								$page_param = $url_mapper['admin/topics'];
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
									$page_param = $url_mapper['admin/topics'];
									$page_param .= "?page=";
									$page_param .= $p;

									echo "<a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\">{$p}</a>";
								}
							}
							if($pagination->has_next_page()) {
								$page_param = $url_mapper['admin/topics'];
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

<script type="text/javascript">
	$(document).ready(function() {
		$("#img1_upl").change(function(){
			readURL(this, 'img1');
		});
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>