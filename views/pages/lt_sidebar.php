<div class="col-lg-2 d-none d-lg-block">
	<ul class="feed-ul">
	
	<?php
			$current = '';
			if(!isset($_GET['feed']) || $_GET['feed'] == '' ) {
				$current = 'current';
			} 
			
	?>
	<li><a href="<?php echo $url_mapper['feed/']; ?>" class="btn-block <?php echo $current;  ?>"><div class="icon"><i class='fe fe-layers'></i></div> <?php echo $lang['index-feed-button']; ?></a></li>
	
	<?php 
		$except = array();
		$tags = FollowRule::get_subscriptions('tag',$current_user->id , 'user_id' , '', 'LIMIT 10');
			if($tags) {
				foreach($tags as $tag) {
					$tag = Tag::get_specific_id($tag->obj_id);
					$except[] = $tag->id;
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
				
				<li><a href="<?php echo $url_mapper['feed/'] . $tag->name; ?>" class="btn-block <?php echo $current; ?>"><?php echo $icon; ?> <?php echo $tag->name; ?></a></li>
			<?php
				}
			}
		
		 $spaces = FollowRule::get_subscriptions('space',$current_user->id , 'user_id' , '', 'LIMIT 10');
		 
			if($spaces) {
				foreach($spaces as $tag) {
					$space = Space::get_specific_id($tag->obj_id);
					if($space->avatar) {
						$img = File::get_specific_id($space->avatar);
						$quser_avatar = WEB_LINK."public/".$img->image_path();
						$quser_avatar_path = UPLOAD_PATH."/".$img->image_path();
						if (file_exists($quser_avatar_path)) {
							$icon= "<img src='{$quser_avatar}'>";
						} else {
							$url = WEB_LINK."public/img/space.png";
							$icon= "<img src='{$url}'>";
						}					
					} else {
						$url = WEB_LINK."public/img/space.png";
						$icon= "<img src='{$url}'>";
					}
				
					if(URLTYPE == 'id') {
						$space_url = $url_mapper['spaces/view']. $space->id;
					} else {
						$space_url = $url_mapper['spaces/view']. $space->slug;
					}
			?>
				<li><a href="<?php echo $space_url; ?>" class="btn-block "><?php echo $icon; ?><?php echo $space->name; ?></a></li>
				
			<?php
				}
			}
			
			
		$per_page = 10;
		$page = 1;
		$tag_count = Tag::count_trending('');
		$pagination = new Pagination($page, $per_page, $tag_count);
		
		$tags = Tag::get_trending(" LIMIT {$per_page} OFFSET {$pagination->offset()} ");
		
			if($tags) {
				foreach($tags as $tag) {
					if(!in_array($tag->id, $except)) {
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
			}
			
			
		?>
		
		<div class='tag-holder'></div>
		<input type='text' class='tag-page d-none' value='1' readonly>
		
		<?php
		if(isset($pagination) && $pagination->total_pages() > 1) {
		?>
			<li><hr><center><a href='#me' class='btn-block load-tag' >Load More</a></center></li>
		<?php } ?>
		
	</ul>
	
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
	