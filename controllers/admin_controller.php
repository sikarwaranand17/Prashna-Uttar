<?php
  class AdminController {
    public function run() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('admin.read',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Admin Panel';
		require_once(VIEW_PATH . 'admin/dashboard.php');
    } 
	
	public function settings() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('general_settings.update',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'General Settings';
		require_once(VIEW_PATH . 'admin/settings.php');
    }
	public function pending() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('pending.read',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Pending Posts';
		require_once(VIEW_PATH . 'admin/pending.php');
    }
	public function reports() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('pending.read',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Pending Reports';
		require_once(VIEW_PATH . 'admin/reports.php');
    }
	public function users() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('adminusers.read',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Manage Users';
		require_once(VIEW_PATH . 'admin/users.php');
    }
	public function groups() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('groups.read',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Manage Groups';
		require_once(VIEW_PATH . 'admin/groups.php');
    }
	public function pages() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('pages.update',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Edit Pages';
		require_once(VIEW_PATH . 'admin/pages.php');
    }
	public function topics() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('admintopics.update',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Edit Topics';
		require_once(VIEW_PATH . 'admin/topics.php');
    }
	public function ads() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('admanager.update',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Ads Manager';
		require_once(VIEW_PATH . 'admin/ads.php');
    }
	public function filter() {
		global $db;
		global $elhash;
		global $session;
		global $settings;
		global $url_mapper;
		global $addthis_info;
		global $analytics_info;
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
		require_once(LIBRARY_PATH ."lang/lang.{$settings['site_lang']}.php");
		
		if(!$current_user->can_see_this('profanity_filter.update',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		
		$page = "admin.read";
		
		$title = 'Profanity Filter';
		require_once(VIEW_PATH . 'admin/filter.php');
    }
  }
?>