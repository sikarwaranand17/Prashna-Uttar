<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');
if(isset($_POST['add_q'])) {
	if($_POST['hash'] == $_SESSION[$elhash]){
		unset($_SESSION[$elhash]);
		
		if(!$current_user->can_see_this("questions.create",$group) ) {
			$msg = $lang['alert-restricted'];
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
				echo json_encode($response); die();
			} else {
				redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
			}
		}
		
		/*if(isset($_POST['g-recaptcha-response'])) {
          $captcha=$_POST['g-recaptcha-response'];

        if(!$captcha) {
			$msg = "Captcha Error! please try again";
			redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
          exit;
        }
        $response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$captcha_info['secret']}&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        if($response['success'] == false) {
			$msg = "Captcha Error! please try again";
			redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
        } else {*/
			$title = profanity_filter($_POST['title']);
			$title = strip_tags($title);
			
			$slug = slugify($title);
			$slug_checker = Question::check_slug($slug);
			if($slug_checker) {
				$slug .= "-". (count($slug_checker) +1);
			}
			
			$content = profanity_filter($_POST['content']);
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
			
			$published = false;
			
			if( !$title ) {
				$msg = $lang['alert-create_failed'];
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
				}
			}
			
			/*$tags = explode(',',$_POST['tags']);
			$tagsid = array();
			foreach($tags as $k => $v) {
				//$v = slugify(profanity_filter($v));
				$v = strip_tags(profanity_filter($v));
				$v = str_replace('?' , '' , $v);
				$actualtag = Tag::find_exact($v , 'name' , 'LIMIT 1');
				if($actualtag) {
					$actualtag = $actualtag[0];
					$actualtag->used += 1;
					$actualtag->update();
					$tagsid[] = $actualtag->id;
					//unset($tags[$k]);
				} else {
					if($v !='') {
						$t = new Tag();
						$t->name = $v;
						$t->used = 1;
						$t->create();
						$t_id= $t->id;
						$tagsid[] = $t_id;
						//$tags[] = $v;
					}
				}
			}*/
			
			$tags = $db->escape_value($_POST['tags']);
			$feed_arr = $_POST['feed'];
			foreach($feed_arr as $f) {
				$f = $db->escape_value($f);
				$v = strip_tags(profanity_filter($f));
				$v = str_replace('?' , '' , $f);
				$tag = Tag::get_tag($f);
				if($tag) {
					$tag->used += 1;
					$tag->update();
				}
			}
			$feed = implode(',', $feed_arr);
			
			
			$q = New Question();
			$q->user_id = $current_user->id;
			$q->title = $title;
			$q->slug = $slug;
			$q->created_at = strftime("%Y-%m-%d %H:%M:%S" , time());
			$q->feed = $feed;
			$q->tags = $tags;
			$q->content = $content;
			
			if(isset($_POST['post-type']) && $_POST['post-type'] == 'anonymous') {
				$q->anonymous = "1";
			}
			
			if(isset($_POST['item_type']) && $_POST['item_type'] == 'post' ) {
				$q->item_type = "post";
			} else {
				$q->item_type = "question";
			}
			
			if(isset($_POST['space_id']) && is_numeric($_POST['space_id']) ) {
				$q->space_id = $db->escape_value($_POST['space_id']);
			}
			
			if($q->item_type == 'question' && !$q->space_id && $settings['q_approval'] == '0' || $settings['q_approval'] == '1' && $current_user->prvlg_group == '1' || $settings['q_approval'] == '1' && $current_user->can_see_this("questions.power",$group) ) {
				$q->published = 1;
				$published = true;
			}
			
			//Spaces privileges
			if($q->space_id) {
				if(!Space::check_id_existance($q->space_id)) {
					$msg = $lang['alert-create_failed'];
					if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
						$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
						echo json_encode($response); die();
					} else {
						redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
					}
					exit();
				}
				$space = Space::get_specific_id($q->space_id);
				if($space->admins) { $admins = explode(',' , $space->admins); } else { $admins = array(); }
				if($space->moderators) { $moderators = explode(',' , $space->moderators); } else { $moderators = array(); }
				
				if($space->open_post == '1' || in_array("-".$current_user->id."-", $admins) || in_array("-".$current_user->id."-", $moderators) ) {
					$q->published = 1;
					$published = true;
				}
			}
			
			
			if($q->create()) {
				###############
				## FOLLOW NOTIF ##
				###############
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				$notif_link = $url_mapper['questions/view'].$url_type;
				
				//User followers
				$notif_msg = sprintf($lang['notif-question-create-msg'] , $current_user->f_name, $title);
				$user_followers = FollowRule::get_subscriptions('user',$current_user->id , 'obj_id' , "" );
				if($user_followers) {
					foreach($user_followers as $uf) {
						$notif_user = $uf->user_id;
						$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
						##########
						## MAILER ##
						##########
						$msg = $notif_msg . "<br>Check it out at " . $notif_link;
						$title = $lang['notif-question-create-title'];
						$receiver = User::get_specific_id($notif_user);
						if($receiver && is_object($receiver) && $notif_user != $current_user->id  && $receiver->can_receive_this('new-user-question') ) {
							Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
						}
					}
				}
				
				//Feed followers
				if(!empty($tagsid)) {
					foreach($tagsid as $k => $v) {
						$tag = Tag::get_specific_id($v);
						if($tag) {
							$notif_msg = sprintf($lang['notif-question-tag-create-msg'] , $tag->name);
							$tag_followers = FollowRule::get_subscriptions('tag', $tag->id , 'obj_id' , "" );
							if($tag_followers) {
								foreach($tag_followers as $tf) {
									$notif_user = $tf->user_id;
									$notif = Notif::send_notification($notif_user,$notif_msg,$notif_link);
									##########
									## MAILER ##
									##########
									$msg = $notif_msg . "<br>Check it out at " . $notif_link;
									$title = $lang['notif-question-tag-create-title'];
									$receiver = User::get_specific_id($notif_user);
									if($receiver && is_object($receiver) && $notif_user != $current_user->id  && $receiver->can_receive_this('new-feed-question')  ) {
										Mailer::send_mail_to($receiver->email , $receiver->f_name , $msg , $title);
									}
								}
							}
						}
					}
				}
				
				
				###############
				
				$msg = $lang['alert-create_success'];
				if($published == false) {
					$msg .= $lang['questions-pending'];
				}
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-success'].'!','type' => 'success' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($url_mapper['questions/view']."{$url_type}&edit=success&msg={$msg}");
				}
			} else {
				$msg = $lang['alert-create_failed'];
				if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					$response = array('title'=>$lang['alert-type-error'].'!','type' => 'error' , 'msg' => $msg);
					echo json_encode($response); die();
				} else {
					redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
				}
			}
			
        /*}

		} else {
			redirect_to($url_mapper['questions/create']);
		}*/
	} else {
		redirect_to($url_mapper['questions/create']);
	}
}

