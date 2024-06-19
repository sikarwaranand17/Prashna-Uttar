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
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pending']; ?>" class="col-md-12 "><?php echo $lang['admin-section-pending'] . ' ' . $pending_posts_badge; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/reports']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-reports'] . ' ' . $pending_reports_badge; ?></a></li><?php } ?>
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
			<form method="post" action="<?php echo $url_mapper['admin/reports']; ?>">
			
				<h5 class="page-header"><?php echo $lang['admin-reports-title']; ?></h5>
				<hr>
					<div class="row">
						<div class="col-md-12">
							<?php $reports = Report::get_everything(' AND result= "" '); ?>
							<?php if($reports) { ?>
								<table class="custom_table table table-striped table-bordered" cellspacing="0" width="100%">
								<thead>
									<th>#</th>
									<th><?php echo $lang['admin-reports-post']; ?></th>
									<th><?php echo $lang['admin-reports-user']; ?></th>
									<th><?php echo $lang['admin-reports-info']; ?></th>
									<th><i class="fe fe-settings"></i></th>
								</thead>
								<tbody><?php	$i = 1; foreach($reports as $r) { $pq_user = User::get_specific_id($r->user_id); 
									if($r->obj_type == 'answer') {
										$a = $pq = Answer::get_specific_id($r->obj_id);
										$pq = Question::get_specific_id($a->q_id);
										if(URLTYPE == 'slug') {
											$url_type = $pq->slug;
										} else {
											$url_type = $pq->id;
										}
										$title = $lang['admin-reports-type-a'] . ': ' . $pq->title;
										$link = $url_mapper['questions/view'] . $url_type . '#answer-' . $r->id;
									} elseif($r->obj_type == 'space' ) {
										$pq = Space::get_specific_id($r->obj_id);
										if(URLTYPE == 'slug') {
											$url_type = $pq->slug;
										} else {
											$url_type = $pq->id;
										}
										$title = $lang['admin-reports-type-space'] . ': ' . $pq->name;
										$link = $url_mapper['spaces/view'] . $url_type;
									} else {
										$pq = Question::get_specific_id($r->obj_id);
										
										if(URLTYPE == 'slug') {
											$url_type = $pq->slug;
										} else {
											$url_type = $pq->id;
										}
										$title = $lang['admin-reports-type-q'] . ': ' . $pq->title;
										$link = $url_mapper['questions/view'] . $url_type;
									}
								
								?>
									<tr>
									<td><?php echo $i; ?></td>
									<td><a href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a></td>
									<td><a href="<?php echo $url_mapper['users/view']; echo $pq_user->id . '/'; ?>" target="_blank"><?php echo $pq_user->f_name . " " . $pq_user->l_name . "</a><br><small style='font-size:10px; color:grey'>" . date_ago($pq->created_at) . "</small>"; ?></td>
									<td><?php echo $r->info; ?></td>
									<td>
										<p class="btn-group approve-machine">
										<a href="javascript:void(0);" class="btn btn-success btn-sm approve-report-item" data-obj="<?php echo $r->obj_type; ?>" data-id="<?php echo $pq->id; ?>" data-action="approve_report" data-report_id="<?php echo $r->id; ?>"><?php echo $lang['admin-reports-approve_report']; ?></a>
										<a href="javascript:void(0);" class="btn btn-danger btn-sm reject-report-item" data-obj="<?php echo $r->obj_type; ?>" data-id="<?php echo $pq->id; ?>" data-action="reject_report" data-report_id="<?php echo $r->id; ?>"><?php echo $lang['admin-reports-reject_report']; ?></a></p>
									</td></tr>
								<?php $i++; } ?>
								</tbody></table>
							<?php } else { ?>
							<h3 style="color:#b0b0b0" id='stop-loading'><center><i class="fe fe-code"></i><br><?php echo $lang['settings-no_reports']; ?></center></h3><br>
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
		$('.custom_table').DataTable({
			"dom" : "rtip"
		});
		
		$('.approve-machine').on('click' , '.approve-report-item' , function() {
			var id = $(this).data('id');
			if(confirm('<?php echo $lang['admin-reports-approve_report-alert']; ?>')) {
				$(this).parent().parent().parent().hide();
				$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=approve-report", {id:id, data: $(this).data('obj') , report_id: $(this).data('report_id'),  hash:'<?php echo $random_hash; ?>'}, function(data){
					parseThisTo(data,'.container');
				});
			}
		});
		$('.approve-machine').on('click' , '.reject-report-item' , function() {
			var id = $(this).data('id');
			if(confirm('<?php echo $lang['admin-reports-reject_report-alert']; ?>')) {
				$(this).parent().parent().parent().hide();
				$.post("<?php echo WEB_LINK; ?>public/includes/one_ajax.php?type=reject-report", {id:id, data: $(this).data('obj') , report_id: $(this).data('report_id') , hash:'<?php echo $random_hash; ?>'}, function(data){
					parseThisTo(data,'.container');
				});
			}
		});
		
	});
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>