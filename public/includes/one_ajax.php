<?php
require_once("../../library/pearls.php");

if ($session->is_logged_in() != true ) {
	if ($settings['public_access'] == '1') {
		$current_user = User::get_specific_id(1000);
	} else {
		redirect_to($url_mapper['login/']); 
	}
} else {
	$current_user = User::get_specific_id($session->admin_id);
}

$group = $current_user->prvlg_group;
if(!isset($settings['site_lang'])) { $settings['site_lang'] = 'English'; }
require_once(LIBRARY_PATH."/lang/lang.{$settings['site_lang']}.php");

if (isset($_GET['type']) && !empty($_GET['type']) && isset($_POST['hash']) && !empty($_POST['hash']) && isset($_POST['data']) && !empty($_POST['data']) && isset($_POST['id']) && is_numeric($_POST['id'])) {
	$id = $db->escape_value($_POST['id']);
	$type = $db->escape_value($_GET['type']);
	$hash = $db->escape_value($_POST['hash']);
	$data = $db->escape_value($_POST['data']);
	
	switch($type) {
		###############################################################
			case 'chat-heads' :
				$chat = Chat::get_chatheads();
				if($chat) {
					$m = 1;
					foreach($chat as $unread) {
						$count = Chat::count_everything(" AND sender = '{$unread->sender}' AND receiver = '{$current_user->id}' AND viewed = 0 ");
						$dev_profile = User::get_specific_id($unread->sender);
					?>
					<a href="javascript:void(0);" style="position:fixed; bottom:0; left:<?php echo ($m*5)+20; ?>%;" class="open-chat" data-user_id="<?php echo $unread->sender; ?>" ><img src="<?php echo $dev_profile->get_avatar(); ?>" class="chat-head img-circle pull-left" alt="User Image" style="border:3px solid white; width:50px;margin-left:-55px">
					<span class="label label-danger pull-left" style="margin-left:-60px"><?php echo $count; ?></span></a>
					<?php
					$m++;
					}					
				}
			break;
		###############################################################
			case 'chat-names' :
				$current_user->set_online();
				
				$chat = User::get_chat($current_user->ban_list);
				$count = count($chat);
					
						echo "<input type='checkbox' /><label data-expanded='{$lang['index-chat-title']} ({$count})' data-collapsed='{$lang['index-chat-title']} ({$count})'></label><div class='chat-box-content'><ul class='feed-ul'>";
						if($chat) { foreach($chat as $unread) {
							$count = Chat::count_everything(" AND sender = '{$unread->id}' AND receiver = '{$current_user->id}' AND viewed = 0 ");
							$dev_profile = User::get_specific_id($unread->id);
						echo "<li><a href='#me' class='open-chat col-xs-12' data-toggle='control-sidebar' data-user_id='{$unread->id}'><i class='fa fa-circle text-success'></i> <span>{$dev_profile->f_name} {$dev_profile->l_name}</span>";
						if($count > 0) { 
						echo "<span class='label label-danger pull-right' style='margin-top:7px'>{$count}</span>";
						}
						echo "</a></li>";
						} } else { echo "<br><li><center style='color:#5e5e5e'>{$lang['index-chat-no_friends']}</center><li>"; }
						echo '</ul></div>';
					
			break;
		
			###############################################################
			case 'open-chat' :
				if(!User::check_id_existance($data)) {
					return false;
					die();
				}
				$banned_list = str_replace('-' , '' , $current_user->ban_list);
				$banned = explode("," , $banned_list);
				
				if(in_array($data , $banned)) {
					echo "<h3 style='color:#ccc; padding:10px'><center>You can't reply to this conversation anymore!<br>This user is currently banned from chatting with you</center></h3>";
					?>
					<script> $('.box-footer').hide(); </script>
					<?php
					die();
				}
				
				$count = Chat::count_everything(" AND sender = '{$data}' AND receiver = '{$current_user->id}' AND viewed = 0 ");
				if($count <= 20) { $count = 20; }
				
				$chat = Chat::get_everything(" AND (sender = '{$current_user->id}' AND receiver = '{$data}' OR sender = '{$data}' AND receiver = '{$current_user->id}' )" , " ORDER BY sent_at DESC LIMIT {$count} ");
				
				echo "<a href='#me' id='ban-user' class='btn btn-danger col-xs-12' data-user_id='{$data}' >Ban User</a><br><br>";
				echo "<div style='padding:20px'>";
				if($chat) {
					$chat = array_reverse ($chat);
					foreach($chat as $msg) {
						$sender = User::get_specific_id($msg->sender);
						?>
						<div class="direct-chat-msg <?php if($msg->sender == $current_user->id) { echo ' right'; } ?>">
							  <div class="direct-chat-info clearfix">
								<span class="direct-chat-name pull-left"><?php echo $sender->f_name; ?></span>
								<span class="direct-chat-timestamp pull-right"><?php echo date_ago($msg->sent_at); ?></span>
							  </div>
							  <!-- /.direct-chat-info -->
							  <img class="direct-chat-img" src="<?php echo $sender->get_avatar(); ?>" alt="Message User Image"><!-- /.direct-chat-img -->
							  <div class="direct-chat-text <?php if($msg->sender == $current_user->id) { echo ' bg-blue'; } ?>">
								<?php echo $msg->msg; ?>
							  </div>
							  <?php if($msg->viewed) {  ?><span class="<?php if($msg->sender == $current_user->id) { echo ' pull-left'; } else { echo ' pull-right';  } ?> text-aqua" style='font-size:10px'><i class="fa fa-check"></i> Seen</span><?php } ?>
							  <!-- /.direct-chat-text -->
							</div>
						<?php
						if($msg->receiver == $current_user->id) {
							$msg->viewed = 1;
							$msg->update();
						}
					}
				}
				?>
			</div>
				<?php 
					$_SESSION[$elhash] = $hash;
					echo "<input type=\"hidden\" name=\"receiver\" id=\"chat-receiver\" value=\"".$data."\" readonly/>";
					echo "<input type=\"hidden\" name=\"hash\" value=\"".$hash."\" readonly/>";
				?>

  <script> 
        // wait for the DOM to be loaded 
        $(document).ready(function() {
            $('#chatForm').ajaxForm(function(responseText) {
				$("#chat-msg").val('');
				$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=open-chat", {id:1, data: <?php echo $data; ?> , hash:'<?php echo $hash; ?>'}, function(response){ $('.chat-receptor').html(response); var scrollTo_val = $('.slimscroll2').prop('scrollHeight') + 'px';
				$('.slimscroll2').slimScroll({ scrollTo : scrollTo_val });});
            });
			
			$("#ban-user").click(function() {
				var user_id = $(this).data("user_id");
				if(confirm("Are you sure you want to ban this user from chatting with you again?")) {
					$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=ban_user", {id:1, data: user_id , hash:'<?php echo $hash; ?>'}, function(){});
				}
			});
			
        }); 
    </script>
	
				<?php
			break;

		###############################################################
		case 'ban_user' :
			
			if($current_user->ban_list) {
				$ban_list = explode(","  , $current_user->ban_list);
				$ban_list[] = "-" . $data ."-";
				$current_user->ban_list = implode(",", $ban_list);
				$current_user->update();
			} else {
				$current_user->ban_list = "-".$data."-";
				$current_user->update();
			}
			
			
			//target_user:
			$target = User::get_specific_id($data);
			if($target) {				
				if($target->ban_list) {
					$ban_list = explode(","  , $target->ban_list);
					$ban_list[] = "-".$current_user->id."-";
					$target->ban_list = implode(",", $ban_list);
					$target->update();
				} else {
					$target->ban_list = "-".$current_user->id."-";
					$target->update();
				}
			}
			
			
		break;
		###############################################################
		case 'mention' :
			
			$result = User::find_username( $data , $current_user->id, " LIMIT 5");
			$return = Array();
			foreach($result as $r) {
				$e = array();
				$e['name'] = $r->username;
				$e['link'] =  $url_mapper['users/view']. $r->id .'/';
				
				array_push($return, $e);
			}
			
			if(!empty($return)) {$json = json_encode($return);
				echo $json;
			} else { return false; }
			
		break;
		###############################################################
		case 'follow' :
		
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			
			if($found) {
				
				//Check prev like..
				$prev_follow = FollowRule::get_for_obj($data , $id, $current_user->id);
				if(!$prev_follow) {
					//Create like..
					$like = New FollowRule();
					$like->user_id = $current_user->id;
					$like->obj_id = $id;
					$like->obj_type = $data;
					$like->follow_date = strftime("%Y-%m-%d %H:%M:%S", time());
					$like->create();
					
					###############
					## FOLLOW NOTIF ##
					###############
					
					if($classname == 'User') {
						$notif_link = $url_mapper['users/view']. $current_user->id.'/';
						$str = $lang['notif-user-follow-msg']; $str = str_replace('[NAME]' , $current_user->f_name , $str); $str = str_replace('[LINK]' , $url_mapper['users/view'] . $current_user->id , $str); 
						$notif_msg = $str;
						$notif_user = $id;
						$receiver = User::get_specific_id($notif_user);
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						$award = Award::send_award($notif_user,$notif_msg . ", {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-user-follow-title'];
						if($receiver && is_object($receiver) && $receiver->can_receive_this('new-user-follow') ) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Question') {
						$receiver = User::get_specific_id($found->user_id);
						if(URLTYPE == 'slug') {
							$url_type = $found->slug;
						} else {
							$url_type = $found->id;
						}
						$str= $lang['notif-q_f_award-msg']; $str = str_replace('[LINK]' , $url_mapper['users/view'].$current_user->id , $str ); $str = str_replace('[Q_LINK]' , $url_mapper['questions/view'] . $url_type, $str ); $str = str_replace('[NAME]' , $current_user->f_name , $str );
						$award = Award::send_award($id, "{$str} , {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						
						$notif_link = $url_mapper['users/view']. $current_user->id.'/';
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-q_f_award-title'];
						if($receiver && is_object($receiver)  && $receiver->can_receive_this('new-question-follow') ) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
					}
					$found->follows +=1;
					$found->update();
				}
			}
		break;
		
		case 'unfollow' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = FollowRule::get_for_obj($data , $id, $current_user->id);
				if($prev_likes) {
					$prev_likes->delete();
					$found->follows -=1;
					$found->update();
				}
			}
		break;
		
		###############################################################
		case 'share' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				$found->shares += 1;
				$found->update();
			}
		break;
		###############################################################
		case 'like' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "like" , $id, $current_user->id);
				if(!$prev_likes) {
					//Create like..
					$like = New LikeRule();
					$like->user_id = $current_user->id;
					$like->obj_id = $id;
					$like->obj_type = $data;
					$like->type = 'like';
					$like->like_date = strftime("%Y-%m-%d %H:%M:%S", time());
					$like->create();
					
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {
							$url_type = $found->slug;
						} else {
							$url_type = $found->id;
						}
						$receiver = User::get_specific_id($found->user_id);
						$str= $lang['notif-q_l_award']; $str = str_replace('[LINK]' , $url_mapper['users/view'].$current_user->id , $str ); $str = str_replace('[Q_LINK]' , $url_mapper['questions/view'] . $url_type, $str ); $str = str_replace('[NAME]' , $current_user->f_name , $str );
						$award = Award::send_award($found->user_id,"{$str} , {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						
						#######
						# NOTIF #
						#######
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {
							$url_type = $q->slug;
						} else {
							$url_type = $q->id;
						}
						$receiver = User::get_specific_id($found->user_id);
						$str= $lang['notif-a_l_award']; $str = str_replace('[LINK]' , $url_mapper['users/view'].$current_user->id , $str ); $str = str_replace('[Q_LINK]' , $url_mapper['questions/view'] . $url_type . "#answer-{$found->id}" , $str ); $str = str_replace('[NAME]' , $current_user->f_name , $str );
						$award = Award::send_award($found->user_id,"{$str} , {$lang['notif-award']} <b>1</b> {$lang['notif-point']}" );
						$receiver->award_points(1);
						
						#######
						# NOTIF #
						#######
						//if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type .'#answer-'.$id;
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						
					}
					
					$found->likes +=1;
					$found->update();
					
				}
			}
		break;
		
		case 'unlike' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "like" , $id, $current_user->id);
				if($prev_likes) {
					//Create like..
					$prev_likes->delete();
					$found->likes -=1;
					$found->update();
				}
			}
		break;
		
		###############################################################
		case 'approve' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
					
					###############
					## APPROVE NOTIF ##
					###############
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$str = $lang['notif-q_publish-msg']; $str = str_replace('[TITLE]' , $found->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-q_publish-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this("approve-question") ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$str = $lang['notif-a_publish-msg']; $str = str_replace('[TITLE]' , $q->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-a_publish-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this("approve-answer") ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					
					$found->published =1;
					if($found->update()) {
						$msg = $lang['alert-update_success'];
						$response = array('title'=>$lang['alert-type-success'].'!','type' => 'success' , 'msg' => $msg);
					} else {
						$msg = $lang['alert-update_failed'];
						$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					}
					echo json_encode($response); die();
			}
		break;
		
		case 'reject' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
					###############
					## APPROVE NOTIF ##
					###############
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$str = $lang['notif-q_reject-msg']; $str = str_replace('[TITLE]' , $found->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-q_reject-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this("reject-question")) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$str = $lang['notif-a_reject-msg']; $str = str_replace('[TITLE]' , $q->title , $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-a_reject-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this("reject-answer")) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					
					if($found->delete()) {
						$msg = $lang['alert-delete_success'];
						$response = array('title'=>$lang['alert-type-success'].'!','type' => 'success' , 'msg' => $msg);
					} else {
						$msg = $lang['alert-delete_failed'];
						$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					}
					echo json_encode($response); die();
			}
		break;
		
		###############################################################
		case 'approve-report' :
			$classname = ucfirst($data);
			$report = Report::get_specific_id($_POST['report_id']);
			$found = $classname::get_specific_id($id);
			if($found) {
					
					####################
					## APPROVE REPORT NOTIF ##
					####################
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						//$notif_link = $url_mapper['questions/view'].$url_type;
						$notif_link = $url_mapper['pages/view'].'terms';
						
						$str = $lang['notif-report-q_publisher-approve-msg'];
						$str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->title."</a>", $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = $lang['notif-report-q_publisher-approve-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this('report-my-questions') ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						
						$str = $lang['notif-report-q_reporter-approve-msg']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->title."</a>" , $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title =$lang['notif-report-q_reporter-approve-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)  && $receiver->can_receive_this('report-others-questions')  ) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						//$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$notif_link = $url_mapper['pages/view'].'terms';
						
						$str = $lang['notif-report-a_publisher-approve-msg']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$q->title."</a>" , $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = $lang['notif-report-a_publisher-approve-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)  && $receiver->can_receive_this('report-my-answers')  ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						$str = $lang['notif-report-a_reporter-approve-msg']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$q->title."</a>" , $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = $lang['notif-report-a_publisher-approve-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)  && $receiver->can_receive_this('report-others-answers') ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						
					} elseif($classname == 'Space') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						//$notif_link = $url_mapper['questions/view'].$url_type;
						$notif_link = $url_mapper['pages/view'].'terms';
						
						$str = $lang['notif-report-s_publisher-approve-msg'];
						$str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->name."</a>", $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $found->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title = $lang['notif-report-s_publisher-approve-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this('report-my-questions') ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
						
						$str = $lang['notif-report-s_reporter-approve-msg']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->name."</a>" , $str);
						//$str = str_replace('[CONTENT]' , strip_tags($found->content), $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg;
						$title =$lang['notif-report-q_reporter-approve-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)  && $receiver->can_receive_this('report-others-questions')  ) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
						
					}
					
					$found->delete();
					
					if($classname == 'Question') { //Delete answers!
						$follows = FollowRule::get_subscriptions('question',$found->id , 'obj_id' , "" );
						if($follows) {
							foreach($follows as $f) {
								$f->delete();
							}
						}
						$answers = Answer::get_answers_for($found->id);
						foreach($answers as $a) {
							$a->delete();
							$reports = Report::get_everything(" AND obj_type = 'answer' AND obj_id = '{$a->id}' ");
							foreach($reports as $r) {
								$r->delete();
							}
						}
					}
					
					if($classname == 'Space') {
						$follows = FollowRule::get_subscriptions('space',$found->id , 'obj_id' , "" );
						if($follows) {
							foreach($follows as $f) {
								$f->delete();
							}
						}
						$reports = Report::get_everything(" AND obj_type = 'space' AND obj_id = '{$found->id}' ");
						foreach($reports as $r) {
							$r->delete();
						}
						
						$questions = Question::get_everything(" AND space_id = '{$id}' ");
						if($questions) {
							foreach($questions as $found) {
								$follows = FollowRule::get_subscriptions('question',$found->id , 'obj_id' , "" );
								if($follows) {
									foreach($follows as $f) {
										$f->delete();
									}
								}
								$reports = Report::get_everything(" AND obj_type = 'question' AND obj_id = '{$found->id}' ");
								foreach($reports as $r) {
									$r->delete();
								}
								
								$answers = Answer::get_answers_for($found->id);
								foreach($answers as $a) {
									$a->delete();
									$reports = Report::get_everything(" AND obj_type = 'answer' AND obj_id = '{$a->id}' ");
									foreach($reports as $r) {
										$r->delete();
									}
								}
							}
						}	
					}
					
					$report->result = 'approved';
					if($report->update()) {
						$msg = $lang['settings-reports-approved'];
						$response = array('title'=>$lang['alert-type-success'].'!','type' => 'success' , 'msg' => $msg);
					} else {
						$msg = $lang['alert-update_failed'];
						$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					}
					echo json_encode($response); die();
			}
		break;
		
		case 'reject-report' :
			$classname = ucfirst($data);
			$report = Report::get_specific_id($_POST['report_id']);
			$found = $classname::get_specific_id($id);
			if($found) {
					###################
					## REJECT REPORT NOTIF ##
					###################
					if($classname == 'Question') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['questions/view'].$url_type;
						$str = $lang['notif-report-q_reporter-reject-msg']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->title."</a>" , $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-report-q_reporter-reject-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)  && $receiver->can_receive_this('question-report-rejected')  ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Answer') {
						$q = Question::get_specific_id($found->q_id);
						if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
						$notif_link = $url_mapper['questions/view'].$url_type.'#answer-'.$found->id;
						$str = $lang['notif-report-q_reporter-reject-msg']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$q->title."</a>" , $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-report-q_reporter-reject-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $receiver->can_receive_this('answer-report-rejected') ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					} elseif($classname == 'Space') {
						if(URLTYPE == 'slug') {$url_type = $found->slug;} else {$url_type = $found->id;}
						$notif_link = $url_mapper['spaces/view'].$url_type;
						$str = $lang['notif-report-s_reporter-reject-msg']; $str = str_replace('[TITLE]' , "<a href='{$notif_link}'>".$found->name."</a>" , $str);
						$notif_msg = $str;
						$notif_user = $report->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at ". $notif_link;
						$title = $lang['notif-report-s_reporter-reject-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver)  && $receiver->can_receive_this('question-report-rejected')  ) {
							@Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
					
					//$found->delete();
					$report->result = 'rejected';
					if($report->update()) {
						$msg = $lang['settings-reports-rejected'];
						$response = array('title'=>$lang['alert-type-success'].'!','type' => 'success' , 'msg' => $msg);
					} else {
						$msg = $lang['alert-update_failed'];
						$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					}
					echo json_encode($response); die();
			}
		break;
		
		###############################################################
		case 'dislike' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "dislike" , $id, $current_user->id);
				if(!$prev_likes) {
					//Create like..
					$like = New LikeRule();
					$like->user_id = $current_user->id;
					$like->obj_id = $id;
					$like->obj_type = $data;
					$like->type = 'dislike';
					$like->like_date = strftime("%Y-%m-%d %H:%M:%S", time());
					$like->create();
					
					$found->dislikes +=1;
					$found->update();
				}
			}
		break;
		
		case 'undislike' :
			$classname = ucfirst($data);
			$found = $classname::get_specific_id($id);
			if($found) {
				//Check prev like..
				$prev_likes = LikeRule::get_for_obj($data , "dislike" , $id, $current_user->id);
				if($prev_likes) {
					//Create like..
					$prev_likes->delete();
					$found->dislikes -=1;
					$found->update();
				}
			}
		break;
		
		###############################################################
		case 'upl_img' :
			
			if ($_FILES['img']['name']) {
				if (!$_FILES['img']['error']) {
					
					$files = '';
					$img_id = 0;
					$f = 0;
					$target = $_FILES['img'];
					$upload_problems = 0;
					
						$file = "file";
						$string = $$file . "{$f}";
						$$string = new File();	
							if(!empty($_FILES['img']['name'])) {
								$$string->ajax_attach_file($_FILES['img']);
								if ($$string->save()) {
									$img_id = $$string->id;
									$img_cont = File::get_specific_id($img_id);
									echo UPL_FILES."/".$img_cont->image_path(); 
								} else {
									$upl_msg = "Upload Error! ";	
									$upl_msg .= join(" " , $$string->errors);
									echo $upl_msg;
								}
							}
				} else {
				  echo  $message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['img']['error'];
				}
			}
			
		break;
		
		###############################################################
		case 'q_suggestions' :
			
			$result = Question::find( $data , 'title' , " LIMIT 5");
			$result2 = User::find( $data , 'f_name' , " LIMIT 5");
			$result3 = Space::find( $data , 'name' , " LIMIT 5");
			$return = Array();
			if(empty($result) && empty($result2) && empty($result3) ) {
				$q = array(
						'title' => 'No Results Found!',
						'slug' => '',
						'full' => "No Results Found!"
					);
				array_push($return, $q);
			} else {
				if($result) {
					foreach($result as $r) {
						if(URLTYPE == 'slug') {
							$slug = $r->slug;
						} else {
							$slug = $r->id;
						}
						$q = array(
							'title' => $r->title,
							//'slug' => $slug,
							'slug' => $url_mapper['questions/view'] . $slug,
							'full' => "Question: {$r->title}"
						);
						array_push($return, $q);
					}	
				}	
				if($result2) {
					foreach($result2 as $r) {
						$slug = $r->id;
						
						$q2 = array(
							'title' => $r->f_name. ' ' . $r->l_name,
							'slug' => $url_mapper['users/view'] . $slug . '/',
							'full' => "User: {$r->f_name} {$r->l_name}"
						);
						array_push($return, $q2);
					}	
				}
				if($result3) {
					foreach($result3 as $r) {
						if(URLTYPE == 'slug') {
							$slug = $r->slug;
						} else {
							$slug = $r->id;
						}
						
						$q3 = array(
							'title' => $r->name,
							'slug' => $url_mapper['spaces/view'] . $slug,
							'full' => "Space: {$r->name}"
						);
						
						array_push($return, $q3);
					}	
				}
			}
			
			$json = json_encode($return);
			echo $json;
		
		break;
		###############################################################
		case 'tags_suggestions' :
			
			$result = Tag::find($data , "name" , "LIMIT 5");
			$return = Array();
			
			foreach($result as $r ) {
				$q = array(
					'id' => $r->id,
					'name' => $r->name
				);
				array_push($return, $q);
			}
			$json = json_encode($return);
			echo $json;
		
		break;
		###############################################################
		case 'read_msg' :
			
			if(!EMail::check_id_existance($id)) {
				echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #item-selector</center></h4>";
			}
			
			if(!EMail::check_ownership($id, $current_user->id)) {
				echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #item-ownership</center></h4>";
			}
			
			$mymsg = 0;
			
			$mail_msg = EMail::get_specific_id($id);
			$last_reply = Reply::get_last_reply_for($id);
			
			if($last_reply) {
				if ($last_reply->sender == $current_user->id) {
					$mymsg = 1;
				}
			}
			
			if ($data =="received" && $mymsg == 0) { $mail_msg->read_msg(); }
			
			
		break;
		###############################################################
		case 'add_question' : ?>
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content" >
					<form method="post" action="<?php echo $url_mapper['questions/create']; ?>" class="" id="myForm" enctype="multipart/form-data">	
					  <div class="modal-body modal-add_q" style='background-color: #f1f2f2; '>
						<ul class="nav nav-tabs nav-add_q" id="myTab" role="tablist">
						  <li class="nav-item">
							<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?php if(isset($_POST['post_type']) && $_POST['post_type'] == 'post' ) { echo $lang['index-search-add_p']; } else { echo $lang['index-search-button']; } ?></a>
						  </li>
						</ul>
						<div class="tab-content" id="myTabContent" style="background-color:white;">
						  <div class="tab-pane fade show active p-3" id="home" role="tabpanel" aria-labelledby="home-tab">
							
							<?php if($data == 'space_question') {
								$space_id = $db->escape_value($_POST['space_id']);
								if(!Space::check_id_existance($space_id)) {
									echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! Space not found.</center></h4>";
									exit();
								}
								$space = Space::get_specific_id($space_id);
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
								if(URLTYPE == 'slug') {$url_type = $space->slug; $s_link = $url_mapper['spaces/view']. $space->slug;} else {$url_type = $space->id; $s_link = $url_mapper['spaces/view']. $s->id;}
								if($space->open_post == '0') {
							?>
							<div class="alert alert-secondary">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								You're submitting to <b><?php echo $space->name;  ?></b>.<br>
								This space's admins will review your submission before it's posted. You'll be notified when it's approved.
							</div>
								<?php } ?>
							<h6 class="card-subtitle text-muted pull-<?php echo $lang['direction-left']; ?>"><img src="<?php echo $current_user->get_avatar(); ?>" class="rounded-circle" style="width:30px;vertical-align:middle"> <?php echo $current_user->f_name . ' ' . $current_user->l_name; ?> is adding in <?php echo $icon . '<b>'.$space->name."</b> <small>(space/{$url_type})</small>"; ?> 
							</h6>
								
							<?php } else {
								$space_id = '';
							?>
							<div class="alert alert-info">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong>Tips on getting good answers quickly</strong><br>
								Make sure your question has not been asked already<br>
								Keep your question short and to the point<br>
								Double-check grammar and spelling
							</div>
							
								<h6 class="card-subtitle text-muted pull-<?php echo $lang['direction-left']; ?>"><img src="<?php echo $current_user->get_avatar(); ?>" class="rounded-circle" style="width:30px;vertical-align:middle"> <?php echo $current_user->f_name . ' ' . $current_user->l_name; ?> asked 
								&nbsp;<select name="post-type" class="nice-select">
									<option value="public" data-html="<i class='fe fe-users'></i> Public"> <?php echo $lang['questions-public']; ?></option>
									<option value="anonymous" data-html="<i class='fe fe-loader'></i> Anonymous"><i class='fe fe-loader'></i> <?php echo $lang['questions-anonymous']; ?></option>
								</select></h6>
								
								<?php }  ?>
								
								<div class="clearfix text-muted p-2 pb-3"><?php echo $lang['index-add_q-classifications']; ?> 
								
								<select class="form-control select2" name="feed[]" multiple required>
									<?php 
										$classifications = Tag::get_everything(' AND deleted = 0 ');
										if(is_array($classifications)) {
											foreach($classifications as $c) {
												echo "<option value='{$c->name}'";
													if($c->name == 'General') {
														echo ' selected';
													}
												echo ">{$c->name}</option>";
											}
										} else {
											echo "<option value='General' selected>General</option>";
										}
									?>
								</select>
								
								</div>
								
								<div class="clearfix text-muted p-2 pb-3"><?php echo $lang['questions-tags']; ?>
								
								<p><input class="" name="tags" id="add_q-tagsinput" data-role="tagsinput" value="" placeholder="Type & press enter.."></p>
								</div>
								
								
								<?php if(isset($_POST['post_type']) && $_POST['post_type'] == 'post' ) {
									$item_type = 'post';
								?>
								
								<textarea class="form-control modal-textarea" name="title" placeholder="Your post title.." required rows="1"></textarea>
								<br>
								
								<textarea class="form-control" name="content" id="summernote" placeholder="" rows="5" ></textarea>
								
								<?php } else {
									$item_type = 'question';
								?>
								
								<textarea class="form-control modal-textarea" name="title" placeholder="Start your question with 'What', 'How', 'Why' ..etc." required rows="1"></textarea>
								<br>
								
								<textarea class="form-control modal-textarea" name="content" placeholder="Question details (optional)" rows="1"></textarea>
								<?php } ?>
							
							
						  </div>
						</div>
					  </div>
					<div class="modal-footer" style='border:0'>
						<button type="button" class="btn btn-default btn-pill " data-dismiss="modal"><?php echo $lang['btn-close']; ?></button>
						<button type="submit" name="add_q" class="btn btn-primary btn-pill " id="submitBtn"><?php echo $lang['btn-submit']; ?></button>
					  </div>
					<?php 
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$hash."\" readonly/>";
						echo "<input type=\"hidden\" name=\"item_type\" value=\"".$item_type."\" readonly/>";
						echo "<input type=\"hidden\" name=\"space_id\" value=\"".$space_id."\" readonly/>";
					?>
					</form>
				</div>
			</div>
			
			<script src="<?php echo WEB_LINK; ?>public/plugins/niceselect/js/jquery.nice-select.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/select2/select2.min.js"></script> 
			<script src="<?php echo WEB_LINK; ?>public/plugins/tagsinput/bootstrap-tagsinput.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/typeahead/typeahead.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/jquery.form.min.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-bs4.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-emoji/summernote-emoji.js"></script>
			
			<script> 
		$(document).ready(function() {
			
			$('#summernote').summernote({
			minHeight: 150,
			dialogsInBody: true,
			toolbar:[
			  	['custom', ['emojiList']],
				['style', ['bold', 'italic', 'underline', 'clear']],
				['font', ['strikethrough', 'superscript', 'subscript']],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['insert', ['link', 'picture', 'video']]
			],
			callbacks : {
	            onImageUpload: function(image) {
					sendFile(image[0]);
				}
			}
        });
		$('<div id="loading_wrap"><div class="com_loading"><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" /> Loading ...</center></div></div>').appendTo('body');

        function sendFile(image) {
            $("#loading_wrap").fadeIn("fast");

			data = new FormData();
            data.append("data", 'summernote-inline-uploader');
            data.append("id", <?php echo $current_user->id; ?>);
            data.append("hash", '<?php echo $hash; ?>');
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
					$("#loading_wrap").fadeOut("fast");
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
		
			$('.nice-select').niceSelect();
			$('.select2').select2({
				dropdownParent: $('#myModal')
			});
			
			
var accountBloodhound  = new Bloodhound({
  datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
   remote: {
        url: '<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=tags_suggestions#%QUERY',
        wildcard: '%QUERY',
        transport: function (opts, onSuccess, onError) {
            var url = opts.url.split("#")[0];
            var query = opts.url.split("#")[1];
            $.ajax({
                url: url,
                type: 'POST',
				dataType: 'JSON',
				data: 'id=<?php echo $current_user->id; ?>&data=' + query + '&hash="<?php echo $hash; ?>"',
				success: onSuccess,
				error: function(data) {
					console.log(data);
				}
            });
        }
    }
});

	
// Destroy all previous bootstrap tags inputs (optional)
$('input[data-role="tagsinput"]').tagsinput('destroy');
$('input[data-role="tagsinput"]').tagsinput({
maxTags: 8,
maxChars: 30,
trimValue: true,

typeaheadjs: {
	name: 'tags',
	displayKey: 'name',
    valueKey: 'name',
    afterSelect: function(val) { this.$element.val(""); },	
	source: accountBloodhound
}
});
			
			// bind 'myForm' and provide a simple callback function 
            $('#myForm').ajaxForm(function(responseText) {
                $('.modal-receptor').modal('toggle');
				parseThisTo(responseText,'.posts_container');
				$('#loading_wrap').hide();
				$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=questions", {id: '<?php echo $current_user->id; ?>' , data: ' AND id != 0 ' , page:1 , hash:'<?php echo $hash; ?>'}, 
				function(data){
					$('#loading_wrap').hide();
					parseThisTo(data,'.index-questions');
					 $('.page').val(1);
				});
            });
		});
			</script> 
			
		<?php break;
		###############################################################
		case 'add_space' : ?>
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content" >
					<form method="post" action="<?php echo $url_mapper['spaces/']; ?>" class="" id="myForm" enctype="multipart/form-data">	
					  <div class="modal-body modal-add_q" style='background-color: #f1f2f2; '>
						<ul class="nav nav-tabs nav-add_q" id="myTab" role="tablist">
						  <li class="nav-item">
							<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?php echo $lang['index-spaces-add']; ?></a>
						  </li>
						</ul>
						<div class="tab-content" id="myTabContent" style="background-color:white;">
						  <div class="tab-pane fade show active p-3" id="home" role="tabpanel" aria-labelledby="home-tab">
							
							
								<div class="clearfix text-muted p-2 pb-3"><?php echo $lang['index-add_q-classifications']; ?> 
								
								<select class="form-control select2" name="tags[]" multiple required>
									<?php if(isset($settings['spaces_classifications']) && $settings['spaces_classifications'] != '') {
										$classifications = explode(",", $settings['spaces_classifications']);
										if(is_array($classifications)) {
											foreach($classifications as $c) {
												echo "<option value='{$c}'";
													if($c == $classifications[0]) {
														echo ' selected';
													}
												echo ">{$c}</option>";
											}
										}
									} else {
										echo "<option value='General' selected>General</option>";
									} ?>
								</select>
								
								</div>
								
								<textarea class="form-control modal-textarea" name="name" placeholder="Name" required rows="1"></textarea>
								<textarea class="form-control modal-textarea" name="tagline" placeholder="Tagline" rows="1"></textarea>
								<br>
								
								<textarea class="form-control modal-textarea" name="description" placeholder="Description (optional)" rows="1"></textarea>
								
								<br>
								
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-who_can_post']; ?></div>
								<div class="col-10"><select class="select2 form-control" data-placeholder="Space admins.." id="who_can_post" name="open_post" style="" required>
									<option value="1"><?php echo $lang['spaces-who_can_post-all']; ?></option>
									<option value="0"><?php echo $lang['spaces-who_can_post-contributors']; ?></option>
								</select>
								</div>
								</div>
								<br>
								
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-admins']; ?></div>
								<div class="col-10"><select class="user_selector form-control" data-placeholder="Space admins (@username).." id="admins" name="admins[]" style="" multiple></select></div>
								</div>
								<br>
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-moderators']; ?></div>
								<div class="col-10"><select class="user_selector form-control" data-placeholder="Space moderators (@username).." id="moderators" name="moderators[]" style="" multiple></select></div>
								</div>
								<br>
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-contributors']; ?></div>
								<div class="col-10"><select class="user_selector form-control" data-placeholder="Space contributors (@username).." id="contributors" name="contributors[]" style="" multiple></select></div>
								</div>
							
							
						  </div>
						</div>
					  </div>
					<div class="modal-footer" style='border:0'>
						<button type="button" class="btn btn-default btn-pill " data-dismiss="modal"><?php echo $lang['btn-close']; ?></button>
						<button type="submit" name="add_space" class="btn btn-primary btn-pill " id="submitBtn"><?php echo $lang['btn-submit']; ?></button>
					  </div>
					<?php 
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$hash."\" readonly/>";
					?>
					</form>
				</div>
			</div>
			
			<script src="<?php echo WEB_LINK; ?>public/plugins/tagsinput/bootstrap-tagsinput.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/typeahead/typeahead.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/select2/select2.min.js"></script> 
			<script> 
		$(document).ready(function() {
			
var accountBloodhound  = new Bloodhound({
  datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
   remote: {
        url: '<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=tags_suggestions#%QUERY',
        wildcard: '%QUERY',
        transport: function (opts, onSuccess, onError) {
            var url = opts.url.split("#")[0];
            var query = opts.url.split("#")[1];
            $.ajax({
                url: url,
                type: 'POST',
				dataType: 'JSON',
				data: 'id=<?php echo $current_user->id; ?>&data=' + query + '&hash="<?php echo $hash; ?>"',
				success: onSuccess,
				error: function(data) {
					console.log(data);
				}
            });
        }
    }
});


// Destroy all previous bootstrap tags inputs (optional)
$('input[data-role="tagsinput"]').tagsinput('destroy');
$('input[data-role="tagsinput"]').tagsinput({
maxTags: 8,
maxChars: 30,
trimValue: true,

typeaheadjs: {
	name: 'tags',
	displayKey: 'name',
    valueKey: 'name',
    afterSelect: function(val) { this.$element.val(""); },	
	source: accountBloodhound
}
});
			
		});

