<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

$data = $q_slug;
if(URLTYPE == 'id') {
	$space = Space::get_specific_id($data);
} else {
	$space = Space::get_slug($data);
}

if($space) {
	if(URLTYPE == 'slug') {$url_type = $space->slug;} else {$url_type = $space->id;}
	
	$title= strip_tags($space->name);
	if($space->admins) { $admins = explode(',' , $space->admins); } else { $admins = array(); }
	if($space->moderators) { $moderators = explode(',' , $space->moderators); } else { $moderators = array(); }
	if($space->contributors) { $contributors = explode(',' , $space->contributors); } else { $contributors = array(); }
	
	if($space->avatar) {
		$img = File::get_specific_id($space->avatar);
		$quser_avatar = WEB_LINK."public/".$img->image_path();
		$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
		if (!file_exists($quser_avatar_path)) {
			$quser_avatar = WEB_LINK.'public/img/space.png';
		}
	} else {
		$quser_avatar = WEB_LINK.'public/img/space.png';
	}
	
	if(URLTYPE == 'slug') {$url_type = $space->slug; $s_link = $url_mapper['spaces/view']. $space->slug;} else {$url_type = $space->id; $s_link = $url_mapper['spaces/view']. $s->id;}
} else {
	redirect_to($url_mapper['error/404/']);
}

if (isset($_POST['submit_report'])) {
	
	if($_POST['hash'] != $_SESSION[$elhash] ) {
		redirect_to($url_mapper['index/']);
	}
	
	$id = $_POST['id'];
	$obj_type = $_POST['obj_type'];
	$info = $_POST['info'];
	
	$report = new Report();
	$report->obj_id = $id;
	$report->obj_type = $obj_type;
	$report->info = profanity_filter(strip_tags($info));
	$report->user_id = $current_user->id;
	$report->report_date = strftime("%Y-%m-%d %H:%M:%S" , time());
	
	if($report->create()) {
		$msg = $lang['alert-report_success'];
		if(isset($_GET['ref']) && $_GET['ref'] != '') {
			$ref = $db->escape_value($_GET['ref']).'/';
			redirect_to($url_mapper[$ref]."?edit=success&msg={$msg}");
		} else {
			if(URLTYPE == 'slug') {$url_type = $space->slug;} else {$url_type = $space->id;}
			redirect_to($url_mapper['spaces/view']."{$url_type}/?edit=success&msg={$msg}");
		}
	} else {
		$msg = $lang['alert-report_failed'];
		redirect_to($url_mapper['questions/create']."/?edit=fail&msg={$msg}");
	}
	
}
require_once(VIEW_PATH.'pages/header.php'); ?>
<?php require_once(VIEW_PATH.'pages/navbar.php'); ?>

<div class="container">		

