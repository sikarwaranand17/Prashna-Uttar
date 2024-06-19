<div class="col-lg-3 d-none d-lg-block">
	<div class="card post-item">
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
		<hr>
<?php
$ads = ads('right_sidebar');
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
	<center><a href="https://www.michael-designs.com/" target="_blank" class="text-muted text-decoration-none "><small>Michael Designs &copy; 2020</small></a></center>
</div>
