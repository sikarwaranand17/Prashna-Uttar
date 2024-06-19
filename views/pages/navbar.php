<?php 
$navsec = 'index';
if(!isset($page)) {
	$page = "index.read";
}
if($page == 'spaces.read') {
	$navsec = 'spaces';
}

if($page == 'users.read') {
	$navsec = 'users';
}

if($page == 'admin.read') {
	$navsec = 'admin';
}

if($page == 'index.read') {
	$navsec = 'index';
}

if($page == 'notifications.read') {
	$navsec = 'notifications';
}

?>
<body style='background-color: #f1f2f2'>
<nav class="navbar navbar-default navbar-expand-lg navbar-light navbar-fixed-top d-none d-lg-flex">
  <a class="navbar-brand" href="<?php echo $url_mapper['index/']; ?>" style="color:#b92b27;font-size:25px">
	<?php 
		if(!empty($settings['site_logo'])) {
			$file = File::get_specific_id($settings['site_logo']);
	?>
		<img src="<?php echo WEB_LINK."public/".$file->image_path(); ?>" style='max-height:30px;' id="site-logo" class='' />
	<?php } else {
		echo APPNAME; 
	}
  ?>
  
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse row" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto col-6" >
      <li class="nav-item <?php if($navsec == 'index') { echo 'active'; } ?>">
        <a class="nav-link " href="<?php echo $url_mapper['index/']; ?>"><i class="fe fe-list"></i> <?php echo $lang['index-read-button']; ?></a>
      </li>
      
      <li class="nav-item <?php if($navsec == 'spaces') { echo 'active'; } ?>">
        <a class="nav-link " href="<?php echo $url_mapper['spaces/']; ?>"><i class="fe fe-users"></i> <?php echo $lang['index-spaces-button']; ?></a>
      </li>
      
		<?php if($current_user->id != '1000') {
		$notif_count = Notif::count_everything(" AND user_id = '{$current_user->id}' AND viewed = 0 ");
		?>
       <li class="nav-item <?php if($navsec == 'notifications') { echo 'active'; } ?>">
			<a class="nav-link " href="<?php echo $url_mapper['notifications/']; ?>"><i class="fe fe-bell"></i> <?php echo $lang['index-notification-button']; if($notif_count) { ?>&nbsp;<span class='badge badge-danger'><?php echo $notif_count; ?></span><?php } ?></a>
		</li>
		<?php } ?>
		
      <li class="nav-item <?php if($navsec == 'admin') { echo 'active'; } ?>">
			<?php if($current_user->can_see_this('admin.read', $group)) { ?><a class="nav-link " href="<?php echo $url_mapper['admin/']; ?>"><i class="fe fe-settings"></i> <?php echo $lang['index-admin-button']; ?></a><?php } ?>
		</li>
		
	</ul>
	<ul class="navbar-nav mr-auto col-3" >
		<form class="has-search p-0 my-2" style="width:100%">
			<span class="fa fa-search form-control-feedback"></span>
			<input class="form-control searchbox-field typeahead" data-provide="typeahead" type="search" placeholder="Search" aria-label="Search" >
		</form>
	
	</ul>
	<ul class="navbar-nav mr-auto navbar-right col-3 d-flex justify-content-end">
		<?php 
		if($current_user->id == '1000') {
			$user_avatar = WEB_LINK.'public/img/avatar.png';
		?>
			
			<li class="nav-item">
				<a class="nav-link " href="<?php echo $url_mapper['login/']; ?>"><i class="fe fe-power"></i> <?php echo $lang['index-user-login']; ?></a>
			</li>
			
		<?php
		} else {
			//global user avatar
			if($current_user->avatar) {
				$img = File::get_specific_id($current_user->avatar);
				$user_avatar = WEB_LINK."public/".$img->image_path();
				$user_avatar_path = UPLOAD_PATH."/".$img->image_path();
				if (!file_exists($user_avatar_path)) {
					$user_avatar = WEB_LINK.'public/img/avatar.png';
				}
			} else {
				$user_avatar = WEB_LINK.'public/img/avatar.png';
			} ?>
		
		 <li class="dropdown">
			<a class="nav-link p-1 mt-2 mr-2 dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  <img src="<?php echo $user_avatar; ?>" class="rounded-circle" style='height:30px'>
			</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
			  
			  <?php if($current_user->can_see_this('admin.read', $group)) { ?><a href="<?php echo $url_mapper['admin/']; ?>" class="dropdown-item"  ><?php echo $lang['index-user-admin']; ?></a><?php } ?>
			  <a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/" class="dropdown-item" ><?php echo $lang['index-user-profile']; ?></a>
			  <a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/?section=edit&hash=<?php echo $random_hash; ?>" class="dropdown-item"   ><?php echo $lang['index-user-settings']; ?></a>
			  <div class="dropdown-divider"></div>
			  <a href="<?php echo $url_mapper['logout/']; ?>" class="dropdown-item"  ><?php echo $lang['index-user-logout']; ?></a>
			</div>
		  </li>

		<a href='javascript:void(0);' class="btn btn-sm btn-pill btn-danger add-q"><?php echo $lang['index-search-button']; ?></a>
		
		
		<?php } ?>
    </ul>
  </div>
