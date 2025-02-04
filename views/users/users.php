<?php 
if(isset($_GET['notif']) && is_numeric($_GET['notif'])) {
	$notification = Notif::get_specific_id($db->escape_value($_GET['notif']));
	if($notification && $notification->user_id == $current_user->id) {
		$notification->read();
	}
}

if (isset($id)) {
		if(!User::check_id_existance($id)) {
			redirect_to($url_mapper['error/404/']);
		}
		$user = User::get_specific_id($id);
		
		/*if(!$current_user->can_see_this('adminusers.power' , $group) && $current_user->prvlg_group != '1' && $current_user->id != $id ) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
		}*/
		
		$title= $user->f_name . ' ' . $user->l_name;
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
		
} else {
	redirect_to($url_mapper['error/404/']);
}

$section = 'questions';

if (isset($_GET['section']) && $_GET['section'] != '' ) {
	switch ($_GET['section']) {
		case 'questions':
			$section = 'questions';
		break;
		
		case 'answers':
			$section = 'answers';
		break;
		
		case 'following':
			$section = 'following';
		break;
		
		case 'points':
			$section = 'points';
		break;
		
		case 'followed_by':
			$section = 'followed_by';
		break;
		
		case 'edit':
			if($current_user->can_see_this('users.update', $group) && $current_user->id == $user->id && isset($_GET['hash']) && $_GET['hash'] == $_SESSION[$elhash] ) {
				$section = 'edit';
			}
		break;
		
		case 'delete':
			if($current_user->can_see_this('users.delete', $group) && $current_user->id == $user->id && isset($_GET['hash']) && $_GET['hash'] == $_SESSION[$elhash] ) {
				$section = 'delete';
			}
		break;
		
		default :
			redirect_to($url_mapper['error/404/']);
		break;
	}
}

require_once(VIEW_PATH.'pages/header.php');



