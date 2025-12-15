<?php include('db_connect.php'); 
$cat = $conn->query("SELECT * FROM room_categories");
$cat_arr = array();
while($row = $cat->fetch_assoc()){
	$cat_arr[$row['id']] = $row;
}
$room = $conn->query("SELECT * FROM rooms");
$room_arr = array();
while($row = $room->fetch_assoc()){
	$room_arr[$row['id']] = $row;
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row mt-3">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Loại phòng</th>
									<th class="text-center">Mã đặt phòng</th>
									<th class="text-center">Trạng thái</th>
									<th class="text-center">Thao tác</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$checked = $conn->query("SELECT * FROM checked WHERE status = 0 ORDER BY id DESC");
								while($row = $checked->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><b><?php echo $i++ ?></b></td>
									<td class="text-center"><b><?php echo $cat_arr[$row['booked_cid']]['name'] ?></b></td>
									<td class="text-center"><b><?php echo $row['ref_no'] ?></b></td>
									<td class="text-center">
										<span class="badge badge-warning">Đã đặt - Chờ nhận phòng</span>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary check_out" type="button" data-id="<?php echo $row['id'] ?>">
											Xem & Nhận phòng
										</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$('table').dataTable({
		"language": {
			"search": "Tìm kiếm:",
			"lengthMenu": "Hiển thị _MENU_ bản ghi",
			"info": "Từ _START_ đến _END_ / Tổng _TOTAL_ đặt phòng",
			"infoEmpty": "Không có dữ liệu",
			"zeroRecords": "Không tìm thấy đặt phòng nào",
			"paginate": {
				"previous": "Trước",
				"next": "Tiếp"
			}
		}
	});

	$('.check_out').click(function(){
		uni_modal("Chi tiết đặt phòng & Nhận phòng", "manage_check_in.php?id=" + $(this).attr("data-id"))
	});
</script>