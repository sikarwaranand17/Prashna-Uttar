<?php
function call($controller, $action , $id="") {
	require_once(CONTROLLER_PATH . $controller . '_controller.php');
	
	switch($controller) {
		case 'login':
			$controller = new LoginController();
		break;
		case 'notifications':
			$controller = new NotificationController();
		break;
		case 'leaderboard':
			$controller = new LeaderboardController();
		break;
		case 'index':
			$controller = new IndexController();
		break;
		case 'ad':
			$controller = new AdController();
		break;
		case 'page':
			$controller = new PageController();
		break;
		case 'question':
			$controller = new PostController();
		break;
		case 'spaces':
			$controller = new SpacesController();
		break;
		case 'space':
			$controller = new SpaceController();
		break;
		case 'admin':
			$controller = new AdminController();
		break;
		case 'users':
			$controller = new UsersController();
		break;
		case 'rss':
			$controller = new RssController();
		break;
		case 'logout':
			$controller = new LogoutController();
		break;
	}

	$controller->{ $action }($id);
}

// we're adding an entry for the new controller and its actions
$controllers = array(
							'login' => ['run', 'error'],
							'page' => ['about_us' , 'contact_us' , 'privacy_policy' , 'terms'],
							'index' => ['run' , 'notifications', 'ad' , 'error'],
							'notifications' => ['run' , 'ad' , 'error'],
							'leaderboard' => ['run' , 'ad' , 'error'],
							'spaces' => ['run'],
							'space' => ['read' , 'update' , 'delete'],
							'question' => ['create', 'read' , 'update' , 'delete'],
							'admin' => ['run','settings','pending','reports','users','groups','pages','topics','ads','filter', 'error'],
							'users' => ['view' , 'error'],
							'rss' => ['run','user','feed','question'],
							'logout' => ['run'],
							'ad' => ['run','error']
				   );

if (array_key_exists($controller, $controllers)) {
	if (in_array($action, $controllers[$controller])) {
		if(!isset($id)) { $id = ""; }
		call($controller, $action , $id);
	} else {
		call('index', 'error');
	}
} else {
	call('index', 'error');
}
