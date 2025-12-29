<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Đăng nhập quản trị | Hệ thống quản lý khách sạn</title>
 	
<?php include('./header.php'); ?>
<?php include('./db_connect.php'); ?>
<?php 
session_start();
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

$query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}
?>

</head>
<style>
	body{
		width: 100%;
	    height: calc(100%);
	}
	main#main{
		width:100%;
		height: calc(100%);
		background:white;
	}
	#login-right{
		position: absolute;
		right:0;
		width:40%;
		height: calc(100%);
		background:white;
		display: flex;
		align-items: center;
	}
	#login-left{
		position: absolute;
		left:0;
		width:60%;
		height: calc(100%);
		background:#00000061;
		display: flex;
		align-items: center;
	}
	#login-right .card{
		margin: auto
	}
	.logo {
	    margin: auto;
	    font-size: 8rem;
	    background: white;
	    padding: .5em 0.8em;
	    border-radius: 50% 50%;
	    color: #000000b3;
	}
	#login-left {
	  background: url(../assets/img/<?php echo $_SESSION['setting_cover_img'] ?>);
	  background-repeat: no-repeat;
	  background-size: cover;
	}
</style>

<body>

  <main id="main" class="alert-info">
  		<div id="login-left">
  		</div>
  		<div id="login-right">
  			<div class="card col-md-8">
  				<div class="card-body">
  					<form id="login-form">
  						<div class="form-group">
  							<label for="username" class="control-label">Tên đăng nhập</label>
  							<input type="text" id="username" name="username" class="form-control" placeholder="Nhập tên đăng nhập">
  						</div>
  						<div class="form-group">
  							<label for="password" class="control-label">Mật khẩu</label>
  							<input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu">
  						</div>
  						<center><button class="btn-sm btn-block btn-wave col-md-4 btn-primary">Đăng nhập</button></center>
  					</form>
  				</div>
  			</div>
  		</div>
  </main>

  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

</body>
<script>
	$('#login-form').submit(function(e){
		e.preventDefault()
		$('#login-form button[type="button"]').attr('disabled',true).html('Đang đăng nhập...');
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
		$.ajax({
			url:'ajax.php?action=login',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				console.log(err)
				$('#login-form button[type="button"]').removeAttr('disabled').html('Đăng nhập');
			},
			success:function(resp){
				if(resp == 1){
					location.href ='index.php?page=home';
				}else if(resp == 2){
					location.href ='voting.php';
				}else{
					$('#login-form').prepend('<div class="alert alert-danger">Tên đăng nhập hoặc mật khẩu không đúng.</div>')
					$('#login-form button[type="button"]').removeAttr('disabled').html('Đăng nhập');
				}
			}
		})
	})
</script>	
</html>
