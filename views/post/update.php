<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');


if(isset($_POST['update_q'])) {

	if($_POST['hash'] == $_SESSION[$elhash]){
		
		if(!$current_user->can_see_this("questions.update",$group) ) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['questions/create']."&edit=fail&msg={$msg}");
		}
		
		$q_id = $_POST['q_id'];
		
		if(!Question::check_id_existance($q_id)) {
			redirect_to($url_mapper['index/']);
		}
		
		$q = Question::get_specific_id($q_id);
		
		if($current_user->prvlg_group != '1' && $q->user_id != $current_user->id ) {
			$msg = $lang['alert-restricted'];
			if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
			redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
		}
		
			$title = profanity_filter($_POST['title']);
			$title = strip_tags($title);
			
			$slug = slugify($title);
			$slug_checker = Question::check_slug($slug);
			if($slug_checker) {
				$slug .= "-". (count($slug_checker) +1);
			}
			
			/*$tags = explode(',',$_POST['tags']);
			$newtags = array();
			foreach($tags as $k => $v) {
				$v = strip_tags(profanity_filter($v));
				$v = str_replace('?' , '' , $v);
				$tag = Tag::get_tag($v);
				if($tag) {
					$tag->used += 1;
					$tag->update();
				} else {
					if($v !='') {
						$t = new Tag();
						$t->name = profanity_filter($v);
						$t->used = 1;
						$t->create();
						$t_id= $t->id;
					}
				}
				$newtags[] = $v;
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
			
			$content = profanity_filter($_POST['content']);
			$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
			
			$published = false;
			
			$q->title = $title;
			$q->slug = $slug;
			$q->updated_at = strftime("%Y-%m-%d %H:%M:%S" , time());
			$q->feed = $feed;
			$q->tags = $tags;
			$q->content = $content;
			
			if(isset($_POST['anonymous']) && $_POST['anonymous'] == '1' ) {
				$q->anonymous = "1";
			}
			
			if($settings['q_approval'] == '0' || $settings['q_approval'] == '1' && $current_user->prvlg_group == '1' || $settings['q_approval'] == '1' && $current_user->can_see_this("questions.power",$group) ) {
				$q->published = 1;
				$published = true;
			} else {
				$q->published = 0;
			}
			
			if($q->update()) {
				$msg = $lang['alert-update_success'];
				if($published == false) {
					$msg .= $lang['questions-pending'];
				}
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/view']."{$url_type}/?edit=success&msg={$msg}");
			} else {
				$msg = $lang['alert-update_failed'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['questions/update']."{$url_type}/?edit=fail&msg={$msg}");
			}
			
	} else {
		redirect_to($url_mapper['questions/create']);
	}
}



require_once(VIEW_PATH.'pages/navbar.php');
?>
<div class="container">		

<div class="row">
	
	<div class="card post-item col-md-9">
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


$data = $q_slug;
if(URLTYPE == 'id') {
	$q = Question::get_specific_id($data);
} else {
	$q = Question::get_slug($data);
}


if($q) {
	if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
	
	if($current_user->prvlg_group != '1' && $q->user_id != $current_user->id ) {
		$msg = $lang['alert-restricted'];
		redirect_to($url_mapper['questions/view'].$url_type."&edit=fail&msg={$msg}");
	}
	
	$title = strip_tags($q->title);
	$title_slug = $q->slug;
	$content = str_replace('\\','',$q->content);
	$content = str_replace('<script','',$content);
	$content = str_replace('</script>','',$content);
	$content = strip_tags($content,'<a><img><b><i><p><u><ol><ul><li><iframe>');
	
}
	
		
		
?>
		
		<h4 class="page-header"><?php echo $lang['questions-update']; ?></h4>
		<form method="post" action="<?php  echo $url_mapper['questions/update']. $url_type.'&hash='.$random_hash; ?>" enctype="multipart/form-data">
			
			
			
							<hr>
							
								<h6 class="card-subtitle text-muted pull-<?php echo $lang['direction-left']; ?>"><img src="<?php echo $current_user->get_avatar(); ?>" class="rounded-circle" style="width:30px;vertical-align:middle"> <?php echo $current_user->f_name . ' ' . $current_user->l_name; ?> asked 
								&nbsp;<select name="post-type" class="nice-select">
									<option value="public" data-html="<i class='fe fe-users'></i> Public" <?php if($q->anonymous == '0') { echo 'selected'; } ?>> <?php echo $lang['questions-public']; ?></option>
									<option value="anonymous" data-html="<i class='fe fe-loader'></i> Anonymous" <?php if($q->anonymous == '1') { echo 'selected'; } ?>><i class='fe fe-loader'></i> <?php echo $lang['questions-anonymous']; ?></option>
								</select></h6>
								
								
								<div class="clearfix text-muted p-2 pb-3"><?php echo $lang['index-add_q-classifications']; ?> 
								
								<select class="form-control select2" name="feed[]" multiple required>
									<?php 
										$classifications_arr = explode(',', $q->feed);
										$classifications = Tag::get_everything(' AND deleted = 0 ');
										if(is_array($classifications)) {
											foreach($classifications as $c) {
												echo "<option value='{$c->name}'";
													if(in_array($c->name, $classifications_arr)) {
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
								
								<p><input class="" name="tags" id="add_q-tagsinput" data-role="tagsinput" value="<?php echo $q->tags; ?>" placeholder="Type & press enter.."></p>
								</div>
								
								<?php if($q->item_type == 'post' ) {
									$item_type = 'post';
								?>
								
								<textarea class="form-control modal-textarea" name="title" placeholder="Your post title.." required rows="1"><?php echo $q->title; ?></textarea>
								<br>
								
								<textarea class="form-control" name="content" id="summernote" placeholder="" rows="5" ><?php echo $q->content; ?></textarea>
								
								<?php } else {
									$item_type = 'question';
								?>
								
								<textarea class="form-control modal-textarea" name="title" placeholder="Start your question with 'What', 'How', 'Why' ..etc." required rows="1"><?php echo $q->title; ?></textarea>
								<br>
								
								<textarea class="form-control modal-textarea" name="content" placeholder="Question details (optional)" rows="1"><?php echo $q->content; ?></textarea>
								<?php } ?>
			
			<div class="modal-footer">
				<br/>
				<center>
				
					
						<input class="btn btn-success" type="submit" name="update_q" value="<?php echo $lang['btn-submit']; ?>">
					
					
					<a href="<?php echo $url_mapper['index/']; ?>" class="btn btn-secondary"><?php echo $lang['btn-cancel']; ?></a>
					
				</center>
				<?php 
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
						echo "<input type=\"hidden\" name=\"item_type\" value=\"".$item_type."\" readonly/>";
						echo "<input type=\"hidden\" name=\"q_id\" value=\"{$q->id}\" readonly/>";
						echo "<input type=\"hidden\" name=\"space_id\" value=\"{$q->space_id}\" readonly/>";
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