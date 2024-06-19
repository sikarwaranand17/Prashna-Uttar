<?php

class Tag Extends OneClass {
	
	public static $table_name = "tags";
	public static $db_fields = array( "id" , "name" , "follows" , "description" , "avatar" , "used", "deleted");
	
	public $id;
	public $name;
	public $follows;
	public $description;
	public $avatar;
	public $used;
	public $deleted;
	
	public static function count_trending($limit = "") {
		global $db;
		$result = $db->query("SELECT COUNT(id) FROM " . DBTP . self::$table_name . " ORDER BY used DESC " . $limit );
		return mysqli_result($result, 0);
	}
	
	public static function get_trending($limit = "LIMIT 10") {
		global $db;
		return self::preform_sql("SELECT id,name, avatar FROM " . DBTP . self::$table_name . " ORDER BY used DESC " . $limit );
	}
	
	public static function get_tag($name) {
		global $db;
		$result_array = self::preform_sql("SELECT * FROM " . DBTP . self::$table_name . " WHERE name= '{$name}' ORDER BY id DESC LIMIT 1" );
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	public function get_avatar() {
		global $db;
		if($this->avatar) {
			$img = File::get_specific_id($this->avatar);
			$dev_avatar = WEB_LINK."public/".$img->image_path();
			$dev_avatar_path = UPLOAD_PATH."/".$img->image_path();
			if (!file_exists($dev_avatar_path)) {
				$dev_avatar = WEB_LINK.'public/img/tag.png';
			}
		} else {
			$dev_avatar = WEB_LINK.'public/img/tag.png';
		}
		return $dev_avatar;
	}
}
	
?>