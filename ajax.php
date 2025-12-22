<?php
ob_start();
include 'admin_class.php';

$crud = new Action();
$action = isset($_GET['action']) ? $_GET['action'] : '';

/* ================== AUTH ================== */
if($action == 'login'){
	echo $crud->login();
	exit;
}

if($action == 'logout'){
	echo $crud->logout();
	exit;
}

/* ================== USER ================== */
if($action == 'save_user'){
	echo $crud->save_user();
	exit;
}

/* ================== SETTINGS ================== */
if($action == 'save_settings'){
	echo $crud->save_settings();
	exit;
}

/* ================== CATEGORY ================== */
if($action == 'save_category'){
	echo $crud->save_category();
	exit;
}

if($action == 'delete_category'){
	echo $crud->delete_category();
	exit;
}

/* ================== ROOM ================== */
if($action == 'save_room'){
	echo $crud->save_room();
	exit;
}

if($action == 'delete_room'){
	echo $crud->delete_room();
	exit;
}

/* ================== CHECK IN / OUT ================== */
/* ❗ ĐỔI save_check-in → save_check_in (chuẩn PHP & JS) */
if($action == 'save_check_in'){
	echo $crud->save_check_in();
	exit;
}

if($action == 'save_checkout'){
	echo $crud->save_checkout();
	exit;
}

/* ================== BOOKING ================== */
if($action == 'save_book'){
	echo $crud->save_book();
	exit;
}

/* ================== DELETE BOOKING / CHECKED ================== */
if($action == 'delete_check_out'){
	echo $crud->delete_check_out();
	exit;
}
