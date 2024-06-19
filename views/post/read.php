<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

if(isset($_GET['notif']) && is_numeric($_GET['notif'])) {
	$notification = Notif::get_specific_id($db->escape_value($_GET['notif']));
	if($notification && $notification->user_id == $current_user->id) {
		$notification->read();
	}
}

$data = $q_slug;
if(URLTYPE == 'id') {
	$q = Question::get_specific_id($data);
} else {
	$q = Question::get_slug($data);
}

if($q) {
	if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
	
	$title= strip_tags($q->title);
	$user = User::get_specific_id($q->user_id);
	if($user->avatar) {
		$img = File::get_specific_id($user->avatar);
		$quser_avatar = WEB_LINK."public/".$img->image_path();
		$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
		if (!file_exists($quser_avatar_path)) {
			$quser_avatar = WEB_LINK.'public/img/avatar.png';
		}
	} else {
		$quser_avatar = WEB_LINK.'public/img/avatar.png';
	}
	
	if($q->anonymous) {
		$quser_avatar = WEB_LINK.'public/img/anonymous.png';
	}
	
	$q->view_q();

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
			if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
			if($q->space_id) {
				redirect_to($url_mapper['posts/view']."{$url_type}/?edit=success&msg={$msg}");
			} else {
				redirect_to($url_mapper['questions/view']."{$url_type}/?edit=success&msg={$msg}");
			}
		}
	} else {
		$msg = $lang['alert-report_failed'];
		redirect_to($url_mapper['questions/create']."/?edit=fail&msg={$msg}");
	}
	
}

if (isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['type']) && $_GET['type'] != '' && isset($_GET['hash']) && $_GET['hash'] != '' ) {
	
	if($_GET['hash'] != $_SESSION[$elhash] ) {
		redirect_to($url_mapper['index/']);
	}

	switch($_GET['type']) {

		case 'approve' :
			
			if(!$current_user->can_see_this("pending.update",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."/?edit=fail&msg={$msg}");
			}
			$id = $db->escape_value($_GET['id']);
			$q = Question::get_specific_id($id);
			if($q) {
				$q->publish();
				
				###############
				## APPROVE NOTIF ##
				###############
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				$notif_link = $url_mapper['questions/view'].$url_type;
				$str = $lang['notif-q_publish-msg']; $str = str_replace('[TITLE]' , $q->title , $str);
				$notif_msg = $str;
				$notif_user = $q->user_id;
				$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
				##########
				## MAILER ##
				##########
				$msg = $notif_msg . "<br>Check it out at " . $notif_link;
				$title = $lang['notif-q_publish-title'];
				$receiver = User::get_specific_id($notif_user);
				if($receiver && is_object($receiver) && $receiver->can_receive_this("approve-question")) {
					Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
				}
			}
			
		break;
		
		case 'approve_answer' :
			
			if(!$current_user->can_see_this("pending.update",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."/?edit=fail&msg={$msg}");
			}
			
			$data = $db->escape_value($_GET['id']);
			$edited_answer = Answer::get_specific_id($data);
			if($edited_answer) {
				$edited_answer->publish();
				
				###############
				## APPROVE NOTIF ##
				###############
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$edited_answer->id;
				$str = $lang['notif-a_publish-msg']; $str = str_replace('[TITLE]' , $q->title , $str);
				$notif_msg = $str;
				$notif_user = $edited_answer->user_id;
				$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
				
				##########
				## MAILER ##
				##########
				$msg = $notif_msg . "<br>Check it out at " . $notif_link;
				$title = $lang['notif-a_publish-title'];
				$receiver = User::get_specific_id($notif_user);
				if($receiver && is_object($receiver) && $receiver->can_receive_this("approve-answer") ) {
					Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
				}
				
			}
			
		break;
		
		case 'edit_answer' :
			if(!$current_user->can_see_this("answers.update",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."/?edit=fail&msg={$msg}");
			}
			$data = $db->escape_value($_GET['id']);
			$edited_answer = Answer::get_specific_id($data);
			if($edited_answer) {
				$edit_answer_mode = true;
			}
			
		break;
		
		case 'delete_answer' :
			if(!$current_user->can_see_this("answers.delete",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."/?edit=fail&msg={$msg}");
			}
			$data = $db->escape_value($_GET['id']);
			$edited_answer = Answer::get_specific_id($data);
			
			if($edited_answer && $edited_answer->user_id == $current_user->id ||  $edited_answer && $current_user->prvlg_group == '1' ) {
				if($edited_answer->delete()) {
					//get assoc reports..
					$rep = Report::get_everything(" AND obj_type = 'answer' AND obj_id = '{$edited_answer->id}' ");
					if($rep) {
						foreach($rep as $r) {
							$r->delete();
						}
					}
					$msg = $lang['alert-delete_success'];
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'].$url_type."/?edit=success&msg={$msg}");
				} else {
					$msg = $lang['alert-delete_failed'];
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'].$url_type."/?edit=fail&msg={$msg}");
				}
			}
		break;
	}
}