if (isset($_POST['delete_user'])) {
		if(!$current_user->can_see_this("users.delete",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			if($edit_id == '1' || $edit_id == '1000' ) {
				$msg = $lang['alert-restricted'];
				redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			}
			if($current_user->prvlg_group != '1' && $current_user->id != $edit_id && !$current_user->can_see_this('adminusers.power' , $group) ) {
				$msg = $lang['alert-restricted'];
				redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			}
			
			$edited_entry = User::get_specific_id($edit_id);
			$edited_entry->deleted = 1;
			$edited_entry->disabled = 1;
			if($edited_entry->update()) {
				$contact = MiscFunction::get_function("contact-us");
				$str = $lang['user-farewell']; $str = str_replace('[NAME]' , $edited_entry->f_name , $str); $str = str_replace('[EMAIL]' , $contact->msg, $str);
				$msg = $str;
				$_SESSION = array();
				if (isset($_COOKIE[session_name()])) {
					setcookie(session_name() , '' , time()-42000 , '/');		
				}
				session_destroy();					
				redirect_to("{$url_mapper['login/']}&edit=success&msg={$msg}");
			}
			
		}
}

if (isset($_POST['edit_user'])) {
		if(!$current_user->can_see_this("users.update",$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to("{$url_mapper['index/']}&edit=fail&msg={$msg}");
		}
		if($_POST['hash'] == $_SESSION[$elhash]){
			//unset($_SESSION[$elhash]);
			
			$edit_id = $db->escape_value($_POST["edit_id"]);
			
			if($current_user->prvlg_group != '1' && $current_user->id != $edit_id && !$current_user->can_see_this('adminusers.power' , $group) ) {
				$msg = $lang['alert-restricted'];
				redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			}
			
			$db_fields = Array('f_name','l_name', 'mobile', 'address' , 'comment' , 'about' , 'upload_files');
			
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
			
			if($current_user->can_see_this('users.changemail' , $group) ) {
			$email = $db->escape_value($_POST['email']);
			
			$current_email = $edited_entry->email;
			$email_exists = User::check_existance_except("email", $email , $edit_id);
			
			if($email_exists) {
				$msg = $lang['alert-email_exists'];
				redirect_to("{$url_mapper['users/view']}{$edit_id}/?section=edit&edit=fail&msg={$msg}");
			}
			
			if($email != '' && $email != $current_email) {
			$edited_entry->email = $email;
			}
			}
			
			
			if($current_user->can_see_this('users.changepass' , $group) ) {
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
				$crop_arr = $_POST['cropped'];
				$crop = json_decode($crop_arr , true);
				$upload_problems = 0;
				for ($f ; $f < $num_pics ; $f++) :
					$file = "file";
					$string = $$file . "{$f}";
					$$string = new File();	
						if(!empty($_FILES['upload_files']['name'][$f])) {
							$$string->attach_file($_FILES['upload_files'], $f);
							if ($$string->save($crop)) {
								$images[$f] = $$string->id;
							} else {
								$upl_msg = $lang['alert-upload_error'];
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
			
			$privileges_raw= $_POST["mail_notif"];
			$privileges_danger = implode("," , $privileges_raw);
			$privileges = $db->escape_value($privileges_danger);
			
			$edited_entry->mail_notif = $privileges;
			
			if ($edited_entry->update()) {
				//Log::log_action($current_user->id , "Edit User object" , "Edit User object ({$edited_entry->name}) - id #({$edited_entry->id})" );
				$msg = $lang['alert-update_success'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['users/view']}{$edit_id}/?section=edit&edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(isset($upl_msg)) {
					$msg .= $upl_msg;
				}
				redirect_to("{$url_mapper['users/view']}{$edit_id}/?section=edit&edit=fail&msg={$msg}");
			}
		} else {
			$msg = $lang['alert-auth_error'];
			redirect_to("{$url_mapper['users/view']}{$edit_id}/?section=edit&edit=fail&msg={$msg}");
		}
}




require_once(VIEW_PATH.'pages/navbar.php');


?>
<div class="container">		

<div class="row">
	
	<div class="col-md-2">
		
		<center><img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="width:90%;"></center>
		
		
		
		<br style="clear:both">
		
		<div class="card post-item">
			<div class="card-body">
			  <h5><i class='fe fe-help-circle'></i> <?php echo $lang['user-sections']; ?></h5>
			  <hr>
		  
		  <ul class="nav-ul">
			<li><a href="<?php echo $url_mapper['users/view']; echo $user->id; ?>/?section=questions" class="col-md-12"><?php echo $lang['user-questions']; ?></a></li>
			<li><a href="<?php echo $url_mapper['users/view']; echo $user->id; ?>/?section=answers" class="col-md-12"><?php echo $lang['user-answers']; ?></a></li>
			<li><a href="<?php echo $url_mapper['users/view']; echo $user->id; ?>/?section=followed_by" class="col-md-12"><?php echo $lang['user-followed']; ?></a></li>
			<li><a href="<?php echo $url_mapper['users/view']; echo $user->id; ?>/?section=following" class="col-md-12"><?php echo $lang['user-following']; ?></a></li>
			<li><a href="<?php echo $url_mapper['users/view']; echo $user->id; ?>/?section=points" class="col-md-12"><?php echo $lang['user-points']; ?> (<?php echo $user->points; ?>)</a></li>
		</ul>
			  
			</div>
		</div>
		
		
		
<?php
	$ads = ads('left_sidebar');
	if($ads) {
		$r= array_rand($ads);
		$ad = $ads[$r];
		if($ad) {
			echo '<p>&nbsp;<hr></p>';
				if($ad->link) { echo "<a href='".WEB_LINK."ad/run/{$ad->id}' target='_blank'>"; }
					$content = str_replace('\\','',$ad->content);
					$content = str_replace('<script','',$content);
					$content = str_replace('</script>','',$content);
					echo $content;
				if($ad->link) { echo "</a>"; }
				$ad->view();
		}
	}
	?>


	</div>
	
	
	<div class="col-md-7">
		
			<?php
			
			if (isset($_GET['edit']) && isset($_GET['msg']) && $_GET['edit'] == "success") :
			$status_msg = $db->escape_value($_GET['msg']); $status_msg = htmlspecialchars($status_msg, ENT_QUOTES, 'UTF-8');				
		?>
			<div class="alert alert-success">
				<i class="fa fa-check"></i> <strong><?php echo $lang['alert-type-success']; ?>!</strong>&nbsp;&nbsp;<?php echo $status_msg; ?>
			</div>
		<?php
			endif; 	
			if (isset($_GET['edit']) && isset($_GET['msg']) && $_GET['edit'] == "fail") :
			$status_msg = $db->escape_value($_GET['msg']); $status_msg = htmlspecialchars($status_msg, ENT_QUOTES, 'UTF-8');		
		?>
			<div class="alert alert-danger">
				<i class="fa fa-times"></i> <strong><?php echo $lang['alert-type-error']; ?>!</strong>&nbsp;&nbsp;<?php echo $status_msg; ?>
			</div>
			
		<?php 
			endif;
		?>
		
		<div class="card post-item" style="">
			  <div class="card-body">
			<p class="name question-like-machine" style="font-size:25px">
				<b><?php echo $user->f_name . " " . $user->l_name; ?></b>
				<small style="color:#999">@<?php echo $user->username; ?></small>
				<?php if($user->last_seen >= (time() - 60000)) { echo "&nbsp;<i class='text-success fa fa-circle'></i>"; }  echo "&nbsp;<a href='".WEB_LINK."rss/user/".$user->id."' target='_blank' class='btn btn-sm btn-secondary m-1 text-dark text-decoration-none' data-toggle='tooltip' title='RSS Feed' > <i class='fe fe-rss'></i> {$lang['btn-rss']}</a>"; 
				if($user->id != $current_user->id && $current_user->can_see_this('users.follow' , $group ) ) {
				$u_follow_class = 'follow';
				$follow_txt = $lang['btn-follow'];
				$followed = FollowRule::check_for_obj('user' , $user->id, $current_user->id);
				if($followed) {
					$follow_txt = $lang['btn-followed'];
					$u_follow_class = 'active unfollow';
				}
				?>
				<a href="javascript:void(0);" class="btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" name="<?php echo $user->id; ?>" value="<?php echo $user->follows; ?>" data-obj="user" data-lbl="<?php echo $lang['btn-follow']; ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" ><i class="fe fe-user-plus"></i> <?php echo $follow_txt; ?> · <?php echo $user->follows; ?></a>
			<?php } ?>
				
				
				
				<br><small><?php if($user->comment) { echo $user->comment; } ?></small>
				
				<p style="font:size:16px">
					<?php echo $user->about; ?>
				</p>
			</p>
			
		</div>
		</div>
		
		<hr>
		
		<?php
		if($section == 'questions') {
		
		$per_page = "20";
		if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
				$page= $_GET['page'];
		} else {
				$page=1;
		}
		
		$total_count = Question::count_questions_for($user->id," ");
		$pagination = new Pagination($page, $per_page, $total_count);
		$questions = Question::get_questions_for($user->id ," LIMIT {$per_page} OFFSET {$pagination->offset()} " );
			
			if($questions) {
			foreach($questions as $q) {
				$upvote_class = 'upvote';
				$downvote_class = 'downvote';
				
				$upvote_txt = $lang['btn-like'];
				$liked = LikeRule::check_for_obj('question' , "like" , $q->id, $current_user->id);
				if($liked) {
					$upvote_txt = $lang['btn-liked'];
					$upvote_class = 'active undo-upvote';
					$downvote_class = 'downvote disabled';
				}
				
				$downvote_txt = $lang['btn-dislike'];
				$disliked = LikeRule::check_for_obj('question' , "dislike" , $q->id, $current_user->id);
				if($disliked) {
					$downvote_txt = $lang['btn-disliked'];
					$upvote_class = 'upvote disabled';
					$downvote_class = 'active undo-downvote';
				}
				if(URLTYPE == 'slug') {
					$url_type = $q->slug;
				} else {
					$url_type = $q->id;
				}
				
				if($q->anonymous == "0" || $q->anonymous == '1' && $current_user->id == $q->user_id) {
					if($q->anonymous) { $quser_avatar = WEB_LINK.'public/img/avatar.png'; }
		?>
			<div class=" card post-item" style="">
			  <div class="card-body">
				
					<?php if($q->anonymous == '1' && $current_user->id == $q->user_id) { ?><small><i><?php echo $lang['user-anonymous-intro']; ?></i></small><?php } ?>
					
					<p class="publisher">
						<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
						
							<?php if($q->anonymous) { echo $lang['user-anonymous']; } else { ?>
							<?php echo $user->f_name . " " . $user->l_name; ?> · <span style="color:grey"><?php echo $user->comment; ?></span>
							<?php } ?>
							<br><small>@<?php echo $user->username; ?> | <?php if($q->updated_at != "0000-00-00 00:00:00") { echo $lang['index-question-updated'] . " " . date_ago($q->updated_at); } else { echo $lang['index-question-created'] . " " . date_ago($q->created_at); }?></small>
						
					</p><h4 class="font-weight-bold pt-2"><a href="<?php echo $url_mapper['questions/view']; echo $url_type; ?>" class="text-dark"><?php echo strip_tags($q->title); ?></a></h4><p>
						<?php $string = strip_tags($q->content);
							if (strlen($string) > 500) {
								// truncate string
								$stringCut = substr($string, 0, 500);
								// make sure it ends in a word so assassinate doesn't become ass...
								$string = substr($stringCut, 0, strrpos($stringCut, ' '))."... <a href='./questions/{$q->slug}' target='_blank'>({$lang['index-question-read_more']})</a>"; 
							}
							echo $string;?>
					</p>
					<?php if($current_user->can_see_this('questions.interact' ,$group)) { ?><p class="footer question-like-machine">
						<a href="<?php echo $url_mapper['questions/view'] . $url_type; ?>#answer-question" class="btn btn-secondary"><i class="fe fe-edit-3"></i> <?php echo $lang['index-question-answer']; if($q->answers) {  echo " | {$q->answers}"; } ?></a>
						<?php if($q->user_id != $current_user->id) { ?><a href="javascript:void(0);" class="btn btn-secondary <?php echo $upvote_class; ?>" name="<?php echo $q->id; ?>" value="<?php echo $q->likes; ?>" data-obj="question" data-lbl="<?php echo $lang['btn-like']; ?>" data-lbl-active="<?php echo $lang['btn-liked']; ?>"  ><i class="fa fa-thumbs-o-up"></i> <?php echo $upvote_txt; if($q->likes) {  echo " | {$q->likes}"; } ?></a>
						<a href="javascript:void(0);" class="btn btn-secondary <?php echo $downvote_class; ?>" name="<?php echo $q->id; ?>" value="<?php echo $q->dislikes; ?>" data-obj="question" data-lbl="<?php echo $lang['btn-dislike']; ?>" data-lbl-active="<?php echo $lang['btn-disliked']; ?>" ><i class="fa fa-thumbs-o-down"></i> <?php echo $downvote_txt; if($q->dislikes) {  echo " | {$q->dislikes}"; } ?></a><?php } ?>
					</p><?php } ?>
				</div>
				</div>
			<?php } } }
			
		} elseif($section == 'answers') {
			
		$per_page = "20";
		if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
				$page= $_GET['page'];
		} else {
				$page=1;
		}
		
		$total_count = Answer::count_answers_for_user($user->id," ");
		$pagination = new Pagination($page, $per_page, $total_count);
		$answers = Answer::get_answers_for_user($user->id ," LIMIT {$per_page} OFFSET {$pagination->offset()} " );
			
			if($answers) {
			foreach($answers as $a) {
				
				$q= Question::get_specific_id($a->q_id);
				
				if($q) {
				
				$upvote_class = 'upvote';
				$downvote_class = 'downvote';

				$upvote_txt = '';
				$liked = LikeRule::check_for_obj('answer' , "like" , $a->id, $current_user->id);
				if($liked) {
					$upvote_txt = '';
					$upvote_class = 'active undo-upvote';
					$downvote_class = 'downvote disabled';
				}

				$downvote_txt = '';
				$disliked = LikeRule::check_for_obj('answer' , "dislike" , $a->id, $current_user->id);
				if($disliked) {
					$downvote_txt = '';
					$upvote_class = 'upvote disabled';
					$downvote_class = 'active undo-downvote';
				}
				
				if(URLTYPE == 'slug') {
					$url_type = $q->slug;
				} else {
					$url_type = $q->id;
				}
		?>
			<div class=" card post-item" style="">
			  <div class="card-body">
				
					<p>
						<?php $string = strip_tags($a->content);
							if (strlen($string) > 500) {
								$stringCut = substr($string, 0, 500);
								$string = substr($stringCut, 0, strrpos($stringCut, ' '))."... <a href='{$url_mapper['questions/view']}{$url_type}#answer-{$a->id}' >({$lang['user-comment-read_more']})</a>";
							}
							echo $string;?><hr>
					</p>
					
					<p class="publisher">
						<?php if($a->published == 0) { ?><p class="label label-danger"><i class="fa fa-eye-slash"></i> <?php echo $lang['questions-pending-tag']; ?></p><?php } ?>
			
						<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
						<p class="name">
							<small>@<?php echo $user->username; ?> | <?php if($a->updated_at != "0000-00-00 00:00:00") { echo $lang['index-questions-updated'] . date_ago($a->updated_at); } else { echo $lang['index-question-created'] . date_ago($a->created_at); }?>
							<br>
							<?php echo $lang['user-comment-posted_at'] . ": <b><a href='{$url_mapper['questions/view']}{$url_type}#answer-{$a->id}'class='text-dark' target='_blank'>".strip_tags($q->title). '</a></b>'; ?>
							</small>
						</p>
					</p>
					<p class="footer">
					
						<?php if($current_user->can_see_this('questions.interact' ,$group)) { ?>
						
						<div class="btn-group question-like-machine">
						
						<a href="<?php echo "{$url_mapper['questions/view']}{$url_type}#answer-{$a->id}"; ?>" class="btn btn-sm btn-secondary" target="_blank"><i class="fa fa-search"></i></a>
						<?php if($a->user_id != $current_user->id) { ?><a href="javascript:void(0);" class="btn btn-sm btn-secondary <?php echo $upvote_class; ?>" name="<?php echo $a->id; ?>" value="<?php echo $a->likes; ?>" data-obj="answer" data-lbl="" data-lbl-active="" ><i class="fa fa-thumbs-o-up"></i> | <?php echo $a->likes; ?></a>
						<a href="javascript:void(0);" class="btn btn-sm btn-secondary <?php echo $downvote_class; ?>" name="<?php echo $a->id; ?>" value="<?php echo $a->dislikes; ?>" data-obj="answer" data-lbl="" data-lbl-active="" ><i class="fa fa-thumbs-o-down"></i> | <?php echo $a->dislikes; ?></a><?php } ?>
						<div class="btn-group">
						
						<?php if($a->user_id == $current_user->id || $current_user->prvlg_group == '1') { ?>
							<button type="button" class="btn btn-sm btn-secondary text-dark dropdown-toggle" data-toggle="dropdown">
							Tools <span class="caret"></span></button>
							<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
									
								<?php if($a->published == 0 && $current_user->prvlg_group == '1' ) { ?><a href="<?php echo $url_mapper['answers/approve'] . $url_type . "&type=approve_answer&id={$a->id}&hash={$random_hash}"; ?>" class="dropdown-item">Approve Answer</a>
								<div class="dropdown-divider"></div><?php } ?>
								

								<a href="<?php echo $url_mapper['answers/edit'] . $url_type; ?>&type=edit_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>#answer-question" class="dropdown-item">Edit</a>
								<a href="<?php echo $url_mapper['answers/delete'] . $url_type; ?>&type=delete_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this answer?');">Delete</a>
								
								<div class="dropdown-divider"></div>
								<?php } ?>
								
								<a href="javascript:void(0);" onClick="return confirm('Are you sure you want to report this answer?');" class="dropdown-item" ><?php echo $lang['questions-answer-report']; ?></a>
							</div>
						  </div>
						</div>
						<?php } ?>
						
					</p>
				</div>
				</div>
			<?php } } } 
			
			
			
			
			
			
			
		} elseif($section == 'followed_by') {
			
			
			$per_page = "25";
			if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
					$page= $_GET['page'];
			} else {
					$page=1;
			}
			
			$total_count = FollowRule::count_subscriptions('user',$user->id , 'obj_id');
			$pagination = new Pagination($page, $per_page, $total_count);
			$following = FollowRule::get_subscriptions('user',$user->id , 'obj_id' , " LIMIT {$per_page} OFFSET {$pagination->offset()} " );
			
			if($following) {
				echo '<div class="row">';
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
			}
			
			
			
			
		} elseif($section == 'following') {
			
			$per_page = "25";
			if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
					$page= $_GET['page'];
			} else {
					$page=1;
			}
		
			$total_count = FollowRule::count_subscriptions('user',$user->id, 'user_id');
			$pagination = new Pagination($page, $per_page, $total_count);
			$following = FollowRule::get_subscriptions('user',$user->id , 'user_id', " LIMIT {$per_page} OFFSET {$pagination->offset()} " );
			
			if($following) {
				echo '<div class="row">';
				foreach($following as $f) {
					$u = User::get_specific_id($f->obj_id);
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
			}
			
		} elseif($section == 'edit') {
				
				
				if(!$current_user->can_see_this('adminusers.power' , $group) && $current_user->prvlg_group != '1' && $current_user->id != $user->id ) {
					$msg = $lang['alert-restricted'];
					redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
				}

			?>
			
			<div class="card post-item">
			<div class="card-body">
				<form method="post" action="<?php echo $url_mapper['users/view'] . $user->id; ?>/" enctype="multipart/form-data">
			
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="f_name"><?php echo $lang['admin-users-f_name']; ?></label>
								<input type="text" class="form-control" name="f_name" id="f_name" placeholder="First Name.." required value="<?php echo $user->f_name; ?>">
								<br>
								
								<label for="l_name"><?php echo $lang['admin-users-l_name']; ?></label>
								<input type="text" class="form-control" name="l_name" id="l_name" placeholder="Last Name.." required value="<?php echo $user->l_name; ?>">
								<br>
								
								<label for="mobile"><?php echo $lang['admin-users-phone']; ?></label>
								<input type="text" class="form-control" name="mobile" id="mobile" placeholder="Phone.." value="<?php echo $user->mobile; ?>">
								<br>
								
								<label for="address"><?php echo $lang['admin-users-address']; ?></label>
								<input type="text" class="form-control" name="address" id="address" placeholder="Address.." value="<?php echo $user->address; ?>">
								<br>
								
								
								<label class="col-form-label" for="img1_upl"><?php echo $lang['admin-users-avatar']; ?></label>
								<div class="controls">
									
									<div><img src="<?php echo $quser_avatar; ?>" class="" style="float:<?php echo $lang['direction-left']; ?>; padding:5px; margin-<?php echo $lang['direction-right']; ?>:10px; max-width:100%; height:350px" id="img1"></div>
									
									<br style='clear:both'>
									<div style="height:64px; padding-top: 12px;width:200px;float:<?php echo $lang['direction-left']; ?>">
										<input class="text-input " type="file" name="upload_files[]" id="img1_upl"/><br/>
									</div>
								
								</div>
								
								
								
								</div>
							<hr>
								
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="comment"><?php echo $lang['admin-users-comment']; ?></label>
								<input type="text" class="form-control" name="comment" id="comment" placeholder="Short Description.." value="<?php echo $user->comment; ?>">
								<br>
								
								<label for="about"><?php echo $lang['admin-users-about']; ?></label>
								<textarea name="about" class="form-control" rows="3"><?php echo $user->about; ?></textarea>
								<br>
						</div>
				
					<label for="username" class="col-form-label"><?php echo $lang['admin-users-username']; ?></label>
				  <div class="input-group">
					  <span class="input-group-addon" id="basic-addon1">@</span>
					  <input type="text" class="form-control " id="username" name="username" placeholder="" value="<?php echo $user->username; ?>" readonly disabled>
					</div>
					<br>
					
					<?php if($current_user->can_see_this('users.changemail' , $group) ) { ?>
				  <label for="username" class="col-form-label"><?php echo $lang['admin-users-email']; ?></label>
				  <div class="controls"><input type="email" class="form-control " id="username" name="email" placeholder="Unchanged" ></div>
				  <br>
					<?php } ?>
				  
				  <?php if($current_user->can_see_this('users.changepass' , $group) ) { ?>
				  <label for="password" class="col-form-label"><?php echo $lang['admin-users-pass']; ?></label>
				  <div class="controls"><input type="text" class="form-control " id="password" name="password" placeholder="Unchanged" ></div>
				  
				  
				   <br><div id="messages"></div>
					<?php } ?>		
					
									<?php 
				$notifications = array("new-user-follow",
											"new-question-follow",
											"approve-question",
											"approve-answer",
											"reject-question",
											"reject-answer",
											"report-my-questions",
											"report-my-answers",
											"report-others-questions",
											"report-others-answers",
											"question-report-rejected",
											"answer-report-rejected",
											"new-user-question",
											"new-feed-question",
											"mention",
											"new-answer"
				);
				
				echo "<label>{$lang['user-notification-settings-title']}</label><br>";
				foreach($notifications as $notif) {
					echo '<input type="checkbox" class="liParent" name="mail_notif[]" value="'.$notif.'" ';
						if($user->can_receive_this($notif)) { echo ' checked'; }
					echo ' /> '.$lang['user-notification-settings-'.$notif].'<br>';
				}
				?>
					<br><br>
				

						</div>
						
					</div>
					
							<center>
								<input class="btn btn-success" type="submit" name="edit_user" value="<?php echo $lang['btn-update']; ?>">
							</center>
						
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"cropped\" id=\"cropped\" value=\"\" readonly/>";
						echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$user->id."\" readonly/>";
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form></div></div>
			<?php
		}elseif($section == 'delete') {
		?>
		
		<div class="card post-item">
			<div class="card-body">
		
			<h4><?php echo $lang['user-delete-msg']; ?></h4>
			<br>
			<form method="post" action="<?php echo $url_mapper['users/view'] . $user->id; ?>/" enctype="multipart/form-data">
					<center>
						<input class="btn btn-danger" type="submit" name="delete_user" value="<?php echo $lang['btn-close_account']; ?>">
						<a href="<?php echo $url_mapper['users/view'] . $user->id; ?>/" class="btn btn-secondary"><?php echo $lang['btn-back']; ?></a>
					</center>
				
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$user->id."\" readonly/>";
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?><br>
			</form></div></div>
		<?php
		}elseif($section == 'points') {
		?>
		<div class="card post-item" style="">
		  <div class="card-body">
			<table class="table custom-table">
			  <thead>
				<tr>
				  <th style='width:10px'>#</th>
				  <th><?php echo $lang['user-points-reason']; ?></th><th><?php echo $lang['user-points-awarded_at']; ?></th>
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
					
					
					$query = " AND user_id = '{$user->id}' ";
					
					$total_count = Award::count_everything($query , "");
					$pagination = new Pagination($page, $per_page, $total_count);
					$all_obj= Award::get_everything($query," LIMIT {$per_page} OFFSET {$pagination->offset()} ");
					
					
					$i= (($page-1) * $per_page) + 1;
					
					
					foreach($all_obj as $obj) :
					
				?>
				<tr>
				  <td><?php echo $i; ?></td>
				  <td><?php echo $obj->reason; ?></td>
				  <td><?php echo date_to_eng($obj->created); ?></td>
				</tr>
				
				<?php 
					$i++;
					endforeach;
				?>
			  </tbody>
			</table>
			</div></div>
		<?php
		}
			
			if(isset($pagination) && $pagination->total_pages() > 1) {
					?>
					<div class="pagination btn-group">
					
							<?php
							if ($pagination->has_previous_page()) {
								$page_param = $url_mapper['users/view'].$user->id.'/';
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
									$page_param = $url_mapper['users/view'].$user->id.'/';
									$page_param .= '?section=' . $section . '&page=' . $p;

									echo "<a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\">{$p}</a>";
								}
							}
							if($pagination->has_next_page()) {
								$page_param = $url_mapper['users/view'].$user->id.'/';
								$page_param .= '?section=' . $section . '&page=' . $pagination->next_page();

								echo " <a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\"><i class=\"fa fa-chevron-{$lang['direction-right']}\"></i></a> ";
							} else {
							?>
							<a class="btn btn-secondary" type="button"><i class="fa fa-chevron-<?php echo $lang['direction-right']; ?>"></i></a>
							<?php
							}
							?>
					
					</div><br>
					<?php
					}
			
			
			?>
		
		
		
		
		
		
	</div>
	
	<div class="col-md-3">
	<?php if($current_user->id != '1000' && $current_user->id == $user->id) { ?>
		<div class="card post-item">
			  <div class="card-body">
				  <h5><i class='fe fe-lock'></i> <?php echo $lang['user-account-options']; ?></h5>
				  <hr>
				  <ul class="nav-ul">
					<?php if($current_user->can_see_this('users.update' , $group)) { ?><li><a href="<?php echo $url_mapper['users/view'] . $user->id . '/?section=edit&hash='.$random_hash; ?>" class="col-md-12"><?php echo $lang['user-account-edit']; ?></a></li><?php } ?>
					<?php if($current_user->can_see_this('users.delete', $group)) { ?><li><a href="<?php echo $url_mapper['users/view'] . $user->id . '/?section=delete&hash='.$random_hash; ?>" class="col-md-12"><?php echo $lang['user-account-delete']; ?></a></li><?php } ?>
					<li><a href="<?php echo $url_mapper['logout/']; ?>" class="col-md-12"><?php echo $lang['index-user-logout']; ?></a></li>
				</ul>
				
			</div>
		</div>
			
<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
<?php
	}
	$ads = ads('right_sidebar');
	if($ads) {
		$r= array_rand($ads);
		$ad = $ads[$r];
		if($ad) {
			echo '<p>&nbsp;</p>';
				if($ad->link) { echo "<a href='".WEB_LINK."ad/run/{$ad->id}' target='_blank'>"; }
					$content = str_replace('\\','',$ad->content);
					$content = str_replace('<script','',$content);
					$content = str_replace('</script>','',$content);
					echo $content;
				if($ad->link) { echo "</a>"; }
				$ad->view();
			echo '<hr>';
		}
	}
?>
	
</div>

	
</div>
    </div> <!-- /container -->
    <?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
	<?php require_once(VIEW_PATH.'pages/footer.php'); ?>
	<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-bs4.js"></script>
	<script src="<?php echo WEB_LINK; ?>public/plugins/strongpass/StrongPass.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script>
    $(document).ready(function() {
        $('#summernote').summernote({
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0]);
				}
			}

        });
        function sendFile(image) {
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
                    $('#summernote').summernote("insertImage", url);
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
		$('select').select2();
	});
	$('a#answer-btn').click(function(){
		scrollToAnchor('answer-question');
		$('.note-editable').trigger('focus');
	});
	
	
	function readURL(input,targetid) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$("#" + targetid).attr('src', e.target.result);
				
				$("#" + targetid).cropper('destroy');
				
				$("#" + targetid).cropper({
				  aspectRatio: 1/1,
				  crop: function(e) {
					var croppedData = '{"x":"'+ e.x +'","y":"'+ e.y +'","width":"' + e.width + '","height":"' + e.height + '" }';
					$('#cropped').val(croppedData);
				  }
				});
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#img1_upl").change(function(){
		readURL(this, 'img1');
	});

	var options = {
			onKeyUp: function (evt) {
				$(evt.target).pwstrength("outputErrorList");
			}
		};
		$('#password').pwstrength(options);
	</script>
	<?php require_once(VIEW_PATH.'pages/like-machine.php'); ?>
	
<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>