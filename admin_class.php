<?php
session_start();

class Action {
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

	/* ===================== LOGIN ===================== */
	function login(){
		extract($_POST);

		$qry = $this->db->query("SELECT * FROM users 
			WHERE username = '$username' AND password = '$password'");

		if($qry->num_rows > 0){
			$row = $qry->fetch_assoc();
			foreach ($row as $key => $value) {
				if($key != 'password')
					$_SESSION['login_'.$key] = $value;
			}

			/*
			 type = 1 → ADMIN
			 type = 2 → NHÂN VIÊN
			*/
			return ($_SESSION['login_type'] == 1) ? 1 : 2;
		}
		return 3; // sai tài khoản / mật khẩu
	}

	function logout(){
		session_destroy();
		header("location:login.php");
	}

	/* ===================== USER ===================== */
	function save_user(){
		extract($_POST);
		$data = " name='$name', username='$username', type='$type' ";
		if(!empty($password))
			$data .= ", password='$password' ";

		if(empty($id)){
			return $this->db->query("INSERT INTO users SET ".$data) ? 1 : 0;
		}else{
			return $this->db->query("UPDATE users SET ".$data." WHERE id=".$id) ? 1 : 0;
		}
	}

	/* ===================== SETTINGS ===================== */
	function save_settings(){
		extract($_POST);
		$data = " hotel_name='$name', email='$email', contact='$contact',
				  about_content='".htmlentities($about)."' ";

		if($_FILES['img']['tmp_name'] != ''){
			$fname = time().'_'.$_FILES['img']['name'];
			move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/'.$fname);
			$data .= ", cover_img='$fname' ";
		}

		$chk = $this->db->query("SELECT id FROM system_settings")->num_rows;
		if($chk > 0){
			$this->db->query("UPDATE system_settings SET ".$data);
		}else{
			$this->db->query("INSERT INTO system_settings SET ".$data);
		}

		$q = $this->db->query("SELECT * FROM system_settings LIMIT 1")->fetch_assoc();
		foreach($q as $k=>$v)
			$_SESSION['setting_'.$k] = $v;

		return 1;
	}

	/* ===================== CATEGORY ===================== */
	function save_category(){
		extract($_POST);
		$data = " name='$name', price='$price' ";

		if($_FILES['img']['tmp_name'] != ''){
			$fname = time().'_'.$_FILES['img']['name'];
			move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/'.$fname);
			$data .= ", cover_img='$fname' ";
		}

		if(empty($id))
			return $this->db->query("INSERT INTO room_categories SET ".$data) ? 1 : 0;
		else
			return $this->db->query("UPDATE room_categories SET ".$data." WHERE id=".$id) ? 1 : 0;
	}

	function delete_category(){
		extract($_POST);
		$chk = $this->db->query("SELECT id FROM rooms WHERE category_id=$id")->num_rows;
		if($chk > 0) return 2;
		return $this->db->query("DELETE FROM room_categories WHERE id=$id") ? 1 : 0;
	}

	/* ===================== ROOM ===================== */
	function save_room(){
		extract($_POST);
		$data = " room='$room', category_id='$category_id', status='$status' ";

		if(empty($id))
			return $this->db->query("INSERT INTO rooms SET ".$data) ? 1 : 0;
		else
			return $this->db->query("UPDATE rooms SET ".$data." WHERE id=".$id) ? 1 : 0;
	}

	function delete_room(){
		extract($_POST);
		$chk = $this->db->query("SELECT id FROM checked WHERE room_id=$id AND status!=2")->num_rows;
		if($chk > 0) return 2;
		return $this->db->query("DELETE FROM rooms WHERE id=$id") ? 1 : 0;
	}

	/* ===================== CHECK IN ===================== */
	function save_check_in(){
		extract($_POST);
		$out = date("Y-m-d H:i", strtotime("$date_in $date_in_time + $days days"));

		do{
			$ref = rand(100000,999999);
		}while($this->db->query("SELECT id FROM checked WHERE ref_no='$ref'")->num_rows > 0);

		$data = " room_id='$rid', name='$name', contact_no='$contact',
				  status=1, date_in='$date_in $date_in_time',
				  date_out='$out', ref_no='$ref' ";

		if(isset($booked_cid))
			$data .= ", booked_cid='$booked_cid' ";

		$this->db->query("INSERT INTO checked SET ".$data);
		$this->db->query("UPDATE rooms SET status=1 WHERE id=$rid");

		return 1;
	}

	/* ===================== CHECK OUT ===================== */
	function save_checkout(){
		extract($_POST);
		$this->db->query("UPDATE checked SET status=2 WHERE id=$id");
		$this->db->query("UPDATE rooms SET status=0 WHERE id=$rid");
		return 1;
	}

	/* ===================== DELETE BOOKING ===================== */
	function delete_check_out(){
		extract($_POST);
		return $this->db->query("DELETE FROM checked WHERE id=$id") ? 1 : 0;
	}

	/* ===================== BOOK ===================== */
	function save_book(){
		extract($_POST);
		$out = date("Y-m-d H:i", strtotime("$date_in $date_in_time + $days days"));

		do{
			$ref = rand(100000,999999);
		}while($this->db->query("SELECT id FROM checked WHERE ref_no='$ref'")->num_rows > 0);

		$data = " booked_cid='$cid', name='$name', contact_no='$contact',
				  status=0, date_in='$date_in $date_in_time',
				  date_out='$out', ref_no='$ref' ";

		return $this->db->query("INSERT INTO checked SET ".$data) ? 1 : 0;
	}
}
