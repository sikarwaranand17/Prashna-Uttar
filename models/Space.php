<?php

class Space Extends OneClass {
	
	public static $table_name = "spaces";
	public static $db_fields = array( "id" , "user_id" , "admins" , "moderators", "contributors", "name", "tagline", "description" ,"feed","tags", "follows", "views" ,"slug", "created_at" , "avatar", "cover", "open_post");
	
	public $id;
	public $user_id;
	public $admins;
	public $moderators;
	public $contributors;
	public $name;
	public $tagline;
	public $description;
	public $feed;
	public $tags;
	public $follows;
	public $views;
	public $slug;
	public $created_at;
	public $avatar;
	public $cover;
	public $open_post;
	
	public function view_s() {
		$this->views += 1;
		$this->update();
	}
	
	public static function check_slug($slug) {
		global $db;
		return self::preform_sql("SELECT id FROM " . DBTP . self::$table_name . " WHERE slug = '{$slug}' ORDER BY created_at ASC" );
	}
	
	public static function check_slug_except($slug , $id) {
		global $db;
		return self::preform_sql("SELECT id FROM " . DBTP . self::$table_name . " WHERE slug = '{$slug}' AND id != {$id} ORDER BY created_at ASC" );
	}
	
	public static function get_slug($slug) {
		$result_array =  static::preform_sql("SELECT * FROM " . DBTP . static::$table_name . " WHERE slug = '" . $slug . "' ORDER BY id DESC LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function get_tagcloud() {
		global $db;
		$result = $db->query("SELECT GROUP_CONCAT(feed SEPARATOR ',') AS tagcloud FROM " . DBTP . static::$table_name . " group by 'all' ");
		$arr = mysqli_result($result, 0);
		@$tags = explode(',' , $arr);
		if(is_array($tags) && !empty($tags) ) {
			$tags = array_unique($tags); 
			return $tags; 
		} else { return false; }
	}
	
	public function get_avatar() {
		global $db;
		if($this->avatar) {
			$img = File::get_specific_id($this->avatar);
			$dev_avatar = WEB_LINK."public/".$img->image_path();
			$dev_avatar_path = UPLOAD_PATH."/".$img->image_path();
			if (!file_exists($dev_avatar_path)) {
				$dev_avatar = WEB_LINK.'public/img/space.png';
			}
		} else {
			$dev_avatar = WEB_LINK.'public/img/space.png';
		}
		return $dev_avatar;
	}
	
	public function get_cover() {
		global $db;
		if($this->cover) {
			$img = File::get_specific_id($this->cover);
			$dev_avatar = WEB_LINK."public/".$img->image_path();
			$dev_avatar_path = UPLOAD_PATH."/".$img->image_path();
			if (!file_exists($dev_avatar_path)) {
				$dev_avatar = WEB_LINK.'public/img/cover.jpg';
			}
		} else {
			$dev_avatar = WEB_LINK.'public/img/cover.jpg';
		}
		return $dev_avatar;
	}
	
}
	
?>