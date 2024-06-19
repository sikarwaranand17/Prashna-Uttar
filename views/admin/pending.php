<?php 
defined('LIBRARY_PATH') ? null : die('Direct access to this file is not allowed!');

require_once(VIEW_PATH.'pages/header.php');

$general_settings = MiscFunction::get_function("general_settings");



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
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pending']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-pending'] . ' ' . $pending_posts_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/reports']; ?>" class="col-md-12"><?php echo $lang['admin-section-reports'] . ' ' . $pending_reports_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('adminusers.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/users']; ?>" class="col-md-12"><?php echo $lang['admin-section-users']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('groups.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/groups']; ?>" class="col-md-12"><?php echo $lang['admin-section-groups']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pages.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pages']; ?>" class="col-md-12"><?php echo $lang['admin-section-pages']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admintopics.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/topics']; ?>" class="col-md-12"><?php echo $lang['admin-section-topics']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('admanager.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/ads']; ?>" class="col-md-12"><?php echo $lang['admin-section-admanager']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('profanity_filter.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/filter']; ?>" class="col-md-12"><?php echo $lang['admin-section-filter']; ?></a></li><?php } ?>
				
			</ul>
			
		</div></div>
	
	<hr>
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
	</div>
	
	
	<div class="col-md-9">
		<div class="card post-item"><div class="card-body">
			<form method="post" action="<?php echo $url_mapper['admin/pending']; ?>">
			
				<h5 class="page-header"><?php echo $lang['admin-pending-title']; ?></h5>
				<hr>
					<div class="row">
						<div class="col-md-6" style='border-<?php echo $lang['direction-right']; ?>: 1px solid #b0b0b0'>
							<h5><center><?php echo $lang['admin-pending-questions']; ?></center></h5><br>
							<?php $pending_q = Question::get_pending(); ?>
							<?php if($pending_q) { ?>
								<table class="custom_table table table-striped table-bordered" cellspacing="0" width="100%">
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
								</tbody></table>
							<?php } else { ?>
							<h5 style="color:#b0b0b0" id='stop-loading'><center><i class="fe fe-code"></i><br><?php echo $lang['index-question-no_questions']; ?></center></h5><br>
							<?php } ?>
						</div>
						<div class="col-md-6">
							<h5><center><?php echo $lang['admin-pending-answers']; ?></center></h5><br>
							<?php $pending_q = Answer::get_pending(); ?>
							<?php if($pending_q) { ?>
								<table class="custom_table table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<th><?php echo $lang['admin-pending-answers-comment']; ?></th>
									<th><?php echo $lang['admin-pending-answers-user']; ?></th>
									<th><i class="fe fe-settings"></i></th>
								</thead>
								<tbody><?php foreach($pending_q as $pa) { $pa_user = User::get_specific_id($pa->user_id);
								$pq = Question::get_specific_id($pa->q_id);
								if(URLTYPE == 'slug') {
									$url_type = $pq->slug;
								} else {
									$url_type = $pq->id;
								}
								
								
							$string = strip_tags($pa->content);
							if (strlen($string) > 100) {
								$stringCut = substr($string, 0, 100);
								$string= substr($stringCut, 0, strrpos($stringCut, ' '))."..."; 
							}	
							
							if($string == '') { $string = 'Undefined'; }
								?>
									<tr>
									<td><a href="<?php echo $url_mapper['questions/view']; echo $url_type; echo "#answer-" . $pa->id; ?>" target="_blank"><?php echo $string; ?></a></td>
									<td><a href="<?php echo $url_mapper['users/view']; echo $pa_user->id; ?>/" target="_blank"><?php echo $pa_user->f_name . " " . $pa_user->l_name . "</a><br><small style='font-size:10px; color:grey'>" . date_ago($pa->created_at) . "</small>"; ?></td>
									<td>
										<p class="btn-group approve-machine"><a href="javascript:void(0);" class="btn btn-success btn-sm approve-item" data-obj="answer" data-id="<?php echo $pa->id; ?>" data-action="approve"><i class="fa fa-check"></i></a>
										<a href="javascript:void(0);" class="btn btn-danger  btn-sm reject-item" data-obj="answer" data-id="<?php echo $pa->id; ?>" data-action="reject"><i class="fa fa-times"></i></a></p>
									</td></tr>
								<?php } ?>
								</tbody></table>
							<?php } else { ?>
							<h5 style="color:#b0b0b0" id='stop-loading'><center><i class="fe fe-code"></i><br><?php echo $lang['index-no_posts']; ?></center></h5><br>
							<?php } ?>
						</div>
					</div>
					
					<?php 
						$_SESSION[$elhash] = $random_hash;
						echo "<input type=\"hidden\" name=\"hash\" value=\"".$random_hash."\" readonly/>";
					?>
					</form>
		</div></div>
	</div>
</div>
</div>
<?php require_once(VIEW_PATH.'pages/preloader.php'); ?>

<script type="text/javascript">
	$(document).ready(function() {
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
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>