require_once(VIEW_PATH.'pages/header.php');
require_once(VIEW_PATH.'pages/navbar.php');
?>
<div class="container">		

<div class="row">
	<div class="col-md-9 card">
	<div class="card-body">
		
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
		
		
		<?php 
			$edit_mode = false;
			$title = ''; $title_slug = ''; $content = ''; $anonymous = '';
		?>
		
		<h4 class="page-header"><?php echo $lang['questions-title']; ?></h4>
		
		<form method="post" action="<?php if($edit_mode == true) { echo $url_mapper['questions/update']; } else { echo $url_mapper['questions/create']; } ?>" enctype="multipart/form-data">
			
			
			
						<hr>
								<h6 class="card-subtitle text-muted pull-<?php echo $lang['direction-left']; ?>"><img src="<?php echo $current_user->get_avatar(); ?>" class="rounded-circle" style="width:30px;vertical-align:middle"> <?php echo $current_user->f_name . ' ' . $current_user->l_name; ?> asked 
								&nbsp;<select name="post-type" class="nice-select">
									<option value="public" data-html="<i class='fe fe-users'></i> Public"> <?php echo $lang['questions-public']; ?></option>
									<option value="anonymous" data-html="<i class='fe fe-loader'></i> Anonymous"><i class='fe fe-loader'></i> <?php echo $lang['questions-anonymous']; ?></option>
								</select></h6>
								
								
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
								<?php
								$item_type = 'question';
								?>
								
								<textarea class="form-control modal-textarea" name="title" placeholder="Start your question with 'What', 'How', 'Why' ..etc." required rows="1"></textarea>
								<br>
								
								<textarea class="form-control modal-textarea" name="content" placeholder="Question details (optional)" rows="1"></textarea>
								
			
			
			<div class="modal-footer">
				<br/>
				<center>
				
					
						<input class="btn btn-success" type="submit" name="add_q" value="<?php echo $lang['btn-submit']; ?>">
					
					
					<a href="<?php echo $url_mapper['index/']; ?>" class="btn btn-secondary"><?php echo $lang['btn-cancel']; ?></a>
					
				</center>
				<?php 
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
						echo "<input type=\"hidden\" name=\"item_type\" value=\"".$item_type."\" readonly/>";
						echo "<input type=\"hidden\" name=\"space_id\" value=\"0\" readonly/>";
				?>
			</div>
		</form>
	</div>
	</div>
	
	<?php require_once(VIEW_PATH.'pages/rt_sidebar.php') ?>
	
</div>
	</div> <!-- /container -->
    <?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
	<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-bs4.js"></script>
	<script src="<?php echo WEB_LINK; ?>public/plugins/summernote/summernote-emoji/summernote-emoji.js"></script>
	<script src="<?php echo WEB_LINK; ?>public/plugins/select2/select2.min.js"></script> 
	<script src="<?php echo WEB_LINK; ?>public/plugins/niceselect/js/jquery.nice-select.js"></script>
	<script src="<?php echo WEB_LINK; ?>public/plugins/tagsinput/bootstrap-tagsinput.js"></script>
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
					$("#loading_wrap").fadeOut("fast");
				},
				error: function(data) {
					console.log(data);
				}
            });
        }
		
		
			$('.select2').select2();
			
				
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
				data: 'id=<?php echo $current_user->id; ?>&data=' + query + '&hash="<?php echo $random_hash; ?>"',
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

$('.bootstrap-tagsinput input').blur(function() {
$('input#tagsinput').tagsinput('add', $(this).val());
$(this).val('');
});
  </script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>
<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>