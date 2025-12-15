<?php 
include 'db_connect.php';
$folder_parent = isset($_GET['fid'])? $_GET['fid'] : 0;
$folders = $conn->query("SELECT * FROM folders where parent_id = $folder_parent and user_id = '".$_SESSION['login_id']."'  order by name asc");
$files = $conn->query("SELECT * FROM files where folder_id = $folder_parent and user_id = '".$_SESSION['login_id']."'  order by name asc");
?>
<style>
	.folder-item{
		cursor: pointer;
	}
	.folder-item:hover{
		background: #eaeaea;
	    color: black;
	    box-shadow: 3px 3px #0000000f;
	}
	.custom-menu {
        z-index: 1000;
	    position: absolute;
	    background-color: #ffffff;
	    border: 1px solid #0000001c;
	    border-radius: 5px;
	    padding: 8px;
	    min-width: 13vw;
	}
	a.custom-menu-list {
	    width: 100%;
	    display: flex;
	    color: #4c4b4b;
	    font-weight: 600;
	    font-size: 1em;
	    padding: 1px 11px;
	}
	.file-item{
		cursor: pointer;
	}
	a.custom-menu-list:hover,.file-item:hover,.file-item.active {
	    background: #80808024;
	}
	a.custom-menu-list span.icon{
		width:1em;
		margin-right: 5px
	}
</style>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="card col-lg-12">
				<div class="card-body" id="paths">
					<?php 
					$id = $folder_parent;
					while($id > 0){
						$path = $conn->query("SELECT * FROM folders where id = $id")->fetch_array();
						echo '<script>
							$("#paths").prepend("<a href=\"index.php?page=files&fid='.$path['id'].'\">'.$path['name'].'</a> / ")
						</script>';
						$id = $path['parent_id'];
					}
					echo '<script>
						$("#paths").prepend("<a href=\"index.php?page=files\">Thư mục gốc</a> / ")
					</script>';
					?>
				</div>
			</div>
		</div>

		<div class="row mt-3">
			<button class="btn btn-primary btn-sm" id="new_folder">Tạo thư mục mới</button>
			<button class="btn btn-primary btn-sm ml-4" id="new_file">Tải lên tập tin</button>
		</div>
		<hr>

		<div class="row">
			<div class="col-lg-12">
				<div class="col-md-4 input-group offset-4">
					<input type="text" class="form-control" id="search" placeholder="Tìm kiếm thư mục hoặc tập tin...">
					<div class="input-group-append">
						<span class="input-group-text"><i class="fa fa-search"></i></span>
					</div>
				</div>
			</div>
		</div>

		<div class="row mt-4">
			<div class="col-md-12"><h4><b>Thư mục</b></h4></div>
		</div>
		<hr>

		<div class="row">
			<?php while($row=$folders->fetch_assoc()): ?>
				<div class="card col-md-3 mt-2 ml-2 mr-2 mb-2 folder-item" data-id="<?php echo $row['id'] ?>">
					<div class="card-body">
						<large><span><i class="fa fa-folder"></i></span><b class="to_folder"> <?php echo $row['name'] ?></b></large>
					</div>
				</div>
			<?php endwhile; ?>
		</div>

		<hr>

		<div class="row">
			<div class="card col-md-12">
				<div class="card-body">
					<table width="100%">
						<tr>
							<th width="40%" class="">Tên tập tin</th>
							<th width="20%" class="">Ngày cập nhật</th>
							<th width="40%" class="">Mô tả</th>
						</tr>
						<?php while($row=$files->fetch_assoc()):
							$name = explode(' ||',$row['name']);
							$name = isset($name[1]) ? $name[0] ." (".$name[1].").".$row['file_type'] : $name[0] .".".$row['file_type'];
							$img_arr = array('png','jpg','jpeg','gif','psd','tif');
							$doc_arr =array('doc','docx');
							$pdf_arr =array('pdf','ps','eps','prn');
							$icon ='fa-file';
							if(in_array(strtolower($row['file_type']),$img_arr)) $icon ='fa-image';
							if(in_array(strtolower($row['file_type']),$doc_arr)) $icon ='fa-file-word';
							if(in_array(strtolower($row['file_type']),$pdf_arr)) $icon ='fa-file-pdf';
							if(in_array(strtolower($row['file_type']),['xlsx','xls','xlsm','xlsb','xltm','xlt','xla','xlr'])) $icon ='fa-file-excel';
							if(in_array(strtolower($row['file_type']),['zip','rar','tar'])) $icon ='fa-file-archive';
						?>
						<tr class='file-item' data-id="<?php echo $row['id'] ?>" data-name="<?php echo $name ?>">
							<td><large><span><i class="fa <?php echo $icon ?>"></i></span><b class="to_file"> <?php echo $name ?></b></large>
								<input type="text" class="rename_file" value="<?php echo $row['name'] ?>" data-id="<?php echo $row['id'] ?>" data-type="<?php echo $row['file_type'] ?>" style="display: none">
							</td>
							<td><i class="to_file"><?php echo date('d/m/Y H:i',strtotime($row['date_updated'])) ?></i></td>
							<td><i class="to_file"><?php echo $row['description'] ?></i></td>
						</tr>
						<?php endwhile; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Menu chuột phải cho thư mục -->
