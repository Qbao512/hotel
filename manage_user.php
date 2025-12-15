<?php 
include('db_connect.php');
if(isset($_GET['id'])){
$user = $conn->query("SELECT * FROM users where id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<div class="container-fluid">
	
	<form action="" id="manage-user">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="name">Họ và tên</label>
			<input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="username">Tên đăng nhập</label>
			<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="password">Mật khẩu</label>
			<input type="password" name="password" id="password" class="form-control" value="" placeholder="Để trống nếu không thay đổi">
		</div>
		<div class="form-group">
			<label for="type">Loại tài khoản</label>
			<select name="type" id="type" class="custom-select">
				<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>Quản trị viên</option>
				<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected': '' ?>>Nhân viên</option>
			</select>
		</div>
	</form>
</div>

<script>
	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=save_user',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Lưu dữ liệu thành công",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	})
</script>