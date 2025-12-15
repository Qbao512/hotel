<?php
session_start();
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users WHERE username = '$username' AND password = '$password'");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			return ($_SESSION['login_type'] == 1) ? 1 : 2;
		}else{
			return 3; // Sai tài khoản/mật khẩu
		}
	}

	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		if(!empty($password))
			$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";

		if(empty($id)){
			$save = $this->db->query("INSERT INTO users SET ".$data);
			if($save) return 1; // Thêm người dùng thành công
		}else{
			$save = $this->db->query("UPDATE users SET ".$data." WHERE id = ".$id);
			if($save) return 1; // Cập nhật thành công
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " hotel_name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";

		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('YmdHis')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
			$data .= ", cover_img = '$fname' ";
		}

		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings SET ".$data." WHERE id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings SET ".$data);
		}

		if($save){
			$query = $this->db->query("SELECT * FROM system_settings LIMIT 1")->fetch_array();
			foreach ($query as $key => $value) {
				if(!is_numeric($key))
					$_SESSION['setting_'.$key] = $value;
			}
			return 1; // Cập nhật cài đặt thành công
		}
	}

	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", price = '$price' ";

		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('YmdHis')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
			$data .= ", cover_img = '$fname' ";
		}

		if(empty($id)){
			$save = $this->db->query("INSERT INTO room_categories SET ".$data);
		}else{
			$save = $this->db->query("UPDATE room_categories SET ".$data." WHERE id = ".$id);
		}
		return $save ? 1 : 0;
	}

	function delete_category(){
		extract($_POST);
		// Kiểm tra xem có phòng nào đang dùng loại này không
		$check = $this->db->query("SELECT * FROM rooms WHERE category_id = $id")->num_rows;
		if($check > 0){
			return 2; // Không thể xóa vì đang có phòng sử dụng
		}
		$delete = $this->db->query("DELETE FROM room_categories WHERE id = ".$id);
		return $delete ? 1 : 0;
	}

	function save_room(){
		extract($_POST);
		$data = " room = '$room' ";
		$data .= ", category_id = '$category_id' ";
		$data .= ", status = '$status' ";

		if(empty($id)){
			$save = $this->db->query("INSERT INTO rooms SET ".$data);
		}else{
			$save = $this->db->query("UPDATE rooms SET ".$data." WHERE id = ".$id);
		}
		return $save ? 1 : 0;
	}

	function delete_room(){
		extract($_POST);
		// Kiểm tra phòng có đang được sử dụng không
		$check = $this->db->query("SELECT * FROM checked WHERE room_id = $id AND status != 2")->num_rows;
		if($check > 0){
			return 2; // Không thể xóa vì phòng đang có khách
		}
		$delete = $this->db->query("DELETE FROM rooms WHERE id = ".$id);
		return $delete ? 1 : 0;
	}

	function save_check_in(){
		extract($_POST);
		$data = " room_id = '$rid' ";
		$data .= ", name = '$name' ";
		$data .= ", contact_no = '$contact' ";
		$data .= ", status = 1 ";
		$data .= ", date_in = '".$date_in.' '.$date_in_time."' ";
		$out = date("Y-m-d H:i", strtotime($date_in.' '.$date_in_time.' + '.$days.' days'));
		$data .= ", date_out = '$out' ";

		// Tạo mã đặt phòng duy nhất
		do {
			$ref = sprintf("%06d", mt_rand(1, 999999));
			$check_ref = $this->db->query("SELECT id FROM checked WHERE ref_no = '$ref'")->num_rows;
		} while($check_ref > 0);
		$data .= ", ref_no = '$ref' ";
		if(isset($booked_cid)) $data .= ", booked_cid = '$booked_cid' ";

		if(empty($id)){
			$save = $this->db->query("INSERT INTO checked SET ".$data);
			$last_id = $this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE checked SET ".$data." WHERE id = ".$id);
			$last_id = $id;
		}

		if($save){
			$this->db->query("UPDATE rooms SET status = 1 WHERE id = ".$rid);
			return $last_id;
		}
	}

	function save_checkout(){
		extract($_POST);
		$save = $this->db->query("UPDATE checked SET status = 2 WHERE id = ".$id);
		if($save){
			$this->db->query("UPDATE rooms SET status = 0 WHERE id = ".$rid);
			return 1;
		}
	}

	function save_book(){
		extract($_POST);
		$data = " booked_cid = '$cid' ";
		$data .= ", name = '$name' ";
		$data .= ", contact_no = '$contact' ";
		$data .= ", status = 0 ";
		$data .= ", date_in = '".$date_in.' '.$date_in_time."' ";
		$out = date("Y-m-d H:i", strtotime($date_in.' '.$date_in_time.' + '.$days.' days'));
		$data .= ", date_out = '$out' ";

		// Tạo mã đặt phòng duy nhất
		do {
			$ref = sprintf("%06d", mt_rand(1, 999999));
			$check_ref = $this->db->query("SELECT id FROM checked WHERE ref_no = '$ref'")->num_rows;
		} while($check_ref > 0);
		$data .= ", ref_no = '$ref' ";

		$save = $this->db->query("INSERT INTO checked SET ".$data);
		if($save){
			return $this->db->insert_id;
		}
	}
}