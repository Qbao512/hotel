<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('header.php');
include('admin/db_connect.php');

$query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
foreach ($query as $key => $value) {
    if (!is_numeric($key))
        $_SESSION['setting_' . $key] = $value;
}
?>
<style>
/* -------------------------------------
   HEADER BACKGROUND
-------------------------------------- */
header.masthead {
    background: url(assets/img/<?php echo $_SESSION['setting_cover_img'] ?>);
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    padding-top: 220px;
    padding-bottom: 220px;
}

/* -------------------------------------
   LOADING OVERLAY MƯỢT
-------------------------------------- */
#loading-overlay {
    position: fixed;
    inset: 0;
    background: #ffffff;
    z-index: 999999999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.45s ease, visibility 0.45s ease;
}

#loading-overlay.hide {
    opacity: 0;
    visibility: hidden;
}

#loading-overlay .loader {
    width: 60px;
    height: 60px;
    border: 6px solid #ddd;
    border-top-color: #007bff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* -------------------------------------
   NAVBAR SCROLL
-------------------------------------- */
#mainNav.navbar-scrolled {
    background-color: #ffffff !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    transition: 0.3s ease;
}

html { scroll-behavior: smooth; }

/* -------------------------------------
   BOOKING FILTER CARD
-------------------------------------- */
#filter-book {
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(8px);
}

#filter-book input {
    height: 43px;
    border-radius: 6px;
}

/* -------------------------------------
   PORTFOLIO GRID - UPDATED
-------------------------------------- */
#portfolio .portfolio-box {
    position: relative;
    display: block;
    border-radius: 14px;                     /* Bo góc toàn khung */
    overflow: hidden;                        
    border: 3px solid rgba(255,255,255,0.45); /* Viền đẹp */
    transition: box-shadow .35s ease;
}

#portfolio .portfolio-box:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.25);  /* Bóng đổ sang trọng */
}

#portfolio img {
    height: 250px;              /* Thu nhỏ ảnh */
    width: 100%;
    object-fit: cover;
    border-radius: 14px;        /* Bo góc ảnh */
    transition: transform .45s ease;
}

#portfolio .portfolio-box:hover img {
    transform: scale(1.05);
}

/* -------------------------------------
   PORTFOLIO CAPTION
-------------------------------------- */
.portfolio-box-caption {
    position: absolute;
    bottom: 0;
    width: 100%;
    padding: 18px 22px;
    background: linear-gradient(to top, rgba(0,0,0,0.85), rgba(0,0,0,0));
    color: #fff;
    opacity: 0;
    transition: 0.45s ease;
    border-radius: 0 0 14px 14px;  /* Bo góc phần caption */
}

.portfolio-box:hover .portfolio-box-caption {
    opacity: 1;
}

.portfolio-box-caption .project-category {
    font-size: 16px;
    font-weight: 600;
    opacity: 0.9;
    transform: translateY(10px);
    transition: 0.3s ease;
}

.portfolio-box-caption .project-name {
    font-size: 22px;
    font-weight: 700;
    margin-top: 4px;
    opacity: 0.85;
    transform: translateY(10px);
    transition: 0.3s ease;
}

.portfolio-box:hover .project-category,
.portfolio-box:hover .project-name {
    transform: translateY(0);
    opacity: 1;
}
</style>



<body id="page-top">

<!-- LOADING OVERLAY -->
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<!-- Navigation-->
<div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white"></div>
</div>

<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="./">
            <?php echo $_SESSION['setting_hotel_name'] ?>
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto my-2 my-lg-0">
                <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=home">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=list">Phòng</a></li>
                <li class="nav-item"><a class="nav-link js-scroll-trigger" href="index.php?page=about">Giới thiệu</a></li>
            </ul>
        </div>
    </div>
</nav>

<?php
$page = isset($_GET['page']) ? $_GET['page'] : "home";
include $page . '.php';
?>

<!-- Confirm Modal -->
<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận</h5>
            </div>
            <div class="modal-body">
                <div id="delete_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='confirm' onclick="">Tiếp tục</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Uni Modal -->
<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit'
                        onclick="$('#uni_modal form').submit()">Lưu</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-light py-5">
    <div class="container">
        <div class="small text-center text-muted">
            Bản quyền © 2025 - Hệ thống quản lý khách sạn ALT F4
        </div>
    </div>
</footer>

<?php include('footer.php') ?>

<!-- JS LOADING TRANSITION -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const overlay = document.getElementById("loading-overlay");

    // Tắt overlay khi trang load xong
    window.addEventListener("load", function () {
        setTimeout(() => overlay.classList.add("hide"), 200);
    });

    // Bật overlay khi chuyển trang
    document.querySelectorAll("a:not([target='_blank']):not([href^='#'])")
        .forEach(link => {
            link.addEventListener("click", function (e) {
                if (!this.href) return;

                e.preventDefault();
                overlay.classList.remove("hide");

                setTimeout(() => {
                    window.location = this.href;
                }, 300);
            });
        });
});
</script>

<?php $conn->close() ?>
</body>
</html>
