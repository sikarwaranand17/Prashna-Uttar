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
				<?php if($current_user->can_see_this('dashboard.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/']; ?>" class="col-md-12 current"><?php echo $lang['admin-section-dashboard']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('general_settings.update' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/settings']; ?>" class="col-md-12"><?php echo $lang['admin-section-general']; ?></a></li><?php } ?>
				<?php if($current_user->can_see_this('pending.read' , $group)) { ?><li><a href="<?php echo $url_mapper['admin/pending']; ?>" class="col-md-12"><?php echo $lang['admin-section-pending'] . ' ' . $pending_posts_badge; ?></a></li><?php } ?>
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
		<h5 class="page-header"><?php echo $lang['admin-hello']; ?>, <?php echo $current_user->f_name; ?>!</h5>
				
				
			<p style="font-size:16px"><?php $str = $lang['admin-dashboard-users']; $str = str_replace('[COUNT]' , User::count_everything(' AND id != 1000 AND deleted = 0 ') , $str ); echo $str; ?></p>
			
				<canvas id="user-registration"  class="full" height="100"></canvas>
			
				<br><hr><br>

				<p style="font-size:16px"><?php $str = $lang['admin-dashboard-questions']; $str = str_replace('[COUNT]' , Question::count_everything() , $str ); echo $str; ?></p>
				
				
				<canvas id="questions"  class="full" height="100"></canvas>
				
				<br><hr><br>

				<p style="font-size:16px"><?php $str = $lang['admin-dashboard-answers']; $str = str_replace('[COUNT]' , Answer::count_everything() , $str ); echo $str; ?></p>
				
				
				<canvas id="answers"  class="full" height="100"></canvas>
				
				<hr>
				<br style="clear:both">
		</div></div>
	</div>
</div>
</div>
<?php require_once(VIEW_PATH.'pages/preloader.php'); ?>
<script src="<?php echo WEB_LINK; ?>public/plugins/charts-chartjs/Chart.min.js"></script>  <!-- ChartJS Chart -->	
<script type="text/javascript">
	$(document).ready(function() {
		/**** Line Charts: ChartJs ****/
		  var lineChartData = {
			<?php 
			$months = array();
			$numbers = array();
			for($i = 0; $i < 21 ; $i++) {
				$months[] = strftime("%d.%m" , strtotime("-{$i} Day" , time())); 
				$numbers[] = Question::count_everything(' AND DATE_FORMAT(created_at, "%d-%m-%Y") = "' . strftime("%d-%m-%Y" , strtotime("-{$i} Day" , time())) . '" ');
			}
			
			?>
			labels : [<?php echo '"' . implode('","' , $months) . '"'; ?>],
			datasets : [
			  {
				label: "Questions",
				fillColor : "rgba(49, 157, 181,0.2)",
				strokeColor : "#319DB5",
				pointColor : "#319DB5",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#319DB5",
				data : [<?php echo '"' . implode('","' , $numbers) . '"'; ?>]
			  }
			]
		  }
		  var ctx = document.getElementById("questions").getContext("2d");
		  window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true,
			tooltipCornerRadius: 0
		  });
		
			/**** Line Charts: ChartJs ****/
		  var lineChartData = {
			<?php 
			$months = array();
			$numbers = array();
			for($i = 0; $i < 21 ; $i++) {
				$months[] = strftime("%d.%m" , strtotime("-{$i} Day" , time())); 
				$numbers[] = Answer::count_everything(' AND DATE_FORMAT(created_at, "%d-%m-%Y") = "' . strftime("%d-%m-%Y" , strtotime("-{$i} Day" , time())) . '" ');
			}
			
			?>
			labels : [<?php echo '"' . implode('","' , $months) . '"'; ?>],
			datasets : [
			  {
				label: "Answers",
				fillColor : "rgba(49, 157, 181,0.2)",
				strokeColor : "#319DB5",
				pointColor : "#319DB5",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#319DB5",
				data : [<?php echo '"' . implode('","' , $numbers) . '"'; ?>]
			  }
			]
		  }
		  var ctx = document.getElementById("answers").getContext("2d");
		  window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true,
			tooltipCornerRadius: 0
		  });
		
			
			
			/**** Line Charts: ChartJs ****/
		  var lineChartData = {
			<?php 
			$months = array();
			$numbers = array();
			for($i = 0; $i < 21 ; $i++) {
				$months[] = strftime("%d.%m" , strtotime("-{$i} Day" , time())); 
				$numbers[] = User::count_everything(' AND DATE_FORMAT(joined, "%d-%m-%Y") = "' . strftime("%d-%m-%Y" , strtotime("-{$i} Day" , time())) . '" ');
			}
			
			?>
			labels : [<?php echo '"' . implode('","' , $months) . '"'; ?>],
			datasets : [
			  {
				label: "New Registrations",
				fillColor : "rgba(49, 157, 181,0.2)",
				strokeColor : "#319DB5",
				pointColor : "#319DB5",
				pointStrokeColor : "#fff",
				pointHighlightFill : "#fff",
				pointHighlightStroke : "#319DB5",
				data : [<?php echo '"' . implode('","' , $numbers) . '"'; ?>]
			  }
			]
		  }
		  var ctx = document.getElementById("user-registration").getContext("2d");
		  window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true,
			tooltipCornerRadius: 0
		  });
      });
</script>
<?php require_once(VIEW_PATH.'pages/footer.php'); ?>