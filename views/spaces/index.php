<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

if(isset($_POST['add_space'])) {
	if($_POST['hash'] == $_SESSION[$elhash]){
		unset($_SESSION[$elhash]);
		
		if(!$current_user->can_see_this("spaces.create",$group) ) {
			$msg = $lang['alert-restricted'];
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
				echo json_encode($response); die();
			} else {
				redirect_to($url_mapper['spaces/']."&edit=fail&msg={$msg}");
			}
		}
		
			$name = profanity_filter($db->escape_value($_POST['name']));
			$name = strip_tags($name);
			$tagline = profanity_filter($db->escape_value($_POST['tagline']));
			$tagline = strip_tags($tagline);
			$description = profanity_filter($db->escape_value($_POST['description']));
			$description = strip_tags($description);
			
			$slug = slugify($name);
			$slug_checker = Space::check_slug($slug);
			if($slug_checker) {
				$slug .= "-". (count($slug_checker) +1);
			}
			
			if(isset($_POST['tags']) && !empty($_POST['tags']) ) { 
				$tags = implode("," , $_POST['tags']);
				$tags = $db->escape_value($tags);
				if($tags == '') {
					$tags = 'General';	
				}
			} else {
				$tags = 'General';
			}
			
			if(isset($_POST['admins']) && !empty($_POST['admins']) ) { 
				$admins = $_POST['admins'];
				$admins[] = '-'. $current_user->id . '-';
				$admins = array_unique($admins);
				$admins = implode(',' , $admins);
				$admins = strip_tags($admins);
			} else { 
				$admins = '-'. $current_user->id . '-';
			}
			
			if(isset($_POST['moderators']) && !empty($_POST['moderators']) ) { 
				$moderators = $_POST['moderators'];
				$moderators = implode(',' , $moderators);
				$moderators = strip_tags($moderators);
			} else { 
				$moderators = '';
			}
			
			if(isset($_POST['contributors']) && !empty($_POST['contributors']) ) { 
				$contributors = $_POST['contributors'];
				$contributors = implode(',' , $contributors);
				$contributors = strip_tags($contributors);
			} else { 
				$contributors = '';
			}
			
			if( !$name ) {
				$msg = $lang['alert-create_failed'];
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($url_mapper['spaces/']."&edit=fail&msg={$msg}");
				}
			}
			
			$q = New Space();
			$q->user_id = $current_user->id;
			$q->name = $name;
			$q->tagline = $tagline;
			$q->description = $description;
			$q->slug = $slug;
			$q->admins = $admins;
			$q->moderators = $moderators;
			$q->contributors = $contributors;
			$q->created_at = strftime("%Y-%m-%d %H:%M:%S" , time());
			$q->feed = $tags;
			
			if(isset($_POST['open_post']) && $_POST['open_post'] == '1') {
				$q->open_post = "1";
			}
			
			if($q->create()) {
				
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				$s_link = $url_mapper['spaces/view'].$url_type;
				
				$msg = $lang['alert-create_success'];
				
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-success'].'!','type' => 'success' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($s_link."?edit=success&msg={$msg}");
				}
			} else {
				$msg = $lang['alert-create_failed'];
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($url_mapper['spaces/']."?edit=fail&msg={$msg}");
				}
			}
			
	} else {
		redirect_to($url_mapper['spaces/']);
	}
}