require_once(VIEW_PATH.'pages/header.php');
if(isset($_POST['add_a'])) {
	if($_POST['hash'] == $_SESSION[$elhash]){
		unset($_SESSION[$elhash]);
		
			if(!$current_user->can_see_this("answers.create",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'].$url_type."/?edit=fail&msg={$msg}");
			}
			
			$content = profanity_filter($_POST['title']);
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
			
			if($content == '') {
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view'] . "{$url_type}");
			}
			
			if(isset($_POST['edit_id'])) { 		//edit_comment mode ..
				if(!Answer::check_id_existance($db->escape_value($_POST['edit_id']))) {
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'] . "{$url_type}");
				}
				$answer = Answer::get_specific_id($db->escape_value($_POST['edit_id']));
				if($current_user->id == $answer->user_id || $current_user->prvlg_group == '1') {	  //Ownership..
					$answer->updated_at = strftime("%Y-%m-%d %H:%M:%S" , time());
					$answer->content = $content;
					if(isset($_POST['post-type']) && $_POST['post-type'] == 'anonymous') {
						$answer->anonymous = "1";
					}
					
					if(isset($_POST['post-type']) && $_POST['post-type'] == 'private') {
						$answer->anonymous = "2";
					}
					if($answer->update()) {
						$msg = $lang['questions-answer-update_success'];
						
					//Mentions
					preg_match_all('/(^|\s|&nbsp;)(@\w+)/', strip_tags($content), $mentions);
					$mention_results = array_unique($mentions[0]);

					if(isset($mention_results) && is_array($mention_results)) {
						
						foreach($mention_results as $r) {
							
							$new_r = trim(str_replace('@','',$r));
							$new_r = trim(str_replace('&nbsp;','',$new_r));
							
							$usrs = User::find($new_r , 'username' , ' LIMIT 1');
							if($usrs) {
								foreach($usrs as $u) {
							
									if($u && $u->id != 0 && $u->id != $current_user->id) {
										$str = $lang['notif-a_mention-msg']; $str = str_replace("[NAME]" , $current_user->f_name, $str); $str = str_replace("[TITLE]" , $q->title , $str);
										$mention_notif_msg = $str;
										$notif_user = $u->id;
										$notif = Notif::send_notification($notif_user,$mention_notif_msg,$notif_link);
										##########
										## MAILER ##
										##########
										$msg = $mention_notif_msg . "<br>" . $notif_link;
										$title = $lang['notif-a_mention-title'];
										$receiver = User::get_specific_id($notif_user);
										if($receiver && is_object($receiver)  && $receiver->can_receive_this("mention")) {
											Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
										}
									}
								}
							}
						}
					}
						
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						
						if($q->space_id) {
							redirect_to($url_mapper['posts/view']."{$url_type}/?edit=success&msg={$msg}");
						} else {
							redirect_to($url_mapper['questions/view'] . "{$url_type}/?edit=success&msg={$msg}");
						}
					} else {
						$msg = $lang['questions-answer-update_failed'];
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						redirect_to($url_mapper['questions/view'] . "{$url_type}/?edit=fail&msg={$msg}");
					}
				}
			} else {
				
				$a = New Answer();
				$a->user_id = $current_user->id;
				$a->q_id = $q->id;
				$a->created_at = strftime("%Y-%m-%d %H:%M:%S" , time());
				$a->content = $content;
				
				if(isset($_POST['post-type']) && $_POST['post-type'] == 'anonymous') {
					$a->anonymous = "1";
				}
				
				if(isset($_POST['post-type']) && $_POST['post-type'] == 'private') {
					$a->anonymous = "2";
				}

				if(isset($_POST['a_id']) && is_numeric($_POST['a_id']) ) {
					$a->a_id = $db->escape_value($_POST['a_id']);
				}
				
				if($settings['a_approval'] == '0' || $settings['a_approval'] == '1' && $current_user->prvlg_group == '1' || $settings['a_approval'] == '1' && $current_user->can_see_this("answers.power",$group) ) {
					$a->published = 1;
				}
				
				if($a->create()) {
					
					###############
					## FOLLOW NOTIF ##
					###############
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$a->id;
					$str = $lang['notif-a_publish-follow-msg']; $str = str_replace("[NAME]" , $current_user->f_name, $str); $str = str_replace("[TITLE]" , $q->title , $str);
					$notif_msg = $str;
					
					//Mentions
					preg_match_all('/(^|\s|&nbsp;)(@\w+)/', strip_tags($content), $mentions);
					$mention_results = array_unique($mentions[0]);
					
					print_r($mention_results);
				
					if(isset($mention_results) && is_array($mention_results)) {
						
						foreach($mention_results as $r) {
							
							$new_r = trim(str_replace('@','',$r));
							$new_r = trim(str_replace('&nbsp;','',$new_r));
							
							$usrs = User::find($new_r , 'username' , ' LIMIT 1');
							if($usrs) {
								foreach($usrs as $u) {
									
									if($u && $u->id != 0 && $u->id != $current_user->id) {
										$str = $lang['notif-a_mention-msg']; $str = str_replace("[NAME]" , $current_user->f_name, $str); $str = str_replace("[TITLE]" , $q->title , $str);
										$mention_notif_msg = $str;
										$notif_user = $u->id;
										$notif = Notif::send_notification($notif_user,$mention_notif_msg,$notif_link);
										##########
										## MAILER ##
										##########
										$msg = $mention_notif_msg . "<br>" . $notif_link;
										$title = $lang['notif-a_mention-title'];
										$receiver = User::get_specific_id($notif_user);
										if($receiver && is_object($receiver) && $receiver->can_receive_this("mention")) {
											Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
										}
									}
								}
							}
						}
						
					}
					
					//Question owner
					if($q->user_id != $a->user_id) {
						$notif_user = $q->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at " . $notif_link;
						$title = $lang['notif-a_publish-follow-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this("new-answer")) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					//Question followers
					$user_followers = FollowRule::get_subscriptions('question',$q->id , 'obj_id' , "" );
					if($user_followers) {
						foreach($user_followers as $uf) {
							$notif_user = $uf->user_id;
							if($q->user_id != $uf->user_id && $notif_user != $current_user->id ) {
								$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
								##########
								## MAILER ##
								##########
								$msg = $notif_msg . "<br>Check it out at " . $notif_link;
								$title = $lang['notif-a_publish-follow-title'];
								$receiver = User::get_specific_id($notif_user);
								if($receiver && is_object($receiver) && $receiver->can_receive_this("new-answer")) {
									Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
								}
							}
						}
					}
					$q->answers +=1;
					$q->update();
					//$id = mysql_insert_id();
					
					$msg = $lang['questions-answer-create_success'];
					
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					if($q->space_id) {
						redirect_to($url_mapper['posts/view']."{$url_type}/?edit=success&msg={$msg}");
					} else {
						redirect_to($url_mapper['questions/view'] . "{$url_type}/?edit=success&msg={$msg}");
					}
				} else {
					$msg = $lang['questions-answer-create_failed'];
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['questions/view'] . "{$url_type}/?edit=fail&msg={$msg}");
				}
				
			}
			
			
        }
}
require_once(VIEW_PATH.'pages/navbar.php');


$like_class = 'like';
$dislike_class = 'dislike';

$like_txt = $lang['btn-like'];
$liked = LikeRule::check_for_obj('question' , "like" , $q->id, $current_user->id);
if($liked) {
	$like_txt = $lang['btn-liked'];
	$like_class = 'active undo-like';
	$dislike_class = 'dislike disabled';
}

$dislike_txt = $lang['btn-dislike'];
$disliked = LikeRule::check_for_obj('question' , "dislike" , $q->id, $current_user->id);
if($disliked) {
	$dislike_txt = $lang['btn-disliked'];
	$like_class = 'like disabled';
	$dislike_class = 'active undo-dislike';
}


$q_follow_class = 'follow';
$follow_txt = $lang['btn-follow'];
$followed = FollowRule::check_for_obj('question' , $q->id, $current_user->id);
if($followed) {
	$follow_txt = $lang['btn-followed'];
	$q_follow_class = 'active unfollow';
}

?>
<div class="container">	

<div class="row">
	
	
	<div class="post-item col-md-9 question-like-machine">
	
	<?php 
	$space = false;
	if($q->space_id) {
		if(Space::check_id_existance($q->space_id)) {
			$space = Space::get_specific_id($q->space_id);
			if(URLTYPE == 'slug') {$url_type = $space->slug; $s_link = $url_mapper['spaces/view']. $space->slug;} else {$url_type = $space->id; $s_link = $url_mapper['spaces/view']. $s->id;}
			if($space->admins) { $admins = explode(',' , $space->admins); } else { $admins = array(); }
			if($space->moderators) { $moderators = explode(',' , $space->moderators); } else { $moderators = array(); }
			if($space->contributors) { $contributors = explode(',' , $space->contributors); } else { $contributors = array(); }
			
	?>
	<div class="card hovercard full-page">
                <div class="cardheader" style="background:url('<?php echo $space->get_cover(); ?>')">
                </div>
                <div class="avatar">
                    <img alt="" src="<?php echo $space->get_avatar(); ?>">
                </div>
                <div class="info">
                    <div class="title" style='font-size:24px'>
                        <a href="<?php echo $s_link; ?>"><?php echo $space->name; ?></a>
                    </div>
					<p class="text-muted" style="font-size:20px">space/<?php echo $url_type; ?></p>
                    <div class="desc" style='font-size:20px; color: black'><?php echo $space->tagline; ?></div><br>
                </div>
                <?php if($current_user->can_see_this('questions.interact', $group)) { ?><div class="bottom ">
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
					<?php if($space->user_id == $current_user->id || $current_user->prvlg_group == '1') { ?>
						<?php if($current_user->can_see_this('spaces.update' , $group)) { ?><a href="#me" class="dropdown-item edit_space"><?php echo $lang['spaces-edit']; ?></a><?php } ?>
						<?php if($current_user->can_see_this('spaces.delete' , $group)) { ?><a href="<?php echo $url_mapper['spaces/delete']. $url_type."&hash={$random_hash}"; ?>" onClick="return confirm('<?php echo $lang['question-delete-alert']; ?>');" class="dropdown-item"><?php echo $lang['spaces-delete']; ?></a><?php } ?>
					<div class="dropdown-divider"></div>
					<?php } ?>
					<?php $reported = Report::check_for_obj('space' , $space->id, $current_user->id); ?>
					<?php if(!$reported) { ?>
					<a href="#report-q-<?php echo $space->id; ?>" data-toggle="modal" class="dropdown-item"><?php echo $lang['spaces-report']; ?></a>
					<?php } else { ?>
					<a href="javascript:void(0);" class="dropdown-item text-muted text-center " style="width:250px; white-space: normal"><?php echo $lang['spaces-report-reported']; ?></a>
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
				
            </div><hr>
		<?php }} ?>
	
	<div class="card">
	<div class="card-body">
	
		<?php 
			@$tags = explode(",",$q->feed); 
			if(is_array($tags)) {
				foreach($tags as $tag) {
		?>
		<a href="<?php echo $url_mapper['feed/'].$tag; ?>" class="btn btn-sm btn-light"><?php echo $tag; ?></a>
			<?php }} ?>
		<?php if($q->published == 0) { ?><p class="badge badge-danger"><i class="fa fa-eye-slash"></i> <?php echo $lang['questions-pending-tag']; ?></p><?php } ?>
		
		<h3 class="title m-2"><b><?php echo strip_tags($q->title); ?></b>
		<?php echo "&nbsp;<a href='".WEB_LINK."rss/question/".$q->id."' target='_blank' class='text-dark text-decoration-none'><i class='fe fe-rss'></i></a>&nbsp;";  ?>
		
		<?php 
			@$tags = explode(",",$q->tags); 
			if(is_array($tags)) {
				echo "<p style='font-size: 18px; font-weight:normal; padding-top: 8px' class='text-muted'>";
				foreach($tags as $tag) {
					if($tag != '') {
						echo " #<a href='".WEB_LINK."?tag={$tag}' target='_blank' class='text-muted text-decoration-none'>{$tag}</a>";
					}	
				}	
				echo "</p>";
			}
		?>
		</h3>
		
			
			<?php 
			if(URLTYPE == 'slug') {$url_type = $q->slug; $q_link = $url_mapper['questions/view']. $q->slug;} else {$url_type = $q->id; $q_link = $url_mapper['questions/view']. $q->id;}
			
			if($current_user->can_see_this('questions.interact', $group)) {
			?>
			
		<div class="d-flex justify-content-between">
			
			<div class="btn-group  p-0" role="group" aria-label="question-tools">
			<a href="#answer-question" class="btn btn-icon btn-like-machine" data-toggle="tooltip" data-placement="top" title="<?php echo $lang['index-question-answer']; ?>" ><i class="fe fe-edit"></i>&nbsp;<?php echo $lang['index-question-answer']; if($q->answers) { echo "&nbsp;·&nbsp;{$q->answers}"; } ?></a>
			<a href="javascript:void(0);" class="<?php echo $q_follow_class; ?> btn btn-icon btn-like-machine" data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-follow']; ?>"name="<?php echo $q->id; ?>" value="<?php echo $q->follows; ?>" data-obj="question" data-lbl="<?php echo $lang['btn-follow']; ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>"  ><i class="fe fe-user-plus"></i>&nbsp;<?php echo $follow_txt; if($q->follows) { echo "&nbsp;·&nbsp;" . convert_to_k($q->follows); } ?></a>
			
			</div>
			
			<div class="btn-group  p-0" role="group" aria-label="question-tools">
			<a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $like_class; ?>" data-obj="question" name="<?php echo $q->id; ?>" value="<?php echo $q->likes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-likes']; ?>" ><i class="fe fe-thumbs-up"></i><?php if($q->likes) { echo "&nbsp;{$q->likes}"; }?></a>
			<a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $dislike_class; ?>" data-obj="question" name="<?php echo $q->id; ?>" value="<?php echo $q->dislikes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-dislikes']; ?>"><i class="fe fe-thumbs-down"></i><?php if($q->dislikes) { echo "&nbsp;{$q->dislikes}"; }?></a>
			<a href="javascript:void(0);" class="d-none d-lg-block comment btn btn-icon btn-like-machine" data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-views']; ?>"><i class="fe fe-eye"></i><?php if($q->views) { echo "&nbsp;". convert_to_k($q->views); }?></a>
				
			<div class="dropdown">
			  <button class="btn btn-icon btn-like-machine share-receptor" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
				<i class="fe fe-repeat"></i><?php if($q->shares) { echo "&nbsp;". convert_to_k($q->shares); }?></button>
			  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($q_link); ?>&quote=<?php echo urlencode("Check out this interesting question: {$q->title}"); ?>" class="share dropdown-item" target="_blank" name="<?php echo $q->id; ?>" value="<?php echo $q->shares; ?>" data-obj="question" ><i class='fa fa-facebook' style="color: #3b5998"></i> Facebook</a>
				<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($q_link); ?>&text=<?php echo urlencode("Check out this interesting question: {$q->title}"); ?>" class="share dropdown-item" target="_blank" name="<?php echo $q->id; ?>" value="<?php echo $q->shares; ?>" data-obj="question" ><i class='fa fa-twitter' style="color: #00acee"></i> Twitter</a>
				<a href="javascript:void(0);" class="dropdown-item copy-link" data-link="<?php echo $q_link; ?>"><i class='fe fe-link' ></i> Copy Link</a>
			  </div>
			</div>
				
				<div class="dropdown">
				  <button class="btn btn-icon btn-like-machine" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fe fe-more-horizontal"></i>
				  </button>
				  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					<?php if($q->user_id == $current_user->id || $current_user->prvlg_group == '1') { ?>
						<?php if($q->published == 0) { ?>
						<?php if($current_user->can_see_this('pending.update' , $group)) { ?>
							<a href="<?php echo $url_mapper['questions/approve'] . $url_type."&id={$q->id}&hash={$random_hash}"; ?>" class="dropdown-item"><?php echo $lang['questions-approve']; ?></a>
							  <div class="dropdown-divider"></div>
						<?php } ?>
						<?php } ?>
					
						<?php if($current_user->can_see_this('questions.update' , $group)) { ?><a href="<?php echo $url_mapper['questions/update']. $url_type."&hash={$random_hash}"; ?>" class="dropdown-item"><?php if($q->item_type == 'post') { echo $lang['spaces-edit_post']; } else { echo $lang['questions-edit']; } ?></a><?php } ?>
						<?php if($current_user->can_see_this('questions.delete' , $group)) { ?><a href="<?php echo $url_mapper['questions/delete']. $url_type."&hash={$random_hash}"; ?>" onClick="return confirm('<?php echo $lang['question-delete-alert']; ?>');" class="dropdown-item"><?php if($q->item_type == 'post') { echo $lang['spaces-delete_post']; } else { echo $lang['questions-delete']; } ?></a><?php } ?>
					<div class="dropdown-divider"></div>
					<?php } ?>
					<?php $reported = Report::check_for_obj('question' , $q->id, $current_user->id); ?>
					<?php if(!$reported) { ?>
					<a href="#report-q-<?php echo $q->id; ?>" data-toggle="modal" class="dropdown-item"><?php if($q->item_type == 'post') { echo $lang['spaces-report_post']; } else { echo $lang['questions-report']; } ?></a>
					<?php } else { ?>
					<a href="javascript:void(0);" class="dropdown-item text-muted text-center " style="width:250px; white-space: normal"><?php echo $lang['questions-report-reported']; ?></a>
					<?php } ?>
				  </div>
				</div>
				
			</div>
			
			
		</div>
			
			<?php if(!$reported) { ?>
					<!-- Modal -->
					<div class="modal fade in" id="report-q-<?php echo $q->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel"><?php echo $lang['questions-report']; ?></h5>
						  </div>
						  <form action="<?php  echo $url_mapper['questions/view']; echo $url_type; ?>" method="POST" >
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
								echo "<input type=\"hidden\" name=\"id\" value=\"".$q->id."\" readonly/>";
								echo "<input type=\"hidden\" name=\"obj_type\" value=\"question\" readonly/>";
							?>
						  </form>
						</div>
					  </div>
					</div>
				<?php }} ?>
			<hr class='m-1'>
		
		
		<p class="publisher">
			<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:9px;margin-top:16px">
			
			<p class="name ">
				<?php if($q->anonymous) { echo $lang['user-anonymous']; } else { ?>
				
				<b><a href="<?php echo $url_mapper['users/view'] . $q->user_id; ?>/" style="color:black"><?php echo $user->f_name . " " . $user->l_name; ?></a></b><?php if($user->last_seen >= (time() - 60000)) { echo "&nbsp;<i class='text-success fa fa-circle' style='font-size: 12px'></i>"; } ?><?php if($user->comment) { echo " " . $user->comment; } ?>
				
				<?php if($q->user_id != $current_user->id && $current_user->can_see_this('users.follow' , $group) ) { ?>
				<?php	
				$u_follow_class = 'follow';
				$follow_txt = $lang['btn-follow'];
				$followed = FollowRule::check_for_obj('user' , $user->id, $current_user->id);
				if($followed) {
					$follow_txt = $lang['btn-followed'];
					$u_follow_class = 'active unfollow';
				}
				?>
				<a href="javascript:void(0);" target="" class="btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" data-obj="user" name="<?php echo $user->id; ?>" data-lbl="<?php echo $lang['btn-follow'] ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" value="<?php echo $user->follows; ?>" ><i class='fe fe-user-plus'></i> <?php echo $follow_txt; if($user->follows) { echo " · " . convert_to_k($user->follows); } ?></a>
				
				
				
				
				<?php } } ?>
				
				<br><small style="color:#999"><?php if(!$q->anonymous) { echo '@'.$user->username . ' · '; } if($q->updated_at != "0000-00-00 00:00:00") { echo $lang['index-question-updated'] . ' ' . date_ago($q->updated_at); } else { echo $lang['index-question-created'] . ' ' . date_ago($q->created_at); }?></small>
				
			</p>
		</p><?php 
			$content = str_replace('\\','',$q->content);
			$content = str_replace('<script','',$content);
			$content = str_replace('</script>','',$content);
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
			$content = profanity_filter($content);
		if($content != '') { ?><p class="question-element"><?php echo $content; ?></p><?php } ?>
		<hr>
		<?php 
		
		$per_page = "10";
		if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
				$page= $_GET['page'];
		} else {
				$page=1;
		}
		
		$total_count = Answer::count_answers_for($q->id, '');
		$pagination = new Pagination($page, $per_page, $total_count);		
		
		$str = " LIMIT {$per_page} OFFSET {$pagination->offset()} ";
		/*if($current_user->id == 1000) {
			$str = ' LIMIT 1 ';
		}*/
		
		$answers = Answer::get_answers_for($q->id, $str); 
		$t = 1 + (($page - 1) * $per_page);
		if($answers) {
			$ads = ads('between_answers');
			foreach($answers as $a) {
				
			if($a->published == '1' &&  $a->anonymous != '2' || $a->published == '1' &&  $a->anonymous == '2' && $current_user->id == $a->user_id || $a->published == '1' &&  $a->anonymous == '2' && $current_user->id == $q->user_id || $a->published == '1' &&  $a->anonymous == '2' && $current_user->prvlg_group == "1"  || $a->published == '0' && $current_user->id == $a->user_id || $a->published == '0' && $current_user->prvlg_group == '1' ) {
				
				if(URLTYPE == 'slug') {$a_link = $url_mapper['questions/view']. $q->slug."#answer-{$a->id}";} else {$url_type = $q->id; $q_link = $url_mapper['questions/view']. $q->id."#answer-{$a->id}";}
				
			if($a->anonymous == '1' ) {
				$quser_avatar = WEB_LINK.'public/img/anonymous.png';
			} else {
				$user = User::get_specific_id($a->user_id);
				if($user->avatar) {
					$img = File::get_specific_id($user->avatar);
					$quser_avatar = WEB_LINK."public/".$img->image_path();
					$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
					if (!file_exists($quser_avatar_path)) {
						$quser_avatar = WEB_LINK.'public/img/avatar.png';
					}
				} else {
					$quser_avatar = WEB_LINK.'public/img/avatar.png';
				}
			} 
				
				
				
				$like_class = 'like';
				$dislike_class = 'dislike';

				$like_txt = '';
				$liked = LikeRule::check_for_obj('answer' , "like" , $a->id, $current_user->id);
				if($liked) {
					$like_txt = '';
					$like_class = 'active undo-like';
					$dislike_class = 'dislike disabled';
				}

				$dislike_txt = '';
				$disliked = LikeRule::check_for_obj('answer' , "dislike" , $a->id, $current_user->id);
				if($disliked) {
					$dislike_txt = '';
					$like_class = 'like disabled';
					$dislike_class = 'active undo-dislike';
				}

				
		?>
		
		<?php 
		if($ads) {
			$r= array_rand($ads);
			$ad = $ads[$r];
			if($ad) {
				echo '<hr style="margin-bottom:10px">';
					if($ad->link) { echo "<a href='".WEB_LINK."ad/run/{$ad->id}' target='_blank'>"; }
						$content = str_replace('\\','',$ad->content);
						$content = str_replace('<script','',$content);
						$content = str_replace('</script>','',$content);
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
						echo $content;
					if($ad->link) { echo "</a>"; }
					$ad->view();
				echo '<hr style="margin-top:10px">';
			}
		}
		?>
		
		<div class="question-element" id="answer-<?php echo $a->id; ?>">
		<div class="publisher">
			<?php if($a->published == 0) { ?><p class="badge badge-danger"><i class="fa fa-eye-slash"></i> <?php echo $lang['questions-pending-tag']; ?></p><?php } ?>
			
			<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px;margin-top:5px">
			<p class="name ">
				<?php if($a->anonymous == '1') { echo $lang['user-anonymous']; } else { ?>
				<b><a href="<?php echo $url_mapper['users/view'] . $a->user_id; ?>/"><?php echo $user->f_name . " " . $user->l_name; ?></a></b><?php if($user->last_seen >= (time() - 60000)) { echo "&nbsp;<i class='text-success fa fa-circle' style='font-size: 12px'></i>"; } ?><?php if($user->comment) { echo " " . $user->comment; } 
				
				?>
				
				<?php if($a->user_id != $current_user->id && $current_user->can_see_this('users.follow' , $group) ) { ?>
				<?php
				$u_follow_class = 'follow';
				$follow_txt = $lang['btn-follow'];
				$followed = FollowRule::check_for_obj('user' , $user->id, $current_user->id);
				if($followed) {
					$follow_txt = $lang['btn-followed'];
					$u_follow_class = 'active unfollow';
				}
				?>
				<a href="javascript:void(0);" target="" class="follow btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" data-obj="user" name="<?php echo $user->id; ?>" data-lbl="<?php echo $lang['btn-follow'] ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" value="<?php echo $user->follows; ?>" ><i class='fe fe-user-plus'></i> <?php echo $follow_txt; if($user->follows) { echo " · " . convert_to_k($user->follows); } ?></a>
				
				<?php } }?>
				<br><?php if($a->anonymous == 2) { ?><span class="badge badge-warning"><i class="fe fe-lock"></i> Private Answer</span> <?php } ?><small><?php if(!$a->anonymous) { echo '@'.$user->username . ' · '; } if($a->updated_at != "0000-00-00 00:00:00") { echo $lang['index-question-updated'] . ' ' . date_ago($a->updated_at); } else { echo $lang['index-question-created'] . ' ' . date_ago($a->created_at); }?></small>
			</p>
		</div>
		
		<?php $content = str_replace('\\','',$a->content);
				$content = str_replace('<script','',$content);
				$content = str_replace('</script>','',$content);
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
				$content = profanity_filter($content); 
		if($content != '') { ?><p class="question-content"><?php echo $content; ?></p><?php } ?>
		
		<p class="footer">
			<?php if($current_user->can_see_this('questions.interact' , $group)) { ?>
			
		<div class="btn-group  p-0" role="group" aria-label="question-tools">
			<a href="javascript:void(0);" class="answer_comment btn btn-icon btn-like-machine" data-toggle="tooltip" data-placement="top" data-answer_id="<?php echo $a->id; ?>" title="<?php echo $lang['index-question-answer']; ?>" ><i class="fe fe-edit"></i>&nbsp;<?php echo $lang['btn-reply']; ?></a>
			
			<?php if($current_user->id != $a->user_id) { ?><a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $like_class; ?>" data-obj="answer" name="<?php echo $a->id; ?>" value="<?php echo $a->likes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-likes']; ?>" ><i class="fe fe-thumbs-up"></i><?php if($a->likes) { echo "&nbsp;{$a->likes}"; }?></a>
			<a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $dislike_class; ?>" data-obj="answer" name="<?php echo $a->id; ?>" value="<?php echo $a->dislikes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-dislikes']; ?>"><i class="fe fe-thumbs-down"></i><?php if($a->dislikes) { echo "&nbsp;{$a->dislikes}"; }?></a><?php } ?>
				
				
				<div class="dropdown">
				  <button class="btn btn-icon btn-like-machine" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fe fe-more-horizontal"></i>
				  </button>
				  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<?php if($a->user_id == $current_user->id || $current_user->prvlg_group == '1') { ?>
							<?php if($a->published == 0) { ?>
							<?php if($current_user->can_see_this('pending.update' , $group)) { ?>
							<a href="<?php echo $url_mapper['answers/approve'] . $url_type . "&type=approve_answer&id={$a->id}&hash={$random_hash}"; ?>"  class="dropdown-item">Approve Answer</a>
							<div class="dropdown-divider"></div>
							<?php } ?>
							<?php } ?>
						
							<?php if($current_user->can_see_this('answers.update' , $group)) { ?><a href="<?php echo $url_mapper['answers/edit'] . $url_type; ?>&type=edit_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>#answer-question" class="dropdown-item" >Edit</a><?php } ?>
							<?php if($current_user->can_see_this('answers.delete' , $group)) { ?><a href="<?php echo $url_mapper['answers/delete'] . $url_type; ?>&type=delete_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>" onclick="return confirm('<?php echo $lang['answer-delete-alert']; ?>');"  class="dropdown-item">Delete</a><?php } ?>
						<a href="javascript:void(0);" class="dropdown-item copy-link" data-link="<?php echo $a_link; ?>">Copy Link</a>
						<div class="dropdown-divider"></div>
						<?php } ?>
						<?php $reported = Report::check_for_obj('answer' , $a->id, $current_user->id); ?>
						<?php if(!$reported) { ?>
						<a href="#report-a-<?php echo $a->id; ?>" class="dropdown-item" data-toggle="modal" ><?php echo $lang['questions-answer-report']; ?></a>
						<?php } else { ?>
						<a href="javascript:void(0);" class="dropdown-item text-muted text-center" style="width:250px; white-space: normal"><?php echo $lang['questions-report-reported']; ?></a>
						<?php } ?>
					</div>
				</div>
				<?php if(!$reported) { ?>
					<!-- Modal -->
					<div class="modal fade in" id="report-a-<?php echo $a->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel"><?php echo $lang['questions-answer-report']; ?></h5>
						  </div>
						  <form action="<?php  echo $url_mapper['questions/view']; echo $url_type; ?>" method="POST" >
						  <div class="modal-body">
								<div class="form-group">
									<div class="flag_reasons clearfix">
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Harassment: Not respectful towards a person or group" checked>
											<b>Harassment:</b> <span class="light_gray">Not respectful towards a person or group</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info"  value="Spam: Undisclosed promotion for a link or product">
											<b>Spam:</b> <span class="light_gray">Undisclosed promotion for a link or product</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Irrelevant: Does not address question that was asked">
											<b>Irrelevant:</b> <span class="light_gray">Does not address question that was asked</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info"  value="Plagiarism: Reusing content without attribution (link and blockquotes)">
											<b>Plagiarism:</b> <span class="light_gray">Reusing content without attribution (link and blockquotes)</span></label>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info"  value="Joke Answer: Not a sincere answer">
												<b>Joke Answer:</b> <span class="light_gray">Not a sincere answer</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Poorly Written: Bad formatting, grammar, and spelling">
											<b>Poorly Written:</b> <span class="light_gray">Bad formatting, grammar, and spelling</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Incorrect: Substantially incorrect and/or incorrect primary conclusions">
											<b>Incorrect:</b> <span class="light_gray">Substantially incorrect and/or incorrect primary conclusions</span>
										  </label>
										</div>
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
								echo "<input type=\"hidden\" name=\"id\" value=\"".$a->id."\" readonly/>";
								echo "<input type=\"hidden\" name=\"obj_type\" value=\"answer\" readonly/>";
							?>
						  </form>
						</div>
					  </div>
					</div>
					<?php } ?>
				
				
				
				  </div><?php } ?>
				  <div class="answer-box-<?php echo $a->id; ?>" style="clear:both; display:none"></div>
				  
			
		</p>
		
		
		<?php 
			$sub_answers = Answer::get_sub_answers_for($a->id); 
			//if($sub_answers && $current_user->id != 1000 ) {
			if($sub_answers) {
				foreach($sub_answers as $a) {
					
			if($a->published == '1' &&  $a->anonymous != '2' || $a->published == '1' &&  $a->anonymous == '2' && $current_user->id == $a->user_id || $a->published == '1' &&  $a->anonymous == '2' && $current_user->id == $q->user_id || $a->published == '1' &&  $a->anonymous == '2' && $current_user->prvlg_group == "1"  || $a->published == '0' && $current_user->id == $a->user_id || $a->published == '0' && $current_user->prvlg_group == '1' ) {
				
				if(URLTYPE == 'slug') {$a_link = $url_mapper['questions/view']. $q->slug."#answer-{$a->id}";} else {$url_type = $q->id; $q_link = $url_mapper['questions/view']. $q->id."#answer-{$a->id}";}
				
			if($a->anonymous) {
				$quser_avatar = WEB_LINK.'public/img/anonymous.png';
			} else {
				$user = User::get_specific_id($a->user_id);
				if($user->avatar) {
					$img = File::get_specific_id($user->avatar);
					$quser_avatar = WEB_LINK."public/".$img->image_path();
					$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
					if (!file_exists($quser_avatar_path)) {
						$quser_avatar = WEB_LINK.'public/img/avatar.png';
					}
				} else {
					$quser_avatar = WEB_LINK.'public/img/avatar.png';
				}
			} 
				
				
				
				$like_class = 'like';
				$dislike_class = 'dislike';

				$like_txt = '';
				$liked = LikeRule::check_for_obj('answer' , "like" , $a->id, $current_user->id);
				if($liked) {
					$like_txt = '';
					$like_class = 'active undo-like';
					$dislike_class = 'dislike disabled';
				}

				$dislike_txt = '';
				$disliked = LikeRule::check_for_obj('answer' , "dislike" , $a->id, $current_user->id);
				if($disliked) {
					$dislike_txt = '';
					$like_class = 'like disabled';
					$dislike_class = 'active undo-dislike';
				}
					
		?>
		
		<div class="question-element sub-answer" id="answer-<?php echo $a->id; ?>">
		<div class="publisher">
			<?php if($a->published == 0) { ?><p class="badge badge-danger"><i class="fa fa-eye-slash"></i> <?php echo $lang['questions-pending-tag']; ?></p><?php } ?>
			
			<?php if($a->anonymous == 2) { ?><p class="badge badge-warning"><i class="fe fe-lock"></i> Private Answer</p><?php } ?>
			
			<img src="<?php echo $quser_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px;margin-top:5px">
			<p class="name ">
				<?php if($a->anonymous) { echo $lang['user-anonymous']; } else { ?>
				<b><a href="<?php echo $url_mapper['users/view'] . $a->user_id; ?>/"><?php echo $user->f_name . " " . $user->l_name; ?></a></b><?php if($user->last_seen >= (time() - 60000)) { echo "&nbsp;<i class='text-success fa fa-circle' style='font-size: 12px'></i>"; } ?><?php if($user->comment) { echo " " . $user->comment; } 
				
				?>
				
				<?php if($a->user_id != $current_user->id && $current_user->can_see_this('users.follow' , $group) ) { ?>
				<?php
				$u_follow_class = 'follow';
				$follow_txt = $lang['btn-follow'];
				$followed = FollowRule::check_for_obj('user' , $user->id, $current_user->id);
				if($followed) {
					$follow_txt = $lang['btn-followed'];
					$u_follow_class = 'active unfollow';
				}
				?>
				<a href="javascript:void(0);" target="" class="follow btn btn-sm btn-secondary text-dark text-decoration-none <?php echo $u_follow_class; ?>" data-obj="user" name="<?php echo $user->id; ?>" data-lbl="<?php echo $lang['btn-follow'] ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" value="<?php echo $user->follows; ?>" ><i class='fe fe-user-plus'></i> <?php echo $follow_txt; if($user->follows) { echo " · " . convert_to_k($user->follows); } ?></a>
				
				<a href="javascript:void(0);" target="" class="btn btn-sm btn-secondary text-dark text-decoration-none" ><i class='fe fe-message-circle'></i> Message</a>
				<?php } }?>
				<br><small><?php if(!$a->anonymous) { echo '@'.$user->username . ' · '; } if($a->updated_at != "0000-00-00 00:00:00") { echo $lang['index-question-updated'] . ' ' . date_ago($a->updated_at); } else { echo $lang['index-question-created'] . ' ' . date_ago($a->created_at); }?></small>
			</p>
		</div>
		
		<?php $content = str_replace('\\','',$a->content);
				$content = str_replace('<script','',$content);
				$content = str_replace('</script>','',$content);
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
				$content = profanity_filter($content); 
		if($content != '') { ?><p class="question-content"><?php echo $content; ?></p><?php } ?>
		
		<p class="footer">
			<?php if($current_user->can_see_this('questions.interact' , $group)) { ?>
			
		<div class="btn-group  p-0" role="group" aria-label="question-tools">
			<a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $like_class; ?>" data-obj="answer" name="<?php echo $a->id; ?>" value="<?php echo $a->likes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-likes']; ?>" ><i class="fe fe-thumbs-up"></i><?php if($a->likes) { echo "&nbsp;{$a->likes}"; }?></a>
				<a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $dislike_class; ?>" data-obj="answer" name="<?php echo $a->id; ?>" value="<?php echo $a->dislikes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-dislikes']; ?>"><i class="fe fe-thumbs-down"></i><?php if($a->dislikes) { echo "&nbsp;{$a->dislikes}"; }?></a>
				
				
				<div class="dropdown">
				  <button class="btn btn-icon btn-like-machine" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fe fe-more-horizontal"></i>
				  </button>
				  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<?php if($a->user_id == $current_user->id || $current_user->prvlg_group == '1') { ?>
							<?php if($a->published == 0) { ?>
							<?php if($current_user->can_see_this('pending.update' , $group)) { ?>
							<a href="<?php echo $url_mapper['answers/approve'] . $url_type . "&type=approve_answer&id={$a->id}&hash={$random_hash}"; ?>"  class="dropdown-item">Approve Answer</a>
							<div class="dropdown-divider"></div>
							<?php } ?>
							<?php } ?>
						
							<?php if($current_user->can_see_this('answers.update' , $group)) { ?><a href="<?php echo $url_mapper['answers/edit'] . $url_type; ?>&type=edit_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>#answer-question" class="dropdown-item" >Edit</a><?php } ?>
							<?php if($current_user->can_see_this('answers.delete' , $group)) { ?><a href="<?php echo $url_mapper['answers/delete'] . $url_type; ?>&type=delete_answer&id=<?php echo $a->id; ?>&hash=<?php echo $random_hash; ?>" onclick="return confirm('<?php echo $lang['answer-delete-alert']; ?>');"  class="dropdown-item">Delete</a><?php } ?>
						<a href="javascript:void(0);" class="dropdown-item copy-link" data-link="<?php echo $a_link; ?>">Copy Link</a>
						<div class="dropdown-divider"></div>
						<?php } ?>
						<?php $reported = Report::check_for_obj('answer' , $a->id, $current_user->id); ?>
						<?php if(!$reported) { ?>
						<a href="#report-a-<?php echo $a->id; ?>" class="dropdown-item" data-toggle="modal" ><?php echo $lang['questions-answer-report']; ?></a>
						<?php } else { ?>
						<a href="javascript:void(0);" class="dropdown-item text-muted text-center" style="width:250px; white-space: normal"><?php echo $lang['questions-report-reported']; ?></a>
						<?php } ?>
					</div>
				</div>
				<?php if(!$reported) { ?>
					<!-- Modal -->
					<div class="modal fade in" id="report-a-<?php echo $a->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
					  <div class="modal-dialog modal-lg" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel"><?php echo $lang['questions-answer-report']; ?></h5>
						  </div>
						  <form action="<?php  echo $url_mapper['questions/view']; echo $url_type; ?>" method="POST" >
						  <div class="modal-body">
								<div class="form-group">
									<div class="flag_reasons clearfix">
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Harassment: Not respectful towards a person or group" checked>
											<b>Harassment:</b> <span class="light_gray">Not respectful towards a person or group</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Spam: Undisclosed promotion for a link or product">
											<b>Spam:</b> <span class="light_gray">Undisclosed promotion for a link or product</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Irrelevant: Does not address question that was asked">
											<b>Irrelevant:</b> <span class="light_gray">Does not address question that was asked</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info"  value="Plagiarism: Reusing content without attribution (link and blockquotes)">
											<b>Plagiarism:</b> <span class="light_gray">Reusing content without attribution (link and blockquotes)</span></label>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Joke Answer: Not a sincere answer">
												<b>Joke Answer:</b> <span class="light_gray">Not a sincere answer</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Poorly Written: Bad formatting, grammar, and spelling">
											<b>Poorly Written:</b> <span class="light_gray">Bad formatting, grammar, and spelling</span>
										  </label>
										</div>
										<div class="radio">
										  <label>
											<input type="radio" name="info" value="Incorrect: Substantially incorrect and/or incorrect primary conclusions">
											<b>Incorrect:</b> <span class="light_gray">Substantially incorrect and/or incorrect primary conclusions</span>
										  </label>
										</div>
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
								echo "<input type=\"hidden\" name=\"id\" value=\"".$a->id."\" readonly/>";
								echo "<input type=\"hidden\" name=\"obj_type\" value=\"answer\" readonly/>";
							?>
						  </form>
						</div>
					  </div>
					</div>
					<?php } ?>
				
				
				
				  </div>
				  
			<?php } ?>
		</p>
	</div>
	<?php }}} ?></div>
		
		<?php $t++; echo "<hr>"; } }
		
		if(isset($pagination) && $pagination->total_pages() > 1) {
		?>
		<div class="pagination btn-group">
		
				<?php
				if ($pagination->has_previous_page()) {
					$page_param = $url_mapper['questions/view'].$url_type.'&page=';
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
						$page_param = $url_mapper['questions/view'].$url_type.'&page=';
						$page_param .= $p;
						echo "<a href=\"{$page_param}\" class=\"btn btn-secondary\" type=\"button\">{$p}</a>";
					}
				}
				if($pagination->has_next_page()) {
					$page_param = $url_mapper['questions/view'].$url_type.'&page=';
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
		
		}
		
		if($current_user->id == 1000) {
			
			if(isset($_SESSION[$elhash_login]) && $_SESSION[$elhash_login] != "") { 
				$random_hash = $_SESSION[$elhash_login];
			} else {
				$random_hash = uniqid();
				$_SESSION[$elhash_login] = $random_hash;
			}
		?>
		<div class="p-3 card" style='width:100%;background-color: #f1f2f2'>
			<h5>Please login to add your answer</h5>
			<form method="POST" action="<?php echo $url_mapper['login/']; ?>" >
  <div class="form-row align-items-center mt-2 col-12" >
    <div class="col-lg-4 col-sm-6">
      <label class="sr-only" for="inlineFormInput">Email</label>
      <input type="email" class="form-control mb-2" id="inlineFormInput" name="email" placeholder="Email">
    </div>
    <div class="col-lg-4 col-sm-6">
      <label class="sr-only" for="inlineFormInput">Password</label>
      <input type="password" class="form-control mb-2" id="inlineFormInput" name="password" placeholder="Password">
      
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="autoSizingCheck">
        <label class="form-check-label" for="autoSizingCheck">
          Remember me
        </label>
      </div>
    </div>
    <div class="col-lg-1 col-sm-6" >
      <button type="submit" name="enterlogin" class="btn btn-primary mb-2 pull-right">Submit</button>
    </div>
  </div><?php echo "<input type=\"hidden\" name=\"loginhash\" value=\"".$random_hash."\" readonly/>"; 
  echo "<input type=\"hidden\" name=\"next\" value=\"".$q_link."\" readonly/>"; 
  ?>
</form>
		</div>
		
		<?php } else {
			if($current_user->can_see_this("answers.create",$group)) { ?>
		<a name="answer-question" id="answer-question"></a>
		<form action="<?php echo $url_mapper['questions/view']. $url_type; ?>" method="post" role="form" enctype="multipart/form-data" class="facebook-share-box">
		<div class="">
			<ul class="post-types">
				<li class="post-type">
					<p class="publisher">
						<img src="<?php echo $user_avatar; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:46px;margin-<?php echo $lang['direction-right']; ?>:10px">
						<p class="name">
							<a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/"><?php echo $current_user->f_name . ' ' . $current_user->l_name; ?></a>
						</p>
					</p>
				</li>
			</ul>
			<div class="share">
				<div class="arrow"></div>
				<div class="panel panel-default">
                      <div class="panel-body">
                        <div class="">
                            <textarea name="title" cols="40" rows="10" class="summernote" style="height: 62px; overflow: hidden;" placeholder="What's on your mind ?" required>
							<?php if(isset($edit_answer_mode)) { echo str_replace('\\' , '' , $edited_answer->content); } ?>
							</textarea> 
						</div>
                      </div>
						<div class="panel-footer">
							<div class="form-group mb-0" style="position:relative">
								<?php if(isset($edit_answer_mode)) { $answer_value=$lang['questions-answer-update']; } else { $answer_value= $lang['questions-answer-create']; } ?>
								
								<p class="m-2" style="float: <?php echo $lang['direction-right']; ?>"><select class="nice-select sm" name="post-type">
									<option value="public" data-html="<i class='fe fe-users'></i> <?php echo $lang['questions-public']; ?>"> <?php echo $lang['questions-public']; ?></option>
									<option value="anonymous" data-html="<i class='fe fe-loader'></i> <?php echo $lang['questions-anonymous']; ?>"><i class='fe fe-loader'></i> <?php echo $lang['questions-anonymous']; ?></option>
									<option value="private" data-html="<i class='fe fe-lock'></i> <?php echo $lang['questions-private']; ?>"><i class='fe fe-lock'></i> <?php echo $lang['questions-private']; ?></option>
								</select>
								<input type="submit" name="add_a" value="<?php echo $answer_value; ?>" style="" class="btn btn-primary btn-sm btn-pill m-2"></p>
								<div style="clear:both"></div>
								<?php 
									$_SESSION[$elhash] = $random_hash;
									if(isset($edit_answer_mode)) { echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$edited_answer->id."\" readonly/>"; }
									echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
								?>
							</div>									
						</div>
                    </div>
			</div>
			</div>
		</form>
		<?php } } ?>
	</div>
	</div>


	
	

</div>

<div class="col-md-3">
<div class="card">
			  <div class="card-body">
	  <h5><i class='fe fe-user'></i> <?php echo $lang['index-sidebar-welcome']; ?>, <?php echo $current_user->f_name; ?>!</h5>
	  <hr>
		<ul class="nav-ul" >
			<li><a href="<?php echo $url_mapper['pages/view']; ?>about_us" ><i class='fe fe-alert-circle'></i> <?php echo $lang['pages-about-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['pages/view']; ?>contact_us" ><i class='fe fe-message-circle'></i> <?php echo $lang['pages-contact-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['pages/view']; ?>privacy_policy" ><i class='fe fe-clipboard'></i> <?php echo $lang['pages-privacy-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['pages/view']; ?>terms" ><i class='fe fe-layers'></i> <?php echo $lang['pages-terms-title']; ?></a></li>
			<li><a href="<?php echo $url_mapper['leaderboard/']; ?>" ><i class='fe fe-bar-chart'></i> <?php echo $lang['pages-leaderboard-title']; ?></a></li>
		</ul>
	  </div>	  
</div>
<?php
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
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
					echo $content;
				if($ad->link) { echo "</a>"; }
				$ad->view();
			echo '<hr>';
		}
	} else {
		echo "<br style='clear:both'>";
	}
?><div class="card">
			  <div class="card-body">
	  <h5><i class='fe fe-alert-triangle'></i> <?php echo $lang['index-sidebar-related_questions']; ?></h5>
	  <hr>
		<ul class="nav-ul" >
			<?php
				$questions = Question::get_related_questions_for($q->feed ," LIMIT 10 " );
				if($questions) {
					foreach($questions as $q) {
						if(URLTYPE == 'slug') {
							$url_type2 = $q->slug;
						} else {
							$url_type2 = $q->id;
						}
						
						$string=strip_tags($q->title);
						if (strlen($string) > 50) {
							$stringCut = substr($string, 0, 50);
							$string = substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
						}
						
						?>
						<li><a href="<?php echo $url_mapper['questions/view']; echo $url_type2; ?>" class="col-md-12"><?php echo $string; ?></a></li>
						<?php
					}
				}
			?>
			</ul>
	  </div>	  
</div>
<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
</div>
</div><br style="clear:both"><br style="clear:both"><br style="clear:both"><br style="clear:both">

    </div>
	<!-- /container -->
    <?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
	<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-bs4.js"></script>
    <script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-emoji/summernote-emoji.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
	<script src="<?php echo WEB_LINK; ?>public/plugins/Emoji/jquery.emotions.js"></script>
	<script>
    $(document).ready(function() {
		
		$(".question-element").emotions();
		
		$('<div id="loading_wrap"><div class="com_loading"><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" /> Loading ...</center></div></div>').appendTo('body');

        $('.summernote').summernote({
			toolbar:[
			  	['custom', ['emojiList']],
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['insert', ['link', 'picture', 'video']]
			],
			height: 150,
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0]);
				}
			},
			hint: {
					mentions: function(keyword, callback) {
						$.ajax({
							dataType: 'json',
							data: {id:<?php echo $current_user->id; ?>, data: keyword , hash:'<?php echo $random_hash; ?>'},
							type: "POST",
							url: "<?php echo WEB_LINK ?>public/includes/one_ajax.php?type=mention",
							async: true, //This works but freezes the UI
							success:function(data) {
							  //console.log(data); 
							}
						}).done(callback);
					},
					match: /\B@(\w*)$/,
					search: function (keyword, callback) {
						this.mentions(keyword, callback); //callback must be an array
					},
					template: function (item) {
						return item.name;
					},
					content: function (item) {
						return $('<a href="'+ item.link +'" class="mentionned" target="_blank">@' + item.name + '</a>')[0];
					}
			  }
        });
		
		function sendFile(image) {
            $("#loading_wrap").fadeIn("fast");

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
                    $('.summernote').summernote("insertImage", url);
					$("#loading_wrap").fadeOut("fast");
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
		$('.select2').select2();
	});
	$('a#answer-btn').click(function(){
		scrollToAnchor('answer-question');
		$('.note-editable').trigger('focus');
	});
	
	$('.card-body').on('click' , '.answer_comment' , function() {
		
		var answer_id = $(this).data("answer_id");
		
		if($('.answer-box-'+answer_id).html() == "") {
		
			$('.answer-box-'+answer_id).html('<form action="<?php echo $url_mapper["questions/view"]. $url_type; ?>" method="post" role="form" enctype="multipart/form-data" class="facebook-share-box"><div class=""><div class="share"><div class="arrow"></div><div class="panel panel-default"><div class="panel-body"><div class=""><textarea name="title" cols="40" rows="10" class="summernote-'+answer_id+'" style="height: 62px; overflow: hidden;" placeholder="Whats on your mind ?" required></textarea> </div></div><div class="panel-footer"><div class="form-group"><p class="m-2" style="float: <?php echo $lang['direction-right']; ?>"><select class="sub-nice-select sm" name="post-type"><option value="public" data-html="<i class=\'fe fe-users\'></i> Public"><?php echo $lang['questions-public']; ?></option><option value="anonymous" data-html="<i class=\'fe fe-loader\'></i> Anonymous"><i class="fe fe-loader"></i> <?php echo $lang['questions-anonymous']; ?></option></select><input type="submit" name="add_a" value="<?php echo $lang['questions-answer-create'];?>" style="" class="btn btn-primary btn-sm btn-pill m-2"></p><div style="clear:both"></div><?php $_SESSION[$elhash] = $random_hash; echo "<input type=\"hidden\" name=\"a_id\" value=\"'+answer_id+'\" readonly/>"; echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";?></div></div></div></div></div></form>');
			$('.sub-nice-select').niceSelect();

		function sendFile(image) {
            $("#loading_wrap").fadeIn("fast");

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
                    $('.summernote-'+answer_id).summernote("insertImage", url);
					$("#loading_wrap").fadeOut("fast");
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
			
			$('.summernote-'+answer_id).summernote({
				toolbar:[
					['custom', ['emojiList']],
					['style', ['bold', 'italic', 'underline', 'clear']],
					['font', ['strikethrough', 'superscript', 'subscript']],
					['fontsize', ['fontsize']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['insert', ['link', 'picture', 'video']]
				],
				height: 150,
				callbacks : {
					onImageUpload: function(image) {
						sendFile(image[0]);
					}
				},
				hint: {
						mentions: function(keyword, callback) {
							$.ajax({
								dataType: 'json',
								data: {id:<?php echo $current_user->id; ?>, data: keyword , hash:'<?php echo $random_hash; ?>'},
								type: "POST",
								url: "<?php echo WEB_LINK ?>public/includes/one_ajax.php?type=mention",
								async: true, //This works but freezes the UI
								success:function(data) {
								  //console.log(data); 
								}
							}).done(callback);
						},
						match: /\B@(\w*)$/,
						search: function (keyword, callback) {
							this.mentions(keyword, callback); //callback must be an array
						},
						template: function (item) {
							return item.name;
						},
						content: function (item) {
							return $('<a href="'+ item.link +'" class="mentionned" target="_blank">@' + item.name + '</a>')[0];
						}
				  }
			});
			
		}
		
		$('.answer-box-'+answer_id).slideToggle('');
		
	});
	
	$(document).ready(function(){
		$("img[id != 'site-logo']").addClass("img-fluid");
	});
	
	function copyToClipboard(text) {
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(text).select();
		document.execCommand("copy");
		$temp.remove();
	}
	
	$('.copy-link').click(function () { copyToClipboard($(this).data('link')); generateSwal("Link Copied!","success"); });
	
	
<?php if($space) { ?>
	
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
<?php } ?>

if(window.location.hash) {
	scrollToId(window.location.hash);
}

	</script>
	<?php require_once(VIEW_PATH.'pages/like-machine.php'); ?>
	<?php require_once(VIEW_PATH.'pages/footer.php'); ?>

<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>