<div id="menu-folder-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option edit">Đổi tên</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete">Xóa thư mục</a>
</div>

<!-- Menu chuột phải cho tập tin -->
<div id="menu-file-clone" style="display: none;">
	<a href="javascript:void(0)" class="custom-menu-list file-option edit">Đổi tên</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option download">Tải xuống</a>
	<a href="javascript:void(0)" class="custom-menu-list file-option delete">Xóa tập tin</a>
</div>

<script>
	$('#new_folder').click(function(){
		uni_modal('Tạo thư mục mới','manage_folder.php?fid=<?php echo $folder_parent ?>')
	})
	$('#new_file').click(function(){
		uni_modal('Tải lên tập tin','manage_files.php?fid=<?php echo $folder_parent ?>')
	})

	$('.folder-item').dblclick(function(){
		location.href = 'index.php?page=files&fid='+$(this).attr('data-id')
	})

	// Menu chuột phải - Thư mục
	$('.folder-item').bind("contextmenu", function(event) { 
	    event.preventDefault();
	    $("div.custom-menu").hide();
	    var custom = $("<div class='custom-menu'></div>")
	        custom.append($('#menu-folder-clone').html())
	        custom.find('.edit').attr('data-id',$(this).attr('data-id'))
	        custom.find('.delete').attr('data-id',$(this).attr('data-id'))
	    custom.appendTo("body")
		custom.css({top: event.pageY + "px", left: event.pageX + "px"});

		$("div.custom-menu .edit").click(function(e){
			e.preventDefault()
			uni_modal('Đổi tên thư mục','manage_folder.php?fid=<?php echo $folder_parent ?>&id='+$(this).attr('data-id'))
		})
		$("div.custom-menu .delete").click(function(e){
			e.preventDefault()
			_conf("Bạn có chắc chắn muốn xóa thư mục này không?",'delete_folder',[$(this).attr('data-id')])
		})
	})

	// Menu chuột phải - Tập tin
	$('.file-item').bind("contextmenu", function(event) { 
	    event.preventDefault();
	    $('.file-item').removeClass('active')
	    $(this).addClass('active')
	    $("div.custom-menu").hide();
	    var custom = $("<div class='custom-menu file'></div>")
	        custom.append($('#menu-file-clone').html())
	        custom.find('.edit').attr('data-id',$(this).attr('data-id'))
	        custom.find('.delete').attr('data-id',$(this).attr('data-id'))
	        custom.find('.download').attr('data-id',$(this).attr('data-id'))
	    custom.appendTo("body")
		custom.css({top: event.pageY + "px", left: event.pageX + "px"});

		$("div.file.custom-menu .edit").click(function(e){
			e.preventDefault()
			$('.rename_file[data-id="'+$(this).attr('data-id')+'"]').siblings('large').hide();
			$('.rename_file[data-id="'+$(this).attr('data-id')+'"]').show().focus();
		})
		$("div.file.custom-menu .delete").click(function(e){
			e.preventDefault()
			_conf("Bạn có chắc chắn muốn xóa tập tin này không?",'delete_file',[$(this).attr('data-id')])
		})
		$("div.file.custom-menu .download").click(function(e){
			e.preventDefault()
			window.open('download.php?id='+$(this).attr('data-id'))
		})
	})

	$('.rename_file').keypress(function(e){
		var _this = $(this)
		if(e.which == 13){
			start_load()
			$.ajax({
				url:'ajax.php?action=file_rename',
				method:'POST',
				data:{id:$(this).attr('data-id'),name:$(this).val(),type:$(this).attr('data-type'),folder_id:'<?php echo $folder_parent ?>'},
				success:function(resp){
					if(resp){
						resp = JSON.parse(resp);
						if(resp.status == 1){
							_this.siblings('large').find('b').html(resp.new_name);
							_this.hide()
							_this.siblings('large').show()
							end_load()
						}
					}
				}
			})
		}
	})

	$('.file-item').click(function(){
		if($(this).find('input.rename_file').is(':visible')) return false;
		uni_modal($(this).attr('data-name'),'manage_files.php?fid=<?php echo $folder_parent ?>&id='+$(this).attr('data-id'))
	})

	$(document).bind("click keyup", function(e) => {
	    $("div.custom-menu").hide();
	    $('.file-item').removeClass('active')
	});

	$('#search').keyup(function(){
		var _f = $(this).val().toLowerCase()
		$('.to_folder').each(function(){
			var val = $(this).text().toLowerCase()
			$(this).closest('.card').toggle(val.includes(_f))
		})
		$('.to_file').each(function(){
			var val = $(this).text().toLowerCase()
			$(this).closest('tr').toggle(val.includes(_f))
		})
	})

	function delete_folder($id){
		start_load();
		$.ajax({
			url:'ajax.php?action=delete_folder',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp == 1){
					alert_toast("Xóa thư mục thành công",'success')
					setTimeout(() => location.reload(), 1500)
				}
			}
		})
	}

	function delete_file($id){
		start_load();
		$.ajax({
			url:'ajax.php?action=delete_file',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp == 1){
					alert_toast("Xóa tập tin thành công",'success')
					setTimeout(() => location.reload(), 1500)
				}
			}
		})
	}
</script>