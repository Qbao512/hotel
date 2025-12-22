<?php 
include('db_connect.php');
$calc_days = abs(strtotime($_GET['out']) - strtotime($_GET['in'])); 
$calc_days = floor($calc_days / (60*60*24));
?>
<div class="container-fluid">
	
	<form action="" id="manage-check">
		<input type="hidden" name="cid" value="<?php echo $_GET['cid'] ?? '' ?>">
		<input type="hidden" name="rid" value="<?php echo $_GET['rid'] ?? '' ?>">

		<div class="form-group">
			<label for="name"><b>Họ và tên khách</b></label>
			<input type="text" name="name" id="name" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" required>
		</div>

		<div class="form-group">
			<label for="contact"><b>Số điện thoại</b></label>
			<input type="text" name="contact" id="contact" class="form-control" placeholder="Ví dụ: 0901234567" required>
		</div>

		<div class="form-group">
			<label for="date_in"><b>Ngày nhận phòng</b></label>
			<input type="date" name="date_in" id="date_in" class="form-control" 
			       value="<?php echo date('Y-m-d', strtotime($_GET['in'])) ?>" readonly>
		</div>

		<div class="form-group">
			<label for="date_in_time"><b>Giờ nhận phòng</b></label>
			<input type="time" name="date_in_time" id="date_in_time" class="form-control" 
			       value="<?php echo date('H:i') ?>" required>
		</div>

		<div class="form-group">
			<label for="days"><b>Số ngày ở</b></label>
			<input type="number" name="days" id="days" class="form-control" 
			       value="<?php echo $calc_days ?>" readonly>
			<small class="text-muted">Từ <?php echo date('d/m/Y', strtotime($_GET['in'])) ?> → <?php echo date('d/m/Y', strtotime($_GET['out'])) ?></small>
		</div>

		<div class="text-center mt-4">
			<button type="submit" class="btn btn-success btn-lg px-5">
				Xác nhận đặt phòng
			</button>
		</div>
	</form>
</div>

<script>
	$('#manage-check').submit(function(e){
		e.preventDefault();
		
		// Kiểm tra nhanh trước khi gửi
		if($('#name').val().trim() == '' || $('#contact').val().trim() == ''){
			alert_toast("Vui lòng nhập đầy đủ họ tên và số điện thoại",'danger');
			return false;
		}

		start_load();
		$.ajax({
			url:'admin/ajax.php?action=save_book',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Đặt phòng thành công! Chúng tôi sẽ liên hệ sớm nhất.",'success');
					setTimeout(function(){
						end_load();
						$('.modal').modal('hide');
						// Có thể chuyển hướng về trang cảm ơn
							// location.href = 'thank_you.php';
					},2000);
				} else {
					alert_toast("Có lỗi xảy ra, vui lòng thử lại!",'danger');
					end_load();
				}
			}
		});
	});
</script>