$(".select2").select2({
	dropdownParent: $('#myModal')
});

$(".user_selector").select2({
ajax: {
		url: '<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=user_selector',
		dataType: 'json',
		type: 'POST',
		
		data: function (params) {
			return {
				data: params.term,
				id: '<?php echo $current_user->id; ?>',
				hash: '<?php echo $hash; ?>'
			};
		},
		processResults: function (data) {
			return {
			  results: data
			};
		  },
		  cache: true
	},
 minimumInputLength: 3,
 dropdownParent: $('#myModal')
});

			</script> 
			
		<?php break;
		###############################################################
		case 'edit_space' : 
		if(!Space::check_id_existance($data)) {
			return false;
			die();
		}
		$space = Space::get_specific_id($data);
		
		?>
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content" >
					<form method="post" action="<?php echo $url_mapper['spaces/']; ?>" class="" id="myForm" enctype="multipart/form-data">	
					  <div class="modal-body modal-add_q" style='background-color: #f1f2f2; '>
						<ul class="nav nav-tabs nav-add_q" id="myTab" role="tablist">
						  <li class="nav-item">
							<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?php echo $lang['spaces-edit']; ?></a>
						  </li>
						</ul>
						<div class="tab-content" id="myTabContent" style="background-color:white;">
						  <div class="tab-pane fade show active p-3" id="home" role="tabpanel" aria-labelledby="home-tab">
							
							
								<div class="clearfix text-muted p-2 pb-3"><?php echo $lang['index-add_q-classifications']; ?> 
								
								<select class="form-control select2" name="tags[]" multiple required>
									<?php 
									$tags = explode(',' , $space->feed);
									if(isset($settings['spaces_classifications']) && $settings['spaces_classifications'] != '' ) {
										$classifications = explode(",", $settings['spaces_classifications']);
										if(is_array($classifications)) {
											foreach($classifications as $c) {
												echo "<option value='{$c}'";
													if(in_array($c, $tags)) {
														echo ' selected';
													}
												echo ">{$c}</option>";
											}
										}
									} else {
										echo "<option value='General' selected>General</option>";
									} ?>
								</select>
								
								</div>
								
								<textarea class="form-control modal-textarea" name="name" placeholder="Name" required rows="1"><?php echo $space->name; ?></textarea>
								<textarea class="form-control modal-textarea" name="tagline" placeholder="Tagline" rows="1"><?php echo $space->tagline; ?></textarea>
								<br>
								
								<textarea class="form-control modal-textarea" name="description" placeholder="Description (optional)" rows="1"><?php echo $space->description; ?></textarea>
								
								<br>
								
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-who_can_post']; ?></div>
								<div class="col-10"><select class="select2 form-control" data-placeholder="Space admins.." id="who_can_post" name="open_post" style="" required>
									<option value="1" <?php if($space->open_post == '1') { echo 'selected'; } ?>><?php echo $lang['spaces-who_can_post-all']; ?></option>
									<option value="0" <?php if($space->open_post == '0') { echo 'selected'; } ?>><?php echo $lang['spaces-who_can_post-contributors']; ?></option>
								</select>
								</div>
								</div>
								<br>
								
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-admins']; ?></div>
								<div class="col-10"><select class="user_selector form-control" data-placeholder="Space admins (@username).." id="admins" name="admins[]" style="" multiple><?php 
								if($space->admins) {
									$users = explode(',' , $space->admins);
									if($users) {
										foreach($users as $user) {
											$user_id = str_replace('-', '', $user);
											@$user = User::get_specific_id($user_id);
											if($user) {
												echo "<option value='-{$user->id}-' selected>{$user->f_name} {$user->l_name} (@{$user->username})</option>";
											}
										}
									}
								}
								?></select></div>
								</div>
								<br>
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-moderators']; ?></div>
								<div class="col-10"><select class="user_selector form-control" data-placeholder="Space moderators (@username).." id="moderators" name="moderators[]" style="" multiple><?php 
								if($space->moderators) {
									$users = explode(',' , $space->moderators);
									if($users) {
										foreach($users as $user) {
											$user_id = str_replace('-', '', $user);
											@$user = User::get_specific_id($user_id);
											if($user) {
												echo "<option value='-{$user->id}-' selected>{$user->f_name} {$user->l_name} (@{$user->username})</option>";
											}
										}
									}
								}
								?></select></div>
								</div>
								<br>
								<div class="row text-muted">
								<div class="col-2"><?php echo $lang['spaces-contributors']; ?></div>
								<div class="col-10"><select class="user_selector form-control" data-placeholder="Space contributors (@username).." id="contributors" name="contributors[]" style="" multiple><?php 
								if($space->contributors) {
									$users = explode(',' , $space->contributors);
									if($users) {
										foreach($users as $user) {
											$user_id = str_replace('-', '', $user);
											@$user = User::get_specific_id($user_id);
											if($user) {
												echo "<option value='-{$user->id}-' selected>{$user->f_name} {$user->l_name} (@{$user->username})</option>";
											}
										}
									}
								}
								?></select></div>
								</div>
							
							
						  </div>
						</div>
					  </div>
					<div class="modal-footer" style='border:0'>
						<button type="button" class="btn btn-default btn-pill " data-dismiss="modal"><?php echo $lang['btn-close']; ?></button>
						<button type="submit" name="edit_space" class="btn btn-primary btn-pill " id="submitBtn"><?php echo $lang['btn-submit']; ?></button>
					  </div>
					<?php 
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$hash."\" readonly/>";
						echo "<input type=\"hidden\" name=\"space_id\" value=\"".$space->id."\" readonly/>";
					?>
					</form>
				</div>
			</div>
			
			<script src="<?php echo WEB_LINK; ?>public/plugins/tagsinput/bootstrap-tagsinput.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/typeahead/typeahead.js"></script>
			<script src="<?php echo WEB_LINK; ?>public/plugins/select2/select2.min.js"></script> 
			<script>
		$(document).ready(function() {
			
var accountBloodhound  = new Bloodhound({
  datumTokenizer: datum => Bloodhound.tokenizers.whitespace(datum.value),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
   remote: {
        url: '<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=tags_suggestions#%QUERY',
        wildcard: '%QUERY',
        transport: function (opts, onSuccess, onError) {
            var url = opts.url.split("#")[0];
            var query = opts.url.split("#")[1];
            $.ajax({
                url: url,
                type: 'POST',
				dataType: 'JSON',
				data: 'id=<?php echo $current_user->id; ?>&data=' + query + '&hash="<?php echo $hash; ?>"',
				success: onSuccess,
				error: function(data) {
					console.log(data);
				}
            });
        }
    }
});


// Destroy all previous bootstrap tags inputs (optional)
$('input[data-role="tagsinput"]').tagsinput('destroy');
$('input[data-role="tagsinput"]').tagsinput({
maxTags: 8,
maxChars: 30,
trimValue: true,

typeaheadjs: {
	name: 'tags',
	displayKey: 'name',
    valueKey: 'name',
    afterSelect: function(val) { this.$element.val(""); },	
	source: accountBloodhound
}
});
			
		});
		