</nav>


<div class="d-lg-none d-xl-none mob-nav">
	<div class="brand pr-2 pl-2" >
		<div class="mob-search-brand">
			<a href='javascript:void(0);' class="text-white view-search text-decoration-none pull-<?php echo $lang['direction-left']; ?>" style='line-height:35px; font-size: 15px; font-family: "Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif' ><i class="fe fe-search"></i> <?php echo $lang['btn-search']; ?></a>
			<?php 
				if(!empty($settings['site_logo'])) {
					$file = File::get_specific_id($settings['site_logo']);
			?>
				<img src="<?php echo WEB_LINK."public/".$file->image_path(); ?>" style='max-height:30px;' id="site-logo" class='' />
			<?php } else {
				echo APPNAME; 
			}
			
			?>
			<a href='javascript:void(0);' class="text-white text-decoration-none pull-<?php echo $lang['direction-right']; if($current_user->id != '1000') { echo ' add-q'; } ?>" style='line-height:35px; font-size: 15px; font-family: "Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif' ><i class="fe fe-plus"></i> <?php echo $lang['btn-add']; ?></a>
		</div>
		<div class="mob-search-box row m-0 p-0 pt-1">
			<div class='col-2 m-0 p-0'><a href='javascript:void(0);' class="back-from-search text-white text-decoration-none pull-<?php echo $lang['direction-left']; ?>" style='line-height:30px; font-size: 15px; font-family: "Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif' ><i class="fe fe-chevron-<?php echo $lang['direction-left']; ?>"></i> <?php echo $lang['btn-back']; ?></a></div>
			<div class='col-10 m-0 p-0'><form class="has-search" style="width:100%;">
				<span class="fa fa-search form-control-feedback"></span>
				<input class="form-control searchbox-field typeahead" data-provide="typeahead" type="search" placeholder="Search" aria-label="Search" style=''>
			</form></div>
		</div>
	</div>

  <div class="nav-buttons d-flex">
	<div class="flex-fill"><a href='<?php echo $url_mapper['index/']; ?>' class='nav-button <?php if($navsec == 'index') { echo 'active'; } ?>'><i class='fe fe-list'></i></a></div>
	<div class="flex-fill"><a href='<?php echo $url_mapper['spaces/']; ?>' class='nav-button <?php if($navsec == 'spaces') { echo 'active'; } ?>'><i class='fe fe-users'></i></a></div>
	<?php if($current_user->id != '1000') { ?>
	<div class="flex-fill"><a href='<?php echo $url_mapper['notifications/']; ?>' class='nav-button <?php if($navsec == 'notifications') { echo 'active'; } ?>'><i class='fe fe-bell'></i><?php if($notif_count) { ?>&nbsp;<span class='badge badge-danger'><?php echo $notif_count; ?></span><?php } ?></a></div>
	<?php if($current_user->can_see_this('admin.read', $group)) { ?><div class="flex-fill"><a href='<?php echo $url_mapper['admin/']; ?>' class='nav-button <?php if($navsec == 'admin') { echo 'active'; } ?>'><i class='fe fe-settings'></i></a></div><?php } ?>
	<div class="flex-fill">
		<?php //global user avatar
			if($current_user->avatar) {
				$img = File::get_specific_id($current_user->avatar);
				$user_avatar = WEB_LINK."public/".$img->image_path();
				$user_avatar_path = UPLOAD_PATH."/".$img->image_path();
				if (!file_exists($user_avatar_path)) {
					$user_avatar = WEB_LINK.'public/img/avatar.png';
				}
			} else {
				$user_avatar = WEB_LINK.'public/img/avatar.png';
			} ?>
		
			<a class="nav-button dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height:42px; padding:0px">
			  <img src="<?php echo $user_avatar; ?>" class="rounded-circle" style='height:30px'>
			</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdown">
			  
			  <?php if($current_user->can_see_this('admin.read', $group)) { ?><a href="<?php echo $url_mapper['admin/']; ?>" class="dropdown-item"  ><?php echo $lang['index-user-admin']; ?></a><?php } ?>
			  <a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/" class="dropdown-item" ><?php echo $lang['index-user-profile']; ?></a>
			  <a href="<?php echo $url_mapper['users/view'] . $current_user->id; ?>/?section=edit&hash=<?php echo $random_hash; ?>" class="dropdown-item"   ><?php echo $lang['index-user-settings']; ?></a>
			  <div class="dropdown-divider"></div>
			  <a href="<?php echo $url_mapper['logout/']; ?>" class="dropdown-item"  ><?php echo $lang['index-user-logout']; ?></a>
			</div>
	</div>
	<?php } else { ?>
	<div class="flex-fill"><a href='<?php echo $url_mapper['login/']; ?>' class='nav-button'><i class='fe fe-power'></i></a></div>
	<?php } ?>
  </div>
</div>