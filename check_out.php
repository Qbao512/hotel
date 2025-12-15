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
									<th class="text-center">Số phòng</th>
									<th class="text-center">Mã đặt phòng</th>
									<th class="text-center">Trạng thái</th>
									<th class="text-center">Thao tác</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$checked = $conn->query("SELECT * FROM checked where status != 0 order by status asc, id desc ");
								while($row = $checked->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="text-center"><b><?php echo $cat_arr[$room_arr[$row['room_id']]['category_id']]['name'] ?></b></td>
									<td class=""><b><?php echo $room_arr[$row['room_id']]['room'] ?></b></td>
									<td class=""><b><?php echo $row['ref_no'] ?></b></td>
									<?php if($row['status'] == 1): ?>
										<td class="text-center"><span class="badge badge-warning">Đang ở</span></td>
									<?php else: ?>
										<td class="text-center"><span class="badge badge-success">Đã trả phòng</span></td>
									<?php endif; ?>
									<td class="text-center">
										<button class="btn btn-sm btn-primary check_out" type="button" data-id="<?php echo $row['id'] ?>">
											Xem chi tiết
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
			"search: "Tìm kiếm:",
			"lengthMenu": "Hiển thị _MENU_ dòng",
			"info": "Hiển thị _START_ đến _END_ của _TOTAL_ bản ghi",
			"infoEmpty": "Không có dữ liệu",
			"paginate": {
				"previous": "Trước",
				"next": "Tiếp"
			}
		}
	});

	$('.check_out').click(function(){
		uni_modal("Chi tiết khách & Trả phòng","manage_check_out.php?checkout=1&id="+$(this).attr("data-id"))
	})
</script>