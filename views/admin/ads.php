<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");

if (isset($_POST['add_adblock'])) {
		if(!$current_user->can_see_this("admanager.create",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$name = $db->escape_value(strip_tags($_POST["name"]));
			$link = urlencode($_POST["link"]);
			$content = strip_tags($_POST['content'],'<a><img><b><i><p><u><ol><ul><li><iframe>');
			$expiry = $db->escape_value($_POST["expiry"]);
			$location = implode(',' , $_POST["location"]);
			
			$new_entry = New AdManager();
			$new_entry->name = $name;
			$new_entry->link = $link;
			$new_entry->content = $content;
			$new_entry->expiry = $expiry;
			$new_entry->created_at = now();
			$new_entry->location = $location;
			
			if ($new_entry->create()) {
			
				$msg = $lang['alert-create_success'];
				redirect_to("{$url_mapper['admin/ads']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-create_failed'];
				redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
		}
}

if (isset($_POST['edit_adblock'])) {
		if(!$current_user->can_see_this("admanager.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			$name = $db->escape_value(strip_tags($_POST["name"]));
			$link = urlencode($_POST["link"]);
			$content = strip_tags($_POST['content'],'<a><img><b><i><p><u><ol><ul><li><iframe>');
			$expiry = $db->escape_value($_POST["expiry"]);
			$location = implode(',' , $_POST["location"]);
			
			$new_entry = AdManager::get_specific_id($edit_id);
			
			$new_entry->name = $name;
			$new_entry->link = $link;
			$new_entry->content = $content;
			$new_entry->expiry = $expiry;
			$new_entry->location = $location;
			
			if ($new_entry->update()) {
			
				$msg = $lang['alert-update_success'];
				redirect_to("{$url_mapper['admin/ads']}?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
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
				<?php if($current_user->can_see_this('admintopics.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/topics']; ?>" class="col-md-12 "><?php echo $lang['admin-section-topics']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admanager.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/ads']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-admanager']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('profanity_filter.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/filter']; ?>" class="col-md-12"><?php echo $lang['admin-section-filter']; ?></a></li><?php } ?>
				
			</ul>
			
		</div></div>
	
	<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
	</div>
	
	
	<div class="col-md-9">
		<div class="card post-item"><div class="card-body">
			
			<h5><?php echo $lang['admin-section-admanager']; ?>&nbsp;&nbsp;
			<?php if(isset($_GET['type']) && $_GET['type'] != '' ) { ?>
			<a href='<?php echo $url_mapper['admin/ads']; ?>' class="btn btn-sm btn-primary"><i class='fe fe-arrow-<?php echo $lang['direction-left']; ?>'></i>&nbsp;<?php echo $lang['btn-back']; ?></a>
			<?php } else { ?>
			<a href='<?php echo $url_mapper['admin/ads'] . '?type=new'; ?>' class="btn btn-sm btn-success"><i class='fe fe-plus'></i>&nbsp;<?php echo $lang['btn-add']; ?></a>
			<?php } ?></h5>
				<?php if(isset($_GET['type']) && $_GET['type'] == 'new' ) {
				?>
				<hr>
				<form id="form-validation" action="<?php echo $url_mapper['admin/ads']; ?>" method="post" class="">
					<div class="row">
						<div class="col-md-6">
						  <div class="form-group">
							<label for="name" class="col-form-label">Name</label>
							<input type="text" class="form-control form-white" id="name" name="name" placeholder="Name" required >
						  </div>
						</div>
						<div class="col-md-6">
						<div class="form-group">
							<label for="link" class="col-form-label">Ad Link</label>
							<input type="text" class="form-control form-white" id="link" name="link" placeholder="Link">
						  </div>
						</div>
						  
						<div class="col-md-12">
							<textarea class="summernote2" name="content"></textarea>
						</div>
						
						<div class="col-md-6">
						<div class="form-group">
							<label for="expiry" class="col-form-label">Ad Location</label>
							<select id="location" name="location[]" class="form-control select2" style="" data-placeholder="Ad location.." multiple>
								<option value="between_questions">Between Questions</option>
								<option value="between_answers">Between Answers</option>
								<option value="left_sidebar">Left Sidebar</option>
								<option value="right_sidebar">Right Sidebar</option>
							</select>
						  </div>
						</div>
						
						<div class="col-md-6">
						<div class="form-group">
							<label for="expiry" class="col-form-label">Expire after</label>
							<select id="expiry" name="expiry" class="form-control select2" style="" data-placeholder="Expire after..">
								<option value="0">Never</option>
								<?php 
								$arr = Array("1 Day","2 Days","3 Days","4 Days","5 Days","6 Days", "1 Week", "2 Weeks", "3 Weeks", "1 Month", "2 Months", "3 Months", "4 Months", "5 Months", "6 Months");
									
									foreach($arr as $a) {
										echo "<option value='{$a}' "; 
										echo ">{$a}</option>";
									}
								?>
							</select>
						  </div>
						</div>
						
						
						<br style="clear:both"/>
						
						
					
					<?php	
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>"				
					?>
					</div>
					<br><div class='pull-<?php echo $lang['direction-right']; ?>' >
						<button class="btn btn-success" name="add_adblock" type="submit" ><?php echo $lang['btn-submit']; ?></button>
						<a href='<?php echo $url_mapper['admin/ads']; ?>' class="btn btn-danger" ><?php echo $lang['btn-cancel']; ?></a>	
					</div>
					</form>
				<?php
				} elseif(isset($_GET['type']) && $_GET['type'] == 'edit'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					
					if(!AdManager::check_id_existance($db->escape_value($_GET['id']))) {
						redirect_to($url_mapper['error/404/']);
					}
					$this_obj = AdManager::get_specific_id($db->escape_value($_GET['id']));
				?>
					
					
					<hr>
				<form id="form-validation" action="<?php echo $url_mapper['admin/ads']; ?>" method="post" class="">
						
				<div class="row">
						<div class="col-md-6">
						  <div class="form-group">
							<label for="name" class="col-form-label">Name</label>
							<input type="text" class="form-control form-white" id="name" name="name" placeholder="Name" value="<?php echo $this_obj->name; ?>" required >
						  </div>
						</div>
						<div class="col-md-6">
						<div class="form-group">
							<label for="link" class="col-form-label">Ad Link</label>
							<input type="text" class="form-control form-white" id="link" name="link" placeholder="Link"  value="<?php echo urldecode($this_obj->link); ?>" >
						  </div>
						</div>
						  
						<div class="col-md-12">
							<textarea class="summernote2" name="content"><?php $content = str_replace('\\','',$this_obj->content);
							$content = str_replace('<script','',$content);
							$content = str_replace('</script>','',$content);
							echo $content;
							?></textarea>
						</div>
						
						<div class="col-md-6">
						<div class="form-group">
							<label for="expiry" class="col-form-label">Ad Location</label>
							<?php $arr = explode(',', $this_obj->location ); ?>
							<select id="location" name="location[]" class="form-control select2" style="" data-placeholder="Ad location.." multiple>
								<option value="between_questions" <?php if(in_array('between_questions', $arr)) { echo 'selected'; } ?> >Between Questions</option>
								<option value="between_answers" <?php if(in_array('between_answers', $arr)) { echo 'selected'; } ?> >Between Answers</option>
								<option value="left_sidebar" <?php if(in_array('left_sidebar', $arr)) { echo 'selected'; } ?> >Left Sidebar</option>
								<option value="right_sidebar" <?php if(in_array('right_sidebar', $arr)) { echo 'selected'; } ?> >Right Sidebar</option>
							</select>
						  </div>
						</div>
						
						<div class="col-md-6">
						<div class="form-group">
							<label for="expiry" class="col-form-label">Expire after</label>
							<select id="expiry" name="expiry" class="form-control select2" style="" data-placeholder="Expire after..">
								<option value="0" <?php if($this_obj->expiry == '0') { echo 'selected'; } ?>>Never</option>
								<?php 
									$arr = Array("1 Day","2 Days","3 Days","4 Days","5 Days","6 Days", "1 Week", "2 Weeks", "3 Weeks", "1 Month", "2 Months", "3 Months", "4 Months", "5 Months", "6 Months");
									
									foreach($arr as $a) {
										echo "<option value='{$a}' "; 
											if($this_obj->expiry == $a) {
												echo ' selected';
											}
										echo ">{$a}</option>";
									}
								?>
							</select>
						  </div>
						</div>
						
						
						<br style="clear:both"/>
						
						
					
					<?php	
					echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$this_obj->id."\" readonly/>";
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>"				
					?>
					
					</div>
					<br><div class='pull-<?php echo $lang['direction-right']; ?>' >
						<button class="btn btn-success" name="edit_adblock" type="submit" ><?php echo $lang['btn-submit']; ?></button>
						<a href='<?php echo $url_mapper['admin/ads']; ?>' class="btn btn-danger" ><?php echo $lang['btn-cancel']; ?></a>	
					</div>
					</form>
					
				<?php
				} elseif(isset($_GET['type']) && $_GET['type'] == 'delete'  && isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['hash'])) {
					$id = $db->escape_value($_GET['id']);
					
					if(!AdManager::check_id_existance($id)) {
						redirect_to("{$url_mapper['error/404/']}");
					}
					
					$this_obj = AdManager::get_specific_id($id);
					
					if(!$current_user->can_see_this("admanager.delete",$group)) {
						$msg = $lang['alert-restricted'];
						redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
					}
					
					if($this_obj->delete()) {
						$msg = $lang['alert-delete_success'];
						Log::log_action($current_user->id , "Delete Ad" , "Delete Ad named ({$this_obj->name}) - id #({$this_obj->id})");
						redirect_to("{$url_mapper['admin/ads']}?edit=success&msg={$msg}");
					} else {
						$msg = $lang['alert-delete_failed'];
						redirect_to("{$url_mapper['admin/ads']}?edit=fail&msg={$msg}");
					}
					
					
				} else { ?>
				
				
				<table class="table table-hover table-bordered custom_table">
                      <thead>
                        <tr>
                          <th style='width:10px'>#</th>
                          <th>Name</th>
                          <th>Ad</th>
                          <th>Location</th>
                          <th>Views</th>
                          <th>Clicks</th>
                          <th>Expiry</th>
                          <th style='width:150px'><i class="fe fe-settings"></i></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
							
							$all_obj= AdManager::get_everything("");
							
							$i= 1;
							foreach($all_obj as $obj) :
							
							if($current_user->can_see_this("admanager.update",$group)) {
								$edit = "<a href=\"{$url_mapper['admin/ads']}?id={$obj->id}&type=edit&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-primary' data-toggle='tooltip' data-placement='top' title='Edit' data-original-title='Edit'  ><i class='fa fa-pencil'></i></a>";
							} else {
								$edit = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-toggle='tooltip' data-placement='top' title='Edit (Unavailable)' data-original-title='Edit (Unavailable)'  ><i class='fa fa-pencil'></i></a>";
							}
							
							if($current_user->can_see_this("admanager.delete",$group)) {
								$delete = "<a href=\"{$url_mapper['admin/ads']}?id={$obj->id}&type=delete&hash={$random_hash}\" class='btn btn-sm btn-icon btn-rounded btn-danger' data-toggle='tooltip' data-placement='top' title='Delete' data-original-title='Delete'   onclick=\"return confirm('Are you sure you want to delete this ad?');\" ><i class='fa fa-times'></i></a>";
							} else {
								$delete = "<a href=\"javascript:void(0);\" class='btn btn-sm btn-icon btn-rounded btn-secondary' data-toggle='tooltip' data-placement='top' title='Delete (Unavailable)' data-original-title='Delete (Unavailable)'  ><i class='fa fa-times'></i></a>";
							}
							
							$expiry_str = time();
							
							if($obj->expiry != '' && $obj->expiry != '0') {
								$created = strtotime($obj->created_at);
								$expiry_str = strtotime("+{$obj->expiry}" , $created );
								$expiry = strftime("%Y-%m-%d %H:%M:%S" , $expiry_str);
								$expiry = date_to_eng($expiry);
							} else {
								$expiry = "Never";
							}
							
							
							$content = str_replace('\\','',$obj->content);
							$content = str_replace('<script','',$content);
							$content = str_replace('</script>','',$content);
							
							$location = explode(',' , $obj->location);
							
							$expired = false;
							
							if($obj->expiry != '0' && $obj->expiry != '' && $expiry_str < time()) {
								$expired = true;
							}
							
					?>
						<tr <?php if($expired) { echo ' style="background-color:#ebcccc; color:#a94442"'; } ?> >
                          <td><?php echo $i; ?></td>
                          
						  <td><?php echo $obj->name; if($expired) {  echo "<br><p class='label label-danger'>Expired</p>"; } ?></td>
						  <td><?php echo $content; ?></td>
						  <td><?php foreach($location as $k => $v) { echo "<p class='label label-success'>"; echo str_replace("_", " " , $v); echo "</p><br>"; } ?></td>
						  <td><?php echo $obj->views; ?></td>
						  <td><?php echo $obj->clicks; ?></td>
						  <td><?php echo $expiry; ?></td>
                          
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-bs4.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('select').select2();
		$('.summernote2').summernote({
			height: 100,
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
		$(document).ready(function(){
			$("img").addClass("img-small");
		});
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>