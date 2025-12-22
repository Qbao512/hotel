<?php include('db_connect.php'); ?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="container-fluid">
							<div class="col-md-12">
								<form id="filter">
									<div class="row align-items-end">
										<div class="col-md-4">
											<label class="control-label">Loại phòng</label>
											<select class="custom-select browser-default" name="category_id">
												<option value="all" <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == 'all') ? 'selected' : '' ?>>
													Tất cả loại phòng
												</option>
												<?php 
												$cat = $conn->query("SELECT * FROM room_categories order by name asc ");
												while($row = $cat->fetch_assoc()) {
													$cat_name[$row['id']] = $row['name'];
												?>
													<option value="<?php echo $row['id'] ?>" <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $row['id']) ? 'selected' : '' ?>>
														<?php echo $row['name'] ?>
													</option>
												<?php } ?>
											</select>
										</div> 
										<div class="col-md-2">
											<label for="" class="control-label">&nbsp;</label>
											<button class="btn btn-block btn-primary">Lọc</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

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
									<th class="text-center">Trạng thái</th>
									<th class="text-center">Thao tác</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$where = '';
								if(isset($_GET['category_id']) && !empty($_GET['category_id']) && $_GET['category_id'] != 'all'){
									$where .= " where category_id = '".$_GET['category_id']."' ";
								}
								if(empty($where))
									$where .= " where status = '0' ";
								else
									$where .= " and status = '0' ";

								$rooms = $conn->query("SELECT * FROM rooms ".$where." order by id asc");
								while($row = $rooms->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="text-center"><b><?php echo $cat_name[$row['category_id']] ?></b></td>
									<td class="text-center"><b><?php echo $row['room'] ?></b></td>
									<?php if($row['status'] == 0): ?>
										<td class="text-center"><span class="badge badge-success">Còn trống</span></td>
									<?php else: ?>
										<td class="text-center"><span class="badge badge-secondary">Đã đặt</span></td>
									<?php endif; ?>
									<td class="text-center">
										<button class="btn btn-sm btn-primary check_in" type="button" data-id="<?php echo $row['id'] ?>">
											Nhận phòng
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
			"lengthMenu": "Hiển thị _MENU_ dòng",
			"info": "Từ _START_ đến _END_ / Tổng _TOTAL_ phòng",
			"infoEmpty": "Không có dữ liệu",
			"zeroRecords": "Không tìm thấy phòng nào",
			"paginate": {
				"previous": "Trước",
				"next": "Tiếp"
			}
		}
	});

	$('.check_in').click(function(){
		uni_modal("Nhận phòng","manage_check_in.php?rid="+$(this).attr("data-id"))
	});

	$('#filter').submit(function(e){
		e.preventDefault();
		location.replace('index.php?page=check_in&category_id=' + $('[name="category_id"]').val());
	});
</script>