<div class="row">
	<div class="posts_container col-lg-8" style="">
		<div class="card hovercard full-page">
                <div class="cardheader" style="background:url('<?php echo $space->get_cover(); ?>')">
                </div>
                <div class="avatar">
                    <img alt="" src="<?php echo $space->get_avatar(); ?>">
                </div>
                <div class="info">
                    <div class="title" style='font-size:24px'>
                        <a href="<?php echo $s_link; ?>"><?php echo $space->name; ?></a>&nbsp;
		<?php 
			@$tags = explode(",",$space->feed); 
			if(is_array($tags)) {
				foreach($tags as $tag) {
		?>
		<a href="<?php echo $url_mapper['spaces/'].'?feed='.$tag; ?>" class="btn btn-sm btn-light"><?php echo $tag; ?></a>
		<?php }} ?>
                    </div>
					<p class="text-muted" style="font-size:20px">space/<?php echo $url_type; ?></p>
                    <div class="desc" style='font-size:20px; color: black'><?php echo $space->tagline; ?></div><br>
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
                    <div class="btn-group">
					<a href="javascript:void(0);" class="btn btn-rounded btn-pill btn-sm btn-primary <?php echo $follow_class; ?>" data-obj="space" name="<?php echo $space->id; ?>" data-lbl="<?php echo $lang['btn-follow'] ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" value="<?php echo $space->follows; ?>" ><i class='fe fe-user-plus'></i> <?php echo $follow_txt; if($space->follows) { echo ' · ' . convert_to_k($space->follows); } ?></a>
					<?php 
					if($space->open_post || in_array("-".$current_user->id."-", $admins) || in_array("-".$current_user->id."-", $moderators) || in_array("-".$current_user->id."-", $contributors) ) {
					?>
                    <a href="javascript:void(0);" class="btn btn-rounded btn-sm btn-pill btn-primary add-q" style="margin:0" ><i class='fe fe-plus'></i> <?php echo $lang['spaces-add_q']; ?></a>
					
                    <a href="javascript:void(0);" class="btn btn-rounded btn-pill btn-sm btn-primary add-p" ><i class='fe fe-plus'></i> <?php echo $lang['spaces-add_p']; ?></a>
					
					<?php } ?>
					
					</div>
		<div class="btn-group pull-<?php echo $lang['direction-right']; ?>" role="group" aria-label="question-tools">
			
			<a href="javascript:void(0);" class="comment btn btn-icon btn-like-machine" data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-views']; ?>"><i class="fe fe-eye"></i><?php if($space->views) { echo "&nbsp;". convert_to_k($space->views); }?></a>
				
			<div class="dropdown">
			  <button class="btn btn-icon " type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
				<i class="fe fe-repeat"></i></button>
			  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($s_link); ?>&quote=<?php echo urlencode("Check out this interesting space: {$space->name}"); ?>" class="share dropdown-item" target="_blank" ><i class='fa fa-facebook' style="color: #3b5998"></i> Facebook</a>
				<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($s_link); ?>&text=<?php echo urlencode("Check out this interesting space: {$space->name}"); ?>" class="share dropdown-item" target="_blank" ><i class='fa fa-twitter' style="color: #00acee"></i> Twitter</a>
				<a href="javascript:void(0);" class="dropdown-item copy-link" data-link="<?php echo $s_link; ?>"><i class='fe fe-link' ></i> Copy Link</a>
			  </div>
			</div>
				
				<div class="dropdown">
				  <button class="btn btn-icon btn-like-machine" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fe fe-more-horizontal"></i>
				  </button>
				  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					<?php if($space->user_id == $current_user->id || $current_user->prvlg_group == '1' || in_array("-".$current_user->id."-", $admins) ) { ?>
						<?php if($current_user->can_see_this('spaces.update' , $group)) { ?><a href="#me" class="dropdown-item edit_space"><?php echo $lang['spaces-edit']; ?></a><?php } ?>
						<?php if($current_user->can_see_this('spaces.delete' , $group)) { ?><a href="<?php echo $url_mapper['spaces/delete']. $url_type."&hash={$random_hash}"; ?>" onClick="return confirm('<?php echo $lang['question-delete-alert']; ?>');" class="dropdown-item"><?php echo $lang['spaces-delete']; ?></a><?php } ?>
					<div class="dropdown-divider"></div>
					<?php } ?>
					<?php $reported = Report::check_for_obj('space' , $space->id, $current_user->id); ?>
					<?php if(!$reported) { ?>
					<a href="#report-q-<?php echo $space->id; ?>" data-toggle="modal" class="dropdown-item"><?php echo $lang['spaces-report']; ?></a>
					<?php } else { ?>
					<a href="javascript:void(0);" class="dropdown-item text-muted text-center " style="width:250px; white-space: normal"><?php echo $lang['questions-report-reported']; ?></a>
					<?php } ?>
				  </div>
				</div>
				
			</div>
					
                </div>
				
				<?php if(!$reported) { ?>
					<!-- Modal -->
					<div class="modal fade in" id="report-q-<?php echo $space->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel"><?php echo $lang['spaces-report']; ?></h5>
						  </div>
						  <form action="<?php  echo $url_mapper['spaces/view']; echo $url_type; ?>" method="POST" >
						  <div class="modal-body">
								<div class="form-group">
									<div class="flag_reasons clearfix">
										<?php $reasons = $lang['questions-report-types']; 
										foreach($reasons as $reason) {
											$line= explode(':' , $reason);
										?>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="<?php echo $reason; ?>">
											<b><?php echo $line[0]; ?>:</b> <span class="light_gray"><?php echo $line[1]; ?></span>
										  </label>
										</div>
										<?php } ?>
									</div>
								</div>
								
						  </div>
						  
						  <div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $lang['btn-cancel']; ?></button>
							<button type="submit" name="submit_report" class="btn btn-primary"><?php echo $lang['btn-submit']; ?></button>
						  </div>
							<?php 
								$_SESSION[$elhash] = $random_hash;
								echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
								echo "<input type=\"hidden\" name=\"id\" value=\"".$space->id."\" readonly/>";
								echo "<input type=\"hidden\" name=\"obj_type\" value=\"space\" readonly/>";
							?>
						  </form>
						</div>
					  </div>
					</div>
				<?php } } ?>
				
            </div>
			
			<hr>
			<?php if(isset($_GET['section']) && $_GET['section'] == 'people' ) {
			$section = 'people';
			
			if($space->admins) {
				echo '<h4>Admins</h4><div class="row">';
				foreach($admins as $admin) {
					$admin_id = str_replace('-', '' , $admin);
					$u = User::get_specific_id($admin_id);
					if($u->avatar) {
						$img = File::get_specific_id($u->avatar);
						$quser_avatar = WEB_LINK."public/".$img->image_path();
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'public/img/avatar.png';
						}
					} else {
						$quser_avatar = WEB_LINK.'public/img/avatar.png';
					}
					
					$comment = $u->comment;
					if (strlen($u->comment) > 60) {
						$stringCut = substr($u->comment, 0, 60);
						$comment = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
					}
					
					$u_follow_class = 'follow';
					$follow_txt = '';
					$followed = FollowRule::check_for_obj('user' , $u->id, $current_user->id);
					if($followed) {
						$follow_txt = '';
						$u_follow_class = 'active unfollow';
					}
				?>
				<div class="question-element question-like-machine col-6">
					<div class="card" style="min-height:99px">
						<div class="card-body">
							<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
								<p class="name">
									<b><a href="<?php echo $url_mapper['users/view'] . $u->id; ?>/"><?php echo $u->f_name . " " . $u->l_name; ?></a></b>&nbsp;&nbsp;
									<?php if($current_user->id != $u->id && $current_user->can_see_this('users.follow' , $group )) { ?><a href="javascript:void(0);" class="btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" name="<?php echo $u->id; ?>" value="<?php echo $u->follows; ?>" data-obj="user" data-lbl="" data-lbl-active="" ><i class="fe fe-user-plus"></i><?php echo $follow_txt; ?> | <?php echo $u->follows; ?></a><?php } ?>
									<br><small><?php echo $comment; ?></small>
								</p>
						</div>
					</div>
				</div>
			<?php
				}  echo '</div><hr class="clearfix">';
			}
			if($space->moderators) {
				echo '<h4>Moderators</h4><div class="row">';
				foreach($moderators as $moderator) {
					$moderator_id = str_replace('-', '' , $moderator);
					$u = User::get_specific_id($moderator_id);
					if($u->avatar) {
						$img = File::get_specific_id($u->avatar);
						$quser_avatar = WEB_LINK."public/".$img->image_path();
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'public/img/avatar.png';
						}
					} else {
						$quser_avatar = WEB_LINK.'public/img/avatar.png';
					}
					
					$comment = $u->comment;
					if (strlen($u->comment) > 60) {
						$stringCut = substr($u->comment, 0, 60);
						$comment = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
					}
					
					$u_follow_class = 'follow';
					$follow_txt = '';
					$followed = FollowRule::check_for_obj('user' , $u->id, $current_user->id);
					if($followed) {
						$follow_txt = '';
						$u_follow_class = 'active unfollow';
					}
				?>
				<div class="question-element question-like-machine col-6" >
					<div class="card" style="min-height:99px">
						<div class="card-body">
							<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
								<p class="name">
									<b><a href="<?php echo $url_mapper['users/view'] . $u->id; ?>/"><?php echo $u->f_name . " " . $u->l_name; ?></a></b>&nbsp;&nbsp;
									<?php if($current_user->id != $u->id && $current_user->can_see_this('users.follow' , $group )) { ?><a href="javascript:void(0);" class="btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" name="<?php echo $u->id; ?>" value="<?php echo $u->follows; ?>" data-obj="user" data-lbl="" data-lbl-active="" ><i class="fe fe-user-plus"></i><?php echo $follow_txt; ?> | <?php echo $u->follows; ?></a><?php } ?>
									<br><small><?php echo $comment; ?></small>
								</p>
						</div>
					</div>
				</div>
			<?php
				}  echo '</div><hr class="clearfix">';
			}
			if($space->contributors) {
				echo '<h4>Contributors</h4>';
				foreach($contributors as $contributor) {
					$contributor_id = str_replace('-', '' , $contributor);
					$u = User::get_specific_id($contributor_id);
					if($u->avatar) {
						$img = File::get_specific_id($u->avatar);
						$quser_avatar = WEB_LINK."public/".$img->image_path();
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'public/img/avatar.png';
						}
					} else {
						$quser_avatar = WEB_LINK.'public/img/avatar.png';
					}
					
					$comment = $u->comment;
					if (strlen($u->comment) > 60) {
						$stringCut = substr($u->comment, 0, 60);
						$comment = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
					}
					
					$u_follow_class = 'follow';
					$follow_txt = '';
					$followed = FollowRule::check_for_obj('user' , $u->id, $current_user->id);
					if($followed) {
						$follow_txt = '';
						$u_follow_class = 'active unfollow';
					}
				?>
				<div class="question-element question-like-machine" style="float:<?php echo $lang['direction-left']; ?>; width:49%">
					<div class="card" style="min-height:99px">
						<div class="card-body">
							<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
								<p class="name">
									<b><a href="<?php echo $url_mapper['users/view'] . $u->id; ?>/"><?php echo $u->f_name . " " . $u->l_name; ?></a></b>&nbsp;&nbsp;
									<?php if($current_user->id != $u->id && $current_user->can_see_this('users.follow' , $group )) { ?><a href="javascript:void(0);" class="btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" name="<?php echo $u->id; ?>" value="<?php echo $u->follows; ?>" data-obj="user" data-lbl="" data-lbl-active="" ><i class="fe fe-user-plus"></i><?php echo $follow_txt; ?> | <?php echo $u->follows; ?></a><?php } ?>
									<br><small><?php echo $comment; ?></small>
								</p>
						</div>
					</div>
				</div>
			<?php
				}  echo '<br class="clearfix"><hr class="clearfix">';
			}
			
			$per_page = "20";
			if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
					$page= $_GET['page'];
			} else {
					$page=1;
			}
			
			$total_count = FollowRule::count_subscriptions('space',$space->id , 'obj_id');
			$pagination = new Pagination($page, $per_page, $total_count);
			$following = FollowRule::get_subscriptions('space',$space->id , 'obj_id' , " LIMIT {$per_page} OFFSET {$pagination->offset()} " );
			
			if($following) {
				echo '<h4>Followers</h4><div class="row">';
				foreach($following as $f) {
					$u = User::get_specific_id($f->user_id);
					if($u->avatar) {
						$img = File::get_specific_id($u->avatar);
						$quser_avatar = WEB_LINK."public/".$img->image_path();
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (!file_exists($quser_avatar_path)) {
							$quser_avatar = WEB_LINK.'public/img/avatar.png';
						}
					} else {
						$quser_avatar = WEB_LINK.'public/img/avatar.png';
					}
					
					$comment = $u->comment;
					if (strlen($u->comment) > 60) {
						$stringCut = substr($u->comment, 0, 60);
						$comment = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
					}
					
					$u_follow_class = 'follow';
					$follow_txt = '';
					$followed = FollowRule::check_for_obj('user' , $u->id, $current_user->id);
					if($followed) {
						$follow_txt = '';
						$u_follow_class = 'active unfollow';
					}
				?>
				<div class="question-element question-like-machine col-6">
					<div class="card" style="min-height:99px">
						<div class="card-body">
							<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
								<p class="name">
									<b><a href="<?php echo $url_mapper['users/view'] . $u->id; ?>/"><?php echo $u->f_name . " " . $u->l_name; ?></a></b>&nbsp;&nbsp;
									<?php if($current_user->id != $u->id && $current_user->can_see_this('users.follow' , $group )) { ?><a href="javascript:void(0);" class="btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" name="<?php echo $u->id; ?>" value="<?php echo $u->follows; ?>" data-obj="user" data-lbl="" data-lbl-active="" ><i class="fe fe-user-plus"></i><?php echo $follow_txt; ?> | <?php echo $u->follows; ?></a><?php } ?>
									<br><small><?php echo $comment; ?></small>
								</p>
						</div>
					</div>
				</div>
				<?php
				}
				echo "</div><br style='clear:both'>";
			} else { ?>
				<br><h3 style="color:#b0b0b0" id=''><center><i class="fe fe-code"></i><br><?php echo $lang['spaces-no_users']; ?></center></h3><br>
			<?php }
				
				
			} elseif(isset($_GET['section']) && $_GET['section'] == 'pending' && $current_user->prvlg_group == '1' || isset($_GET['section']) && $_GET['section'] == 'pending' && in_array("-".$current_user->id."-", $admins) || isset($_GET['section']) && $_GET['section'] == 'pending' && in_array("-".$current_user->id."-", $moderators) ) {
				
				$section = 'pending';
				
				$pending_q = Question::get_everything(" AND space_id = '{$space->id}' AND published = 0 ");
				if($pending_q) {
			?>
			<div class="card post-item">
			  <div class="card-body">
				<table class="custom_table table table-bordered" cellspacing="0" width="100%">
					<thead>
						<th><?php echo $lang['admin-pending-questions-title']; ?></th>
						<th><?php echo $lang['admin-pending-questions-user']; ?></th>
						<th><i class="fe fe-settings"></i></th>
					</thead>
					<tbody><?php foreach($pending_q as $pq) { $pq_user = User::get_specific_id($pq->user_id); 
					if(URLTYPE == 'slug') {
						$url_type = $pq->slug;
					} else {
						$url_type = $pq->id;
					}
					?>
						<tr>
						<td><a href="<?php echo $url_mapper['questions/view']; echo $url_type; ?>" target="_blank"><?php echo $pq->title; ?></a></td>
						<td><a href="<?php echo $url_mapper['users/view']; echo $pq_user->id . '/'; ?>" target="_blank"><?php echo $pq_user->f_name . " " . $pq_user->l_name . "</a><br><small style='font-size:10px; color:grey'>" . date_ago($pq->created_at) . "</small>"; ?></td>
						<td>
							<p class="btn-group approve-machine"><a href="javascript:void(0);" class="btn btn-success btn-sm approve-item" data-obj="question" data-id="<?php echo $pq->id; ?>" data-action="approve"><i class="fa fa-check"></i></a>
							<a href="javascript:void(0);" class="btn btn-danger  btn-sm reject-item" data-obj="question" data-id="<?php echo $pq->id; ?>" data-action="reject"><i class="fa fa-times"></i></a></p>
						</td></tr>
					<?php } ?>
					</tbody></table></div></div>
				<?php } else { $section = 'posts'; ?>
					<br><h3 style="color:#b0b0b0" id=''><center><i class="fe fe-code"></i><br><?php echo $lang['spaces-no_posts']; ?></center></h3><br>
				<?php }
				} else { $space->view_s(); ?>
				<div class="index-questions question-like-machine"></div>
				<input type='text' class='page d-none' value='0' readonly>
			<?php } 
			
			if(isset($pagination) && $pagination->total_pages() > 1) {
			?>
			<div class="pagination btn-group">
			
					<?php
					if ($pagination->has_previous_page()) {
						$page_param = $s_link;
						$page_param .= '?section=' . $section . '&page=' . $pagination->previous_page();

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
							$page_param = $s_link;
							$page_param .= '?section=' . $section . '&page=' . $p;

							echo "<a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\">{$p}</a>";
						}
					}
					if($pagination->has_next_page()) {
						$page_param = $s_link;
						$page_param .= '?section=' . $section . '&page=' . $pagination->next_page();

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
			
			
			
	</div>
	
	<div class="col-lg-4  d-none d-lg-block">
	<div class="card post-item">
			  <div class="card-body">
			  <h5><i class='fe fe-info'></i> <?php echo $lang['spaces-details']; ?></h5>
			  <hr><?php if($space->description) { echo $space->description . '<hr>'; } ?>
			  <small class="text-secondary"><?php if($space->open_post) { ?><i class="fe fe-unlock"></i> Anyone can post to this space<?php } else { ?><i class="fe fe-lock"></i> Contributors can post to this space<?php } ?></small>
			  </div>
		</div>
		
		<?php if($current_user->prvlg_group == '1' || in_array("-".$current_user->id."-", $admins) || in_array("-".$current_user->id."-", $moderators) ) {
			$pending = Question::count_everything(" AND space_id = '{$space->id}' AND published = 0 ");
			if($pending) {
		?>
		<div class="card post-item">
			  <div class="card-body">
			  <h5><i class='fe fe-clock'></i> <?php echo $lang['spaces-pending']; echo ' · ' . convert_to_k($pending); ?></h5>
			  <hr>
			  There <?php if($pending == '1') { echo 'is '; } else { echo 'are '; } echo '('.$pending.')'; ?> pending posts waiting your approval.<br><br>
			  <a href="<?php echo $s_link ?>?section=pending" class="btn btn-block btn-outline-primary mb-2"><?php echo $lang['spaces-approve']; ?> <i class="fe fe-chevron-<?php echo $lang['direction-right']; ?>"></i></a>
			  </div>
		</div>
			<?php }} ?>
		
		
		<div class="card post-item">
			  <div class="card-body">
			  <h5><i class='fe fe-users'></i> <?php echo $lang['spaces-people']; if($space->follows) { echo ' · ' . convert_to_k($space->follows); } ?></h5>
			  <hr>
				<?php if($space->admins) {
					$admins = explode("," , $space->admins);
					$admin_ids = array();
					$admin_avatar = array();
					foreach($admins as $admin) {
						$admin_ids[] = str_replace('-', '', $admin);
					}
					$admin_txt = '';
					
					if(count($admin_ids) == '1') {
						$user = User::get_specific_id($admin_ids[0]);
						$admin_avatar[$admin_ids[0]] = $user->get_avatar();
						$admin_txt = "<a href='".$url_mapper['users/view'] . $admin_ids[0]."' target='_blank' class='text-dark'>".$user->f_name . ' ' . $user->l_name . '</a> is an admin';
					} else { 
						$i = '1';
						$limit = count($admin_ids);
						if($limit > 5) {
							$limit = 5;
						}
						foreach($admin_ids as $admin_id) {
							
								if($i <= $limit) {
									$user = User::get_specific_id($admin_id);
									$admin_avatar[$admin_id] = $user->get_avatar();
									$admin_txt .= "<a href='".$url_mapper['users/view'] . $admin_id."' target='_blank' class='text-dark'>".$user->f_name . ' ' . $user->l_name."</a>";	
									if($i == $limit - 1 && $limit > count($admin_ids) || count($admin_ids) == $limit && $i == count($admin_ids) - 1 ) {
										$admin_txt .= ' and ';
									} else {
										if($i != $limit) { $admin_txt .= ', '; }
									}
								
								} else {
									if($i == $limit + 1) {
									$admin_txt .= ' and ' .(count($admin_ids) - $limit) . ' more';
									}
								}
									$i++;
						}
						$admin_txt .= ' are admins';
					}
				?>
				<div class="sm-avatar-container">
					<?php foreach($admin_avatar as $admin_id => $avatar_url) { 
						echo "<a href='".$url_mapper['users/view'] . $admin_id."' target='_blank'><img src='{$avatar_url}' class='sm-avatar'></a>";
					}
					?>
				</div>
				
				<?php echo $admin_txt . '<br><br>'; }
				
				if($space->moderators) {
					$admins = explode("," , $space->moderators);
					$admin_ids = array();
					$admin_avatar = array();
					foreach($admins as $admin) {
						$admin_ids[] = str_replace('-', '', $admin);
					}
					$admin_txt = '';
					
					if(count($admin_ids) == '1') {
						$user = User::get_specific_id($admin_ids[0]);
						$admin_avatar[$admin_ids[0]] = $user->get_avatar();
						$admin_txt = "<a href='".$url_mapper['users/view'] .$admin_ids[0]."' target='_blank' class='text-dark'>".$user->f_name . ' ' . $user->l_name . '</a> is a moderator';
					} else { 
						$i = '1';
						$limit = count($admin_ids);
						if($limit > 5) {
							$limit = 5;
						}
						foreach($admin_ids as $admin_id) {
							
								if($i <= $limit) {
									$user = User::get_specific_id($admin_id);
									$admin_avatar[$admin_id] = $user->get_avatar();
									$admin_txt .= "<a href='".$url_mapper['users/view'] . $admin_id."' target='_blank' class='text-dark'>".$user->f_name . ' ' . $user->l_name."</a>";	
									if($i == $limit - 1 && $limit > count($admin_ids) || count($admin_ids) == $limit && $i == count($admin_ids) - 1 ) {
										$admin_txt .= ' and ';
									} else {
										if($i != $limit) { $admin_txt .= ', '; }
									}
								
								} else {
									if($i == $limit + 1) {
									$admin_txt .= ' and ' .(count($admin_ids) - $limit) . ' more';
									}
								}
									$i++;
						}
						$admin_txt .= ' are moderators';
					}
				?>
				<div class="sm-avatar-container">
					<?php foreach($admin_avatar as $admin_id => $avatar_url) { 
						echo "<a href='".$url_mapper['users/view'] . $admin_id."' target='_blank'><img src='{$avatar_url}' class='sm-avatar'></a>";
					}
					?>
				</div>	  
				
				<?php echo $admin_txt . '<br><br>'; }
				
				
				if($space->contributors) {
					$admins = explode("," , $space->contributors);
					$admin_ids = array();
					$admin_avatar = array();
					foreach($admins as $admin) {
						$admin_ids[] = str_replace('-', '', $admin);
					}
					$admin_txt = '';
					
					if(count($admin_ids) == '1') {
						$user = User::get_specific_id($admin_ids[0]);
						$admin_avatar[$admin_ids[0]] = $user->get_avatar();
						$admin_txt = "<a href='".$url_mapper['users/view'] . $admin_ids[0]."' target='_blank' class='text-dark'>".$user->f_name . ' ' . $user->l_name . '</a> is a contributor';
					} else { 
						$i = '1';
						$limit = count($admin_ids);
						if($limit > 5) {
							$limit = 5;
						}
						foreach($admin_ids as $admin_id) {
							
								if($i <= $limit) {
									$user = User::get_specific_id($admin_id);
									$admin_avatar[$admin_id] = $user->get_avatar();
									$admin_txt .= "<a href='".$url_mapper['users/view'] . $admin_id."' target='_blank' class='text-dark'>".$user->f_name . ' ' . $user->l_name."</a>";	
									if($i == $limit - 1 && $limit > count($admin_ids) || count($admin_ids) == $limit && $i == count($admin_ids) - 1 ) {
										$admin_txt .= ' and ';
									} else {
										if($i != $limit) { $admin_txt .= ', '; }
									}
								
								} else {
									if($i == $limit + 1) {
									$admin_txt .= ' and ' .(count($admin_ids) - $limit) . ' more';
									}
								}
									$i++;
						}
						$admin_txt .= ' are contributors';
					}
				?>
				<div class="sm-avatar-container">
					<?php foreach($admin_avatar as $admin_id => $avatar_url) { 
						echo "<a href='".$url_mapper['users/view'] . $admin_id."' target='_blank'><img src='{$avatar_url}' class='sm-avatar'></a>";
					}
					?>
				</div>	  
				
				<?php echo $admin_txt . '<br><br>'; } ?>
				
				<a href="<?php echo $s_link ?>?section=people" class="btn btn-block btn-outline-primary mb-2"><?php echo $lang['btn-view_more']; ?> <i class="fe fe-chevron-<?php echo $lang['direction-right']; ?>"></i></a>
			  </div>	  
			  
		</div>
	<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
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

function copyToClipboard(text) {
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val(text).select();
	document.execCommand("copy");
	$temp.remove();
}

$('.copy-link').click(function () { copyToClipboard($(this).data('link')); generateSwal("Link Copied!","success"); });


$('<div id="loading_wrap"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" style="height:6px"/></center></div>').appendTo('.index-questions');


function get_posts() {
	$('#loading_wrap').show();
	var page= parseInt($('.page').val()) + 1;
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=questions", {id: '<?php echo $current_user->id; ?>' , data: 'space', space: '<?php echo $space->id; ?>' , page: page , hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		$('#loading_wrap').hide();
		parseThisTo(data,'.index-questions','append');
		 $('.page').val(page);
	});
}
get_posts();
$(window).scroll(function() {
   if($(window).scrollTop() + $(window).height() == $(document).height()) {
       if(!$('#stop-loading').length) {
			get_posts();
		}
   }
});
$(document).on('click' , 'a.add-p' , function(){
	var preloader = '<div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-body box-body"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" alt="" style="height:6px"></center><br></div></div></div>';
	$('.modal-receptor').html(preloader); 
	$('.modal-receptor').modal();
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=add_question", {id: '<?php echo $current_user->id; ?>' , data: 'space_question' , post_type: 'post' , space_id: <?php echo $space->id; ?>, hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		parseThisTo(data,'.modal-receptor');
		$('.modal-receptor').modal(); 
	});
});
$(document).on('click' , 'a.add-q' , function(){
	var preloader = '<div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-body box-body"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" alt="" style="height:6px"></center><br></div></div></div>';
	$('.modal-receptor').html(preloader); 
	$('.modal-receptor').modal();
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=add_question", {id: '<?php echo $current_user->id; ?>' , data: 'space_question' , post_type: 'question' , space_id: <?php echo $space->id; ?>, hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		parseThisTo(data,'.modal-receptor');
		$('.modal-receptor').modal(); 
	});
});
$(document).on('click' , 'a.edit_space' , function(){
	var preloader = '<div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-body box-body"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" alt="" style="height:6px"></center><br></div></div></div>';
	$('.modal-receptor').html(preloader); 
	$('.modal-receptor').modal();
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=edit_space", {id: '<?php echo $current_user->id; ?>' , data: '<?php echo $space->id; ?>' , hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		parseThisTo(data,'.modal-receptor');
		$('.modal-receptor').modal(); 
	});
});
$('.approve-machine').on('click' , '.approve-item' , function() {
	var id = $(this).data('id');
	if(confirm('Are you sure you want to approve this item?')) {
		$(this).parent().parent().parent().hide();
		$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=approve", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(data){
			parseThisTo(data,'.container');
		});
	}
});
$('.approve-machine').on('click' , '.reject-item' , function() {
	var id = $(this).data('id');
	if(confirm('Are you sure you want to reject this item?')) {
		$(this).parent().parent().parent().hide();
		$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=reject", {id:id, data: $(this).data('obj') , hash:'<?php echo $random_hash; ?>'}, function(data){
			parseThisTo(data,'.container');
		});
	}
});
</script>
<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>