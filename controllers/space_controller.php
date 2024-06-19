<?php
  class SpaceController {
    
	public function read($q_slug) {
		
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
		
		if(!$current_user->can_see_this('spaces.read',$group)) {
			$msg = $lang['alert-restricted'];
			redirect_to($url_mapper['index/']."?edit=fail&msg={$msg}");
			exit();
		}
		if($q_slug) {
			$page = "spaces.read";
			$title = 'Read Space';
			$elhash_login = "ScU8rNKXHGGyYDh4voHu";
			require_once(VIEW_PATH . 'spaces/read.php');
		} else {
			redirect_to($url_mapper['error/404/']); 
		}
		
    }
	
    public function delete($q_slug) {
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
		
		
			if($_GET['hash'] != $_SESSION[$elhash] ) {
				redirect_to($url_mapper['index/']);
			}
			
		if($q_slug) {
			
			if(URLTYPE == 'id') {
				$q = Space::get_specific_id($q_slug);
			} else {
				$q = Space::get_slug($q_slug);
			}
			
			if(!$current_user->can_see_this("spaces.delete",$group)) {
				$msg = $lang['alert-restricted'];
				if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
				redirect_to($url_mapper['spaces/view'].$url_type."&edit=fail&msg={$msg}");
			}
			
			if($q) {
				
				if($current_user->prvlg_group != '1' && $q->user_id != $current_user->id ) {
					$msg = $lang['alert-restricted'];
					if(URLTYPE == 'slug') {$url_type = $q->slug;} else {$url_type = $q->id;}
					redirect_to($url_mapper['spaces/view'].$url_type."&edit=fail&msg={$msg}");
				}
				
				if($q->delete()) {
					
					$follows = FollowRule::get_subscriptions('space',$q->id , 'obj_id' , "" );
					if($follows) {
						foreach($follows as $f) {
							$f->delete();
						}
					}
					$reports = Report::get_everything(" AND obj_type = 'space' AND obj_id = '{$q->id}' ");
					foreach($reports as $r) {
						$r->delete();
					}
					
					$questions = Question::get_everything(" AND space_id = '{$q->id}' ");
					if($questions) {
						foreach($questions as $found) {
							$follows = FollowRule::get_subscriptions('question',$found->id , 'obj_id' , "" );
							if($follows) {
								foreach($follows as $f) {
									$f->delete();
								}
							}
							$reports = Report::get_everything(" AND obj_type = 'question' AND obj_id = '{$found->id}' ");
							foreach($reports as $r) {
								$r->delete();
							}
							
							$answers = Answer::get_answers_for($found->id);
							foreach($answers as $a) {
								$a->delete();
								$reports = Report::get_everything(" AND obj_type = 'answer' AND obj_id = '{$a->id}' ");
								foreach($reports as $r) {
									$r->delete();
								}
							}
						}
					}
					
					
					$msg = $lang['alert-delete_success'];
					redirect_to($url_mapper['spaces/']."&edit=success&msg={$msg}");
				} else {
					$msg = $lang['alert-delete_failed'];
					redirect_to($url_mapper['spaces/']."&edit=fail&msg={$msg}");
				}
			} else {
				redirect_to($url_mapper['error/404/']); 
			}
			
		} else {
			redirect_to($url_mapper['error/404/']); 
		}
		
    }

    
  }
?>