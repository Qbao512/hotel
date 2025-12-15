<?php 
include('db_connect.php');

// SỬA LỖI: dùng null coalescing operator để tránh warning trên PHP 8+
$rid = $_GET['rid'] ?? '';

// Trường hợp sửa từ đặt phòng chờ nhận (có id nhưng chưa có phòng)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $qry = $conn->query("SELECT * FROM checked WHERE id = " . $id);
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            $meta[$k] = $v;
        }
    }
    // Tính số ngày ở
    $calc_days = abs(strtotime($meta['date_out']) - strtotime($meta['date_in']));
    $calc_days = floor($calc_days / (60 * 60 * 24));

    // Load danh sách loại phòng để hiển thị tên
    $cat = $conn->query("SELECT * FROM room_categories");
    $cat_arr = array();
    while ($row = $cat->fetch_assoc()) {
        $cat_arr[$row['id']] = $row;
    }
}
?>
<div class="container-fluid">
    <form action="" id="manage-check">
        <input type="hidden" name="id" value="<?php echo $meta['id'] ?? '' ?>">

        <?php if (isset($_GET['id'])): 
            // Khi sửa từ đặt chờ → hiển thị dropdown chọn phòng trống
            $rooms = $conn->query("SELECT r.*, rc.name as cat_name 
                                   FROM rooms r 
                                   LEFT JOIN room_categories rc ON rc.id = r.category_id 
                                   WHERE r.status = 0 OR r.id = '$rid' 
                                   ORDER BY r.room ASC");
        ?>
        <div class="form-group">
            <label class="control-label text-primary font-weight-bold">Chọn số phòng trống</label>
            <select name="rid" id="rid" class="custom-select browser-default" required>
                <option value="">-- Chọn phòng --</option>
                <?php while ($row = $rooms->fetch_assoc()): ?>
                <option value="<?php echo $row['id'] ?>" <?php echo ($row['id'] == $rid) ? 'selected' : '' ?>>
                    <?php echo $row['room'] ?> | <?php echo $row['cat_name'] ?>
                    <?php echo ($row['status'] == 1) ? ' (Đã có khách)' : ' (Trống)' ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php else: ?>
        <input type="hidden" name="rid" value="<?php echo $rid ?>">
        <?php endif; ?>

        <div class="form-group">
            <label class="control-label">Họ và tên khách</label>
            <input type="text" name="name" class="form-control" value="<?php echo $meta['name'] ?? '' ?>" required placeholder="Nguyễn Văn A">
        </div>

        <div class="form-group">
            <label class="control-label">Số điện thoại</label>
            <input type="text" name="contact" class="form-control" value="<?php echo $meta['contact_no'] ?? '' ?>" required placeholder="0901234567">
        </div>

        <div class="form-group">
            <label class="control-label">Ngày nhận phòng</label>
            <input type="date" name="date_in" class="form-control" 
                   value="<?php echo isset($meta['date_in']) ? date("Y-m-d", strtotime($meta['date_in'])) : date("Y-m-d") ?>" required>
        </div>

        <div class="form-group">
            <label class="control-label">Giờ nhận phòng</label>
            <input type="time" name="date_in_time" class="form-control" 
                   value="<?php echo isset($meta['date_in']) ? date("H:i", strtotime($meta['date_in'])) : date("H:i") ?>" required>
        </div>

        <div class="form-group">
            <label class="control-label">Số ngày ở</label>
            <input type="number" min="1" name="days" class="form-control" 
                   value="<?php echo $calc_days ?? 1 ?>" required>
            <small class="text-muted">Ngày trả dự kiến: <b><?php echo isset($meta['date_out']) ? date("d/m/Y", strtotime($meta['date_out'])) : '' ?></b></small>
        </div>

        <hr>
        <div class="text-center">
            <button class="btn btn-success btn-lg px-5">
                XÁC NHẬN NHẬN PHÒNG
            </button>
        </div>
    </form>
</div>

<script>
    $('#manage-check').submit(function(e) {
        e.preventDefault();
        start_load();

        $.ajax({
            url: 'ajax.php?action=save_check_in',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if (resp > 0) {
                    alert_toast("Nhận phòng thành công! Mã đặt phòng: <b>" + resp + "</b>", 'success');
                    setTimeout(function() {
                        uni_modal("Chi tiết khách & Trả phòng", "manage_check_out.php?id=" + resp);
                        end_load();
                    }, 1800);
                } else {
                    alert_toast("Lỗi khi nhận phòng, vui lòng thử lại!", 'danger');
                    end_load();
                }
            }
        });
    });
</script>