$(".select2").select2({
	dropdownParent: $('#myModal')
});		
$(".user_selector").select2({
ajax: {
		url: '<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=user_selector',
		dataType: 'json',
		type: 'POST',
		
		data: function (params) {
			return {
				data: params.term,
				id: '<?php echo $current_user->id; ?>',
				hash: '<?php echo $hash; ?>'
			};
		},
		processResults: function (data) {
			return {
			  results: data
			};
		  },
		  cache: true
	},
 minimumInputLength: 3,
 dropdownParent: $('#myModal')
});

			</script> 
			
		<?php break;
		###############################################################
		case 'questions' :
			if(isset($_SESSION[$elhash]) && $_SESSION[$elhash] != "") { 
				$random_hash = $_SESSION[$elhash];
			} else {
				$random_hash = uniqid();
				$_SESSION[$elhash] = $random_hash;
			}
			$per_page = '5';
			$page = '1';
			$query = '';
			if($data == 'all') {
				$query = ' AND space_id = 0 ';
			} elseif($data == 'feed') {
				$feed = $db->escape_value($_POST['feed']);
				$query = "AND feed LIKE '%{$feed}%' AND space_id = 0 ";
			} elseif($data == 'tag') {
				$feed = $db->escape_value($_POST['feed']);
				$query = "AND tags LIKE '%{$feed}%' AND space_id = 0 ";
			} elseif($data == 'space') {
				$space = $db->escape_value($_POST['space']);
				$query = "AND space_id = '{$space}' ";
			}
			if(isset($_POST['page'])) {
				$page = $db->escape_value($_POST['page']);
			}
			$count = Question::count_feed_for($id, $query,'');
			$pagination = new Pagination($page, $per_page, $count);
			$questions = Question::get_feed_for($id, $query, " LIMIT {$per_page} OFFSET {$pagination->offset()} ");
			if($questions) {
				$i = 1;
				foreach($questions as $q) {
				
				$user = User::get_specific_id($q->user_id);
				if($q->item_type == 'post') {
					if(URLTYPE == 'id') {
						$div_link = $url_mapper['posts/view']. $q->id;
					} else {
						$div_link = $url_mapper['posts/view']. $q->slug;
					}
				} else {
					if(URLTYPE == 'id') {
						$div_link = $url_mapper['questions/view']. $q->id;
					} else {
						$div_link = $url_mapper['questions/view']. $q->slug;
					}
				}
				?>
					
		<div class="card post-item">
		  <div class="card-body">
		  <?php $reported = Report::check_for_obj('question' , $q->id, $current_user->id); 
		  if(!$reported && $current_user->can_see_this('questions.interact', $group) ) {
		  ?>
			<a href="#report-q-<?php echo $q->id; ?>" data-toggle="modal" data-placement="top" title="<?php echo $lang['questions-report']; ?>" class="report-q pull-<?php echo $lang['direction-right']; ?> my-1 btn btn-icon" data-obj="question" name="<?php echo $q->id; ?>" value="<?php echo $q->likes; ?>"   ><i class="fe fe-flag"></i></a>
		  <?php } else { ?>
		  <a href="#me" data-toggle="tooltip" data-placement="top" title="Content Reported" class="my-1 disabled pull-<?php echo $lang['direction-right']; ?> btn btn-icon" ><i class="fe fe-flag"></i></a>
		  <?php } ?>
			<?php if($q->anonymous) { ?><a href="javascript:void(0);" class='text-dark' style="text-decoration:none;"><img src="<?php echo WEB_LINK.'public/img/anonymous.png'; ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:40px;margin-<?php echo $lang['direction-right']; ?>:10px">
			<h6 class="m-0"><?php echo $lang['user-anonymous']; ?></h6></a>
			<h6 class="text-muted"><small><?php } else { ?><a href="<?php echo $url_mapper['users/view'] . $user->id; ?>" class='text-dark' style="text-decoration:none;"><img src="<?php echo $user->get_avatar(); ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:40px;margin-<?php echo $lang['direction-right']; ?>:10px">
			<h6 class="m-0"><?php echo $user->f_name . ' ' . $user->l_name; ?></h6></a>
			<h6 class="text-muted ltr"><small>@<?php echo $user->username . ' · '; } ?>Published: <?php echo date_ago($q->created_at); ?></small></h6>
			<a href="<?php echo $div_link; ?>" class='text-dark' style="text-decoration:none;"><h4 class="font-weight-bold pt-2"><?php echo $q->title; ?></h4></a>
			<p><?php 
			$answer = Answer::get_best_answer_for($q->id);
			if($answer) {
				$user = User::get_specific_id($answer->user_id);
				echo '<hr> <small class="text-muted">Answered by: '; 
				if($answer->anonymous) { ?>
					<a href="javascript:void(0);" class='text-dark' style="text-decoration:none;"><img src="<?php echo WEB_LINK.'public/img/anonymous.png'; ?>" class="rounded-circle" style="width:20px">
					<?php echo $lang['user-anonymous']; ?></a> · <?php echo date_ago($answer->created_at); ?></small>
				<?php } else { ?>
					<a href="<?php echo $url_mapper['users/view'] . $user->id; ?>" class='text-dark' style="text-decoration:none;"><img src="<?php echo $user->get_avatar(); ?>" class="rounded-circle" style="width:20px;">
					<?php echo $user->f_name . ' ' . $user->l_name; ?></a> · <?php echo date_ago($answer->created_at); ?></small>
				<?php }
				echo "<p class='pt-1'><a href='{$div_link}' class='text-dark' style='text-decoration:none;'>";
				$content = strip_tags($answer->content);
				if (strlen($content) > 500) {
					// truncate string
					$stringCut = substr($content, 0, 500);
					// make sure it ends in a word so assassinate doesn't become ass...
					$content = substr($stringCut, 0, strrpos($stringCut, ' '))."... <a href='{$div_link}' >({$lang['index-question-read_more']})</a>"; 
					echo profanity_filter($content);
				} else {
					$content = $answer->content;
					$content = str_replace('\\','',$content);
					$content = str_replace('<script','',$content);
					$content = str_replace('</script>','',$content);
					$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
					echo profanity_filter($content);
				}
				echo "</p>"; 
			} elseif($q->content) {
				echo "<p class=''><a href='{$div_link}' class='text-dark' style='text-decoration:none;'>";
				$content = str_replace('\\','',$q->content);
				$content = str_replace('<script','',$content);
				$content = str_replace('</script>','',$content);
				$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
				echo profanity_filter($content);
				echo "</p>";
			} else {
				echo $lang['index-question-no_answers'];
			} echo "</a>";
			?></p>
			<hr class="mb-1">
			<div class="btn-group p-0 ">
				<?php 
				$like_class = 'like';
				$dislike_class = 'dislike';

				$like_txt = $lang['btn-like'];
				$liked = LikeRule::check_for_obj('question' , "like" , $q->id, $current_user->id);
				if($liked) {
					$like_class = 'active undo-like';
					$dislike_class = 'dislike disabled';
				}
				
				$disliked = LikeRule::check_for_obj('question' , "dislike" , $q->id, $current_user->id);
				if($disliked) {
					$like_class = 'like disabled';
					$dislike_class = 'active undo-dislike';
				}
				?>
				<?php if($current_user->can_see_this('questions.interact', $group)) { ?><a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $like_class; ?>" data-obj="question" name="<?php echo $q->id; ?>" value="<?php echo $q->likes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-likes']; ?>" ><i class="fe fe-thumbs-up"></i><?php if($q->likes) { echo "&nbsp;&nbsp;{$q->likes}"; }?></a>
				<a href="javascript:void(0);" class="btn btn-icon btn-like-machine <?php echo $dislike_class; ?>" data-obj="question" name="<?php echo $q->id; ?>" value="<?php echo $q->dislikes; ?>"  data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-dislikes']; ?>"><i class="fe fe-thumbs-down"></i><?php if($q->dislikes) { echo "&nbsp;&nbsp;{$q->dislikes}"; }?></a><?php } ?>
				<a href="javascript:void(0);" class="btn btn-icon btn-like-machine" data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-views']; ?>"><i class="fe fe-eye"></i><?php if($q->views) { echo "&nbsp;&nbsp;{$q->views}"; }?></a>
				<a href="javascript:void(0);" class="comment btn btn-icon btn-like-machine" data-toggle="tooltip" data-placement="top" title="<?php echo $lang['btn-answers']; ?>"><i class="fe fe-message-circle"></i><?php if($q->answers) { echo "&nbsp;&nbsp;{$q->answers}"; }?></a>
				</div>
			
			
			<a href="<?php echo $div_link; ?>" class="btn btn-icon btn-like-machine pull-<?php echo $lang['direction-right']; ?>" data-toggle="tooltip" title="<?php echo $lang['btn-view']; ?>" ><i class="fe fe-search"></i> <?php echo $lang['btn-view']; ?></a>
			
		  </div>
		</div>
<?php
	$ads = ads('between_questions');
	if($ads && $i % 2 == 0 ) {
		$r= array_rand($ads);
		$ad = $ads[$r];
		if($ad) {
			echo '<hr>';
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
		<?php if(!$reported) { ?>
		<!-- Modal -->
		<div class="modal fade in" id="report-q-<?php echo $q->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><?php echo $lang['questions-report']; ?></h5>
			  </div>
			  <form action="<?php echo $div_link; ?>&ref=index" method="POST" >
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
	<?php } ?>
		
		
		
				<?php $i++; }
			} else { ?>
				<br><br><h3 style="color:#b0b0b0" id='stop-loading'><center><i class="fe fe-code"></i><br><?php echo 	$lang['index-question-no_questions']; if($current_user->can_see_this("questions.create", $group)) { ?><br><small><a href='#me' class='add-q' style='color: #b92b27'><?php echo $lang['index-question-post']; ?></a></small><?php } ?></center></h3><br>
			<?php } ?>
			<script> 
			// wait for the DOM to be loaded 
			$(document).ready(function() {
				$('[data-toggle="tooltip"]').tooltip();
				$("img").addClass("img-fluid");
			});
			</script> 
		<?php break;
		###############################################################
		case 'user_selector':
			$results = User::find(strip_tags($db->escape_value($_POST['data'])), 'username' , 'LIMIT 5');
			$json = array();
			if($results) {
				foreach($results as $r){
					$entry = array('id' => "-{$r->id}-" , 'text'=> "{$r->f_name} {$r->l_name} (@{$r->username})");
					$json[] = $entry;
				}
			}
			header('Content-Type: application/json');
			echo json_encode($json);
		break;
		###############################################################
		case 'notifications' :
		
		$per_page = "20";
					if (isset($_GET['page']) && is_numeric($_GET['page']) ) {
							$page= $_GET['page'];
					} else {
							$page=1;
					}
					
					$total_count = Notif::count_everything(" AND user_id = '{$current_user->id}' ");
					$pagination = new Pagination($page, $per_page, $total_count);
					$notif = Notif::get_everything(" AND user_id = '{$current_user->id}' ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$pagination->offset()} ");
					
					if($notif) {
						foreach($notif as $n) {
							$string = str_replace('\\','',$n->msg);
							$link = $n->link;
							if(strpos($link , '#')) {	//There's a hash!
								$linkarr = explode('#' , $link);
								$link = $linkarr[0] . "&notif={$n->id}#" . $linkarr[1];
							} else {
										$link .= "&notif={$n->id}";
									}
							if($string != '') {
								echo "<p class='badge badge-danger'>" . date_ago($n->created_at) . "</p>";
								echo "<h6 onclick=\"location.href='{$link}';\" style='";
								if($n->viewed == '0') {
									echo ' background-color: #edf2fa; ';
								}
								echo " line-height:25px;border-bottom:1px solid #dedede; cursor:pointer '><i class='fa fa-globe'></i>&nbsp;&nbsp;{$string}</h6>";
							}
						}
					} else {
				?>
				<h3 style="color:#b0b0b0"><center><i class="glyphicon glyphicon-bullhorn"></i><br><?php echo $lang['index-notification-no_results']; ?></center></h3><br><br>
				<?php } ?>
		
		<?php
		break;
		###############################################################
		case 'tags':
		
		$per_page = 10;
		if (isset($_POST['page']) && is_numeric($_POST['page']) ) {
				$page= $_POST['page'];
		} else {
				$page=1;
		}
		$tag_count = Tag::count_trending('');
		$pagination = new Pagination($page, $per_page, $tag_count);
		$tags = Tag::get_trending(" LIMIT {$per_page} OFFSET {$pagination->offset()} ");
		
		if($tags) {
			foreach($tags as $tag) {
					
					$current = '';
					if(isset($_GET['feed']) && $_GET['feed'] != '' ) {
						if($_GET['feed'] == $tag->name ) {
							$current = 'current';
						}
					}
					
				if($tag->avatar) {
					$img = File::get_specific_id($tag->avatar);
					$quser_avatar = WEB_LINK."public/".$img->image_path();
					$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
					if (file_exists($quser_avatar_path)) {
						$icon= "<img src='{$quser_avatar}'>";
					} else {
						$icon= "<div class='icon'><i class='fe fe-rss'></i></div>";
					}					
				} else {
					$icon= "<div class='icon'><i class='fe fe-rss'></i></div>";
				}
				
				
			?>
				<li><a href="<?php echo $url_mapper['feed/'] . $tag->name; ?>" class="btn-block <?php echo $current; ?>"><?php echo $icon; ?><?php echo $tag->name; ?></a></li>
				
			<?php
				}
		}
		
		break;	###############################################################	
		default : 
			echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #switch</center></h4>";
			die();
		break;
	}
	
} else {
	
	echo "<h4 style=\"color:red; font-family:Century Gothic\" ><center>Error! This page can't be accessed directly! please try again using main program #intro</center></h4>";
	die();
}
?>