if(isset($_POST['edit_space'])) {
	if($_POST['hash'] == $_SESSION[$elhash]){
		unset($_SESSION[$elhash]);
		
		if(!$current_user->can_see_this("spaces.update",$group) ) {
			$msg = $lang['alert-restricted'];
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
				echo json_encode($response); die();
			} else {
				redirect_to($url_mapper['spaces/']."&edit=fail&msg={$msg}");
			}
		}
		
		$s_id = $_POST['space_id'];
		if(!Space::check_id_existance($s_id)) {
			redirect_to($url_mapper['spaces/']);
		}
		$space = Space::get_specific_id($s_id);
		if($space->admins) { $admins = explode(',' , $space->admins); } else { $admins = array(); }
		if($current_user->prvlg_group != '1' && $space->user_id != $current_user->id || $current_user->prvlg_group != '1' && !in_array("-".$current_user->id."-", $admins) ) {
			$msg = $lang['alert-restricted'];
			if(URLTYPE == 'slug') {$url_type = $space->slug;} else {$url_type = $space->id;}
			redirect_to($url_mapper['spaces/view'].$url_type."&edit=fail&msg={$msg}");
		}
		
			$name = profanity_filter($db->escape_value($_POST['name']));
			$name = strip_tags($name);
			$tagline = profanity_filter($db->escape_value($_POST['tagline']));
			$tagline = strip_tags($tagline);
			$description = profanity_filter($db->escape_value($_POST['description']));
			$description = strip_tags($description);
			
			if(isset($_POST['tags']) && !empty($_POST['tags']) ) { 
				$tags = implode("," , $_POST['tags']);
				$tags = $db->escape_value($tags);
				if($tags == '') {
					$tags = 'General';	
				}
			} else {
				$tags = 'General';
			}
			
			if(isset($_POST['admins']) && !empty($_POST['admins']) ) {
				$admins = $_POST['admins'];
				$admins[] = '-'. $current_user->id . '-';
				$admins = array_unique($admins);
				$admins = implode(',' , $admins);
				$admins = strip_tags($admins);
			} else { 
				$admins = '-'. $current_user->id . '-';
			}
			
			if(isset($_POST['moderators']) && !empty($_POST['moderators']) ) { 
				$moderators = $_POST['moderators'];
				$moderators = implode(',' , $moderators);
				$moderators = strip_tags($moderators);
			} else { 
				$moderators = '';
			}
			
			if(isset($_POST['contributors']) && !empty($_POST['contributors']) ) { 
				$contributors = $_POST['contributors'];
				$contributors = implode(',' , $contributors);
				$contributors = strip_tags($contributors);
			} else { 
				$contributors = '';
			}
			
			if( !$name ) {
				$msg = $lang['alert-update_failed'];
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($url_mapper['spaces/']."&edit=fail&msg={$msg}");
				}
			}
			
			$space->user_id = $current_user->id;
			$space->name = $name;
			$space->tagline = $tagline;
			$space->description = $description;
			$space->admins = $admins;
			$space->moderators = $moderators;
			$space->contributors = $contributors;
			$space->created_at = strftime("%Y-%m-%d %H:%M:%S" , time());
			$space->feed = $tags;
			
			if(isset($_POST['open_post']) && $_POST['open_post'] == '1') {
				$space->open_post = "1";
			}
			
			if($space->update()) {
				
				if(URLTYPE == 'slug') {$url_type = $space->slug;} else {$url_type = $space->id;}
				$s_link = $url_mapper['spaces/view'].$url_type;
				
				$msg = $lang['alert-update_success'];
				
				if(URLTYPE == 'slug') {$url_type = $space->slug;} else {$url_type = $space->id;}
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-success'].'!','type' => 'success' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($s_link."?edit=success&msg={$msg}");
				}
			} else {
				$msg = $lang['alert-update_failed'];
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($url_mapper['spaces/']."?edit=fail&msg={$msg}");
				}
			}
			
	} else {
		redirect_to($url_mapper['spaces/']);
	}
}

$scope = 'all';

require_once(VIEW_PATH.'pages/header.php'); ?>
<?php require_once(VIEW_PATH.'pages/navbar.php'); ?>

<div class="container">		

