<?php
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

if(isset($_GET['feed']) && $_GET['feed'] != '' ) {
	$title = $db->escape_value($_GET['feed']);
	$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
}

if(isset($_GET['hash']) && $_GET['type'] == 'read_all' ) {
	if($_SESSION[$elhash] == $_GET['hash']) {
		$read_all = Notif::read_everything($current_user->id);
	}
}

require_once(VIEW_PATH.'pages/header.php'); ?>
<?php require_once(VIEW_PATH.'pages/navbar.php'); ?>

<div class="container">		

<div class="row">
	<?php require_once(VIEW_PATH.'pages/lt_sidebar.php'); ?>
	<div class="posts_container col-lg-7" style="overflow:hidden">
		
		<?php
	if($current_user->can_see_this('index.post',$group) && !isset($_GET['feed'])) {
		?>
		<a href="javascript:void(0);" class='add-q text-dark' style="text-decoration:none;"><div class="card post-item">
		  <div class="card-body">
			<h6 class="card-subtitle text-muted"><img src="<?php echo $current_user->get_avatar(); ?>" class="rounded-circle" style="float:<?php echo $lang['direction-left']; ?>;width:40px;margin-<?php echo $lang['direction-right']; ?>:10px"> <?php echo $current_user->f_name . ' ' . $current_user->l_name; ?></h6>
			<h5 class="m-0"><?php echo $lang['index-search-title']; ?>?</h5>
		  </div>
		</div></a><hr>
		<?php
		}
		$scope = 'all';
		$feed = '';
		
		if(isset($_GET['feed']) && $_GET['feed'] != '' ) {
			$scope = 'feed';
			$feed = $db->escape_value($_GET['feed']);
			$tag = Tag::get_tag($feed);
			if($tag) { 
				$f_follow_class = 'follow';
				$follow_txt = $lang['btn-follow'];
				$followed = FollowRule::check_for_obj('tag' , $tag->id, $current_user->id);
				if($followed) {
					$follow_txt = $lang['btn-followed'];
					$f_follow_class = 'active unfollow';
				}
			?>
				<div class="card post-item">
				  <div class="card-body question-like-machine">
					<h5 class="m-0"><img src="<?php echo $tag->get_avatar(); ?>" class="" style="float:<?php echo $lang['direction-left']; ?>;width:75px;margin-<?php echo $lang['direction-right']; ?>:10px;margin-top:-5px"> <?php echo $tag->name; ?> <a href="<?php echo WEB_LINK."rss/feed/{$feed}"; ?>" target="_blank" class="btn btn-sm btn-secondary m-1 text-dark text-decoration-none" data-toggle="tooltip" title="RSS Feed"><i class='fe fe-rss'></i> <?php echo $lang['btn-rss']; ?></a><?php if($current_user->can_see_this('questions.interact', $group)) { ?><a href="javascript:void(0);" target="" class="btn btn-sm btn-secondary m-1 text-dark text-decoration-none <?php echo $f_follow_class; ?>" data-toggle="tooltip" title="<?php echo $follow_txt; ?>" data-obj="tag" name="<?php echo $tag->id; ?>" data-lbl="<?php echo $lang['btn-follow'] ?>" data-lbl-active="<?php echo $lang['btn-followed']; ?>" value="<?php echo $tag->follows; ?>" ><i class='fe fe-user-plus'></i> <?php echo $follow_txt; if($tag->follows) { echo " · {$tag->follows}"; } ?></a><?php } ?>
					<p class="text-muted"><?php echo $tag->used; ?> Topics</p></h5>
				  </div>
				</div><hr>
			<?php }
		}
		
		?>
		
		
		<div class="card post-item">
			<div class="card-body">
				<h4><?php echo $lang['pages-notifications-title']; ?>
		<small><a href="<?php echo $url_mapper['notifications/']."&hash={$random_hash}&type=read_all"; ?>" class="btn btn-secondary pull-<?php echo $lang['direction-right']; ?>"><?php echo $lang['pages-notifications-read_all']; ?></a></small></h4><hr>
		
				<div class="index-notifications question-like-machine"></div>
			</div>
		</div>
		<input type='text' class='page d-none' value='0' readonly>
	</div>
	
	<?php require_once(VIEW_PATH.'pages/rt_sidebar.php'); ?>
	
</div>
	</div> <!-- /container -->
	
    <?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
	<?php require_once(VIEW_PATH.'pages/like-machine.php'); ?>
	<?php require_once(VIEW_PATH.'pages/footer.php'); ?>
	
<script src="<?php echo WEB_LINK; ?>public/plugins/tagsinput/bootstrap-tagsinput.js"></script>
<script>

$('<div id="loading_wrap"><br><center><img src="<?php echo WEB_LINK; ?>public/img/loading.gif" style="height:6px"/></center></div>').appendTo('.index-notifications');


function get_posts() {
	$('#loading_wrap').show();
	var page= parseInt($('.page').val()) + 1;
	$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=notifications", {id: '<?php echo $current_user->id; ?>' , data: '<?php echo $scope; ?>' , feed: '<?php echo $feed; ?>' , page: page , hash:'<?php echo $random_hash; ?>'}, 
	function(data){
		$('#loading_wrap').hide();
		parseThisTo(data,'.index-notifications','append');
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
</script>
<?php require_once(VIEW_PATH.'pages/bottom.php'); ?>