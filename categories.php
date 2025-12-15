<?php include('db_connect.php');?>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
				<form action="" id="manage-category">
					<div class="card">
						<div class="card-header">
						    Form loại phòng
				  		</div>
						<div class="card-body">
							<input type="hidden" name="id">
							<div class="form-group">
								<label class="control-label">Tên loại phòng</label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="form-group">
								<label class="control-label">Giá mỗi ngày (₫)</label>
								<input type="number" class="form-control text-right" name="price" step="any" required>
							</div>
							<div class="form-group">
								<label for="" class="control-label">Ảnh đại diện</label>
								<input type="file" class="form-control" name="img" onchange="displayImg(this,$(this))">
							</div>
							<div class="form-group">
								<img src="" alt="" id="cimg" class="img-fluid">
							</div>
						</div>
						
						<div class="card-footer">
							<div class="row">
								<div class="col-md-12">
									<button class="btn btn-sm btn-primary col-sm-3 offset-md-2"> Lưu</button>
									<button class="btn btn-sm btn-default col-sm-3" type="button" onclick="$('#manage-category').get(0).reset()"> Hủy</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Ảnh</th>
									<th class="text-center">Thông tin loại phòng</th>
									<th class="text-center">Thao tác</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$cats = $conn->query("SELECT * FROM room_categories order by id asc");
								while($row = $cats->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="text-center">
										<img src="<?php echo !empty($row['cover_img']) ? '../assets/img/'.$row['cover_img'] : '../assets/img/no-image-available.png' ?>" alt="Ảnh loại phòng" class="cimg">
									</td>
									<td>
										<p><b>Tên:</b> <?php echo $row['name'] ?></p>
										<p><b>Giá:</b> <?php echo number_format($row['price'], 0, ',', '.') ?>₫ / ngày</p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_cat" type="button" 
											data-id="<?php echo $row['id'] ?>" 
											data-name="<?php echo $row['name'] ?>" 
											data-price="<?php echo $row['price'] ?>" 
											data-cover_img="<?php echo $row['cover_img'] ?>">
											Sửa
										</button>
										<button class="btn btn-sm btn-danger delete_cat" type="button" data-id="<?php echo $row['id'] ?>">
											Xóa
										</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	
</div>

<style>
	img#cimg, .cimg{
		max-height: 12vh;
		max-width: 8vw;
		object-fit: cover;
		border-radius: 8px;
	}
	td{
		vertical-align: middle !important;
	}
	td p {
		margin: unset;
	}
</style>

<script>
	function displayImg(input, _this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }
	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$('#manage-category').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_category',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp == 1){
					alert_toast("Thêm loại phòng thành công",'success')
					setTimeout(() => location.reload(), 1500)
				}
				else if(resp == 2){
					alert_toast("Cập nhật loại phòng thành công",'success')
					setTimeout(() => location.reload(), 1500)
				}
			}
		})
	})

	$('.edit_cat').click(function(){
		start_load()
		var cat = $('#manage-category')
		cat.get(0).reset()
		cat.find("[name='id']").val($(this).attr('data-id'))
		cat.find("[name='name']").val($(this).attr('data-name'))
		cat.find("[name='price']").val($(this).attr('data-price'))
		cat.find("#cimg").attr('src', '../assets/img/' + $(this).attr('data-cover_img'))
		end_load()
	})

	$('.delete_cat').click(function(){
		_conf("Bạn có chắc chắn muốn xóa loại phòng này không?","delete_cat",[$(this).attr('data-id')])
	})

	function delete_cat($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_category',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp == 1){
					alert_toast("Xóa loại phòng thành công",'success')
					setTimeout(() => location.reload(), 1500)
				}
			}
		})
	}
</script>