<div class="row">
	<div class="posts_container col-12 col-lg-9" style="overflow:hidden">
		
		<?php
		if($current_user->can_see_this('index.post',$group)) {
		?>
		<div class="card post-item">
		  <div class="card-body">
			<a href="#me" class="add-space btn btn-rounded btn-danger btn-pill pull-<?php echo $lang['direction-right']; ?>"><i class="fe fe-plus"></i> <?php echo $lang['index-spaces-add']; ?></a>
			<h6 class="card-subtitle text-muted"><img src="<?php echo $current_user->get_avatar(); ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:40px;margin-<?php echo $lang['direction-right']; ?>:10px"> <?php echo $current_user->f_name . ' ' . $current_user->l_name; ?></h6>
			<h4 class="m-0"><?php echo $lang['index-spaces-your_spaces']; ?></h4>
			<?php 
			$spaces = FollowRule::get_subscriptions('space',$current_user->id , 'user_id' , '' , 'LIMIT 10');
			if($spaces) {
				echo "<br><ul class='nav-ul'>";
				foreach($spaces as $follow) {
					$space = Space::get_specific_id($follow->obj_id);
					if(URLTYPE == 'id') {
						$space_url = $url_mapper['spaces/view']. $space->id;
					} else {
						$space_url = $url_mapper['spaces/view']. $space->slug;
					}
					
					if($space->avatar) {
						$img = File::get_specific_id($space->avatar);
						$quser_avatar = WEB_LINK."public/".$img->image_path();
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (file_exists($quser_avatar_path)) {
							$icon= "<img src='{$quser_avatar}' style='height:30px; padding-{$lang['direction-right']}: 5px'>";
						} else {
							$url = WEB_LINK."public/img/space.png";
							$icon= "<img src='{$url}' style='height:30px; padding-{$lang['direction-right']}: 5px'>";
						}					
					} else {
						$url = WEB_LINK."public/img/space.png";
						$icon= "<img src='{$url}' style='height:30px; padding-{$lang['direction-right']}: 5px'>";
					}
					
					
					
					echo "<li style='font-size:20px'><a href='{$space_url}'>{$icon} {$space->name}</a></li>";
				}
				echo "</ul>";
			} else { ?>
				<br><h5 style="color:#b0b0b0; padding-<?php echo $lang['direction-left']; ?>: 40px"  ><i class="fe fe-code"></i>&nbsp;<?php echo $lang['index-spaces-no_subscriptions']; ?></h5>
			<?php }
			?>
		  </div>
		</div><hr>
		<?php
		}
		
		//get tagcloud
		if(isset($_GET['feed']) && $_GET['feed'] != '' ) {
			$tag = $db->escape_value($_GET['feed']);
			$tags = array($tag);
			$limit = '';
		} else {
			$tags = Space::get_tagcloud();
			$limit = 'LIMIT 6';
		}
		
		if($tags && $tags[0] != '' ) {
			foreach($tags as $tag) {
				if($tag != '') {					
					$count = Space::count_everything(" AND feed LIKE '%{$tag}%' ");
					$spaces = Space::get_everything(" AND feed LIKE '%{$tag}%' {$limit}");
					if($spaces) { echo "<div class='row'><div class='col-md-12 pb-3'><h4>{$tag}</h4></div>";
						foreach($spaces as $space) {
						
						if(URLTYPE == 'id') {
							$space_url = $url_mapper['spaces/view']. $space->id;
						} else {
							$space_url = $url_mapper['spaces/view']. $space->slug;
						}
						?>
			<div class="col-md-4">
			<div class="card hovercard ">
                <div class="cardheader" style="background:url('<?php echo $space->get_cover(); ?>')">
                </div>
                <div class="avatar">
                    <img alt="" src="<?php echo $space->get_avatar(); ?>">
                </div>
                <div class="info">
                    <div class="title">
                        <a target="" href="<?php echo $space_url; ?>"><?php echo $space->name; ?></a>
                    </div>
                    <div class="desc"><?php echo $space->tagline; ?></div><br>
                </div>
                <?php if($current_user->can_see_this('questions.interact', $group)) { ?><div class="bottom question-like-machine">
					<?php 
					$follow_class = 'follow';
					$follow_txt = $lang['btn-follow'];
					$followed = FollowRule::check_for_obj('space' , $space->id, $current_user->id);
					if($followed) {
						$follow_txt = $lang['btn-followed'];
						$follow_class = 'active unfollow';
					}
					?>
                    <a href="javascript:void(0);" class="btn btn-block btn-rounded btn-pill btn-primary <?php echo $follow_class; ?>" data-obj="space" name="<?php echo $space->id; ?>" data-lbl="<?php echo $lang['btn-follow'] ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" value="<?php echo $space->follows; ?>" ><i class='fe fe-user-plus'></i> <?php echo $follow_txt; if($space->follows) { echo ' · ' . convert_to_k($space->follows); } ?></a>
                </div><?php } ?>
            </div>
            </div>
						
						<?php }
						if($count > 6 && $limit != '' ) {
							$link = $url_mapper['spaces/'].'?feed='.$tag;
							echo "<div class='col-md-12 pb-5'><a href='{$link}' class='btn btn-block '>{$lang['btn-view_more']} <i class='fe fe-chevron-down'></i></a></div>";
						}
					echo "</div><br>"; } else { ?>
						<br><h3 style="color:#b0b0b0" id='stop-loading'><center><i class="fe fe-code"></i><br><?php echo $lang['index-spaces-no_spaces']; ?><br><small><a href='#me' class='add-space' style='color: #b92b27'><?php echo $lang['index-spaces-add']; ?></a></small></center></h3><br>
					<?php }
				}
			}
		} else { ?>
			<br><h3 style="color:#b0b0b0;" id='stop-loading'><center><i class="fe fe-code"></i><br><?php echo $lang['index-spaces-no_spaces']; if($current_user->can_see_this("questions.create", $group)) { ?><br><small><a href='#me' class='add-space' style='color: #b92b27'><?php echo $lang['index-spaces-add']; ?></a></small><?php } ?></center></h3><br>
		<?php } ?>
		
		
		
	</div>
	
	<?php require_once(VIEW_PATH.'pages/rt_sidebar.php'); ?>
	
</div>
	</div> <!-- /container -->
	
    <?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
	<?php require_once(VIEW_PATH.'pages/like-machine.php'); ?>
	<?php require_once(VIEW_PATH.'pages/footer.php'); ?>
	
<script src="<?php echo WEB_LINK; ?>public/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<script>

$(document).on('click' , 'a.add-space' , function(){
	var preloader = '<div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-body box-body"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" alt="" style="height:6px"></center><br></div></div></div>';
	$('.modal-receptor').html(preloader); 
	$('.modal-receptor').modal();
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=add_space", {id: '<?php echo $current_user->id; ?>' , data: 'new_space' , hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		parseThisTo(data,'.modal-receptor');
		$('.modal-receptor').modal(); 
	});
});
$(document).on('click' , 'a.add-q' , function(){
	var preloader = '<div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-body box-body"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" alt="" style="height:6px"></center><br></div></div></div>';
	$('.modal-receptor').html(preloader); 
	$('.modal-receptor').modal();
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=add_question", {id: '<?php echo $current_user->id; ?>' , data: 'new_question' , hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		parseThisTo(data,'.modal-receptor');
		$('.modal-receptor').modal(); 
	});
});
</script>
<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>