<?php
session_start();

require_once 'functions/connection.php';
require_once 'functions/auth_function.php';
require_once 'functions/admin_function.php';

if (isset($_POST['logout'])) {
    logout();   
    header('location: login');
    exit();
}

if (isset($_SESSION['user']['role'])){
    if ($_SESSION['user']['role'] == 'Admin') {
        echo "<script>location.href='dashboard';</script>";
    }
}

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/modal-confirm.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/alert.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    <title>
        <?php
        if ($url === '/') {
            echo 'Home';
        } elseif ($url === '/profile') {
            echo 'Profile';
        } elseif ($url === '/riwayat-pembelian') {
            echo 'Riwayat Pembelian';
        } elseif ($url === '/notifikasi') {
            echo 'Notifikasi';
        } elseif ($url === '/alamat') {
            echo 'Alamat';
        } elseif ($url === '/detail-hewan') {
            echo 'Detail Hewan';
        } elseif ($url === '/pembayaran') {
            echo 'Pembayaran';
        } elseif ($url === '/konfirmasi_pembayaran') {
            echo 'Konfirmasi Pembayaran';
        } elseif ($url == '/payment-success'){
            echo 'Pembayaran Berhasil';
        }
        ?>
    </title>
</head>
<body>
<dialog id="confirmModal">
    <div class="modal-content">
        <p id="modalMessage"></p>
        <div class="btn-modal">
            <button class="cancel" id="cancelBtn"></button>
            <button class="confirm" id="confirmBtn"></button>
        </div>
    </div>
</dialog>
    <nav class="navbar-user">
        <div class="menu-nav1">
            <a href="/"><img src="assets/logo/logo.png" alt="" width="50px"></a>
            <a href="/"><iconify-icon icon="line-md:home"></iconify-icon></a>
            <span id="button-kategori">Kategori</span>
        </div>
        <div class="search">
            <form action="/" method="GET">
                <input type="text" name="search" placeholder="Cari hewan">
            </form>
        </div>
        <div class="menu-nav">
        <?php
                if (isset($_SESSION['user'])){ ?>
                <div class="profile">
                    <span><?= $_SESSION['user']['username'] ?></span>
                    <div class="img-profile"> 
                        <img src="<?= $_SESSION['user']['poto'] ?>" alt=""> 
                    </div>
                </div>
                <?php
                }
                else{ ?>
                    <div class="menu-auth">
                        <a href="login">Masuk</a>
                        <div class="line"></div>
                        <a href="register">Daftar</a>
                    </div>
            <?php } ?>
        </div>
    </nav>
    <?php
    if (isset($_SESSION['user'])){
    ?>
    <div class="profile-dropdown">
        <div class="profile-content">
            <a href="/profile">
                <iconify-icon icon="gg:profile"></iconify-icon>
                <span>Profile</span>
            </a>
            <a href="/alamat">
                <iconify-icon icon="bx:bxs-map"></iconify-icon>
                <span>Alamat</span>
            </a>
            <a href="/riwayat-pembelian">
                <iconify-icon icon="bx:bxs-cart"></iconify-icon>
                <span>Riwayat Pembelian</span>
            </a>
            <a href="/notifikasi">
                <iconify-icon icon="bx:bxs-notification"></iconify-icon>
                <span>Notifikasi</span>
            </a>
            <form action="/" method="post" id="logoutForm">
                <input type="hidden" name="logout" value="1"></input>
            </form>
            <button  class="actionBtn" 
            data-action="logout" 
            data-message="Apakah Anda yakin ingin keluar dari akun anda?" 
            data-form="logoutForm"
            data-cancel-text="Tidak"
            data-confirm-text="Ya">
                <iconify-icon icon="bxs:door-open"></iconify-icon>
                <span>Keluar</span>
            </button>
        </div>
    </div>
    <?php
    }?>
    <div class="container-kategori" id="container-kategori">
        <div class="dropdown-content">
            <a href="/?kategori=1"><iconify-icon icon="mdi:cat"></iconify-icon> Kucing</a>
            <a href="/?kategori=2"><iconify-icon icon="mdi:dog"></iconify-icon> Anjing</a>
            <a href="/?kategori=3"><iconify-icon icon="tdesign:fish-filled"></iconify-icon> Ikan Hias</a>
            <a href="/?kategori=4"><iconify-icon icon="mdi:bird"></iconify-icon> Burung</a>
            <a href="/?kategori=5"><iconify-icon icon="mdi:reptile"></iconify-icon> Reptil</a>
            <a href="/?kategori=6"><iconify-icon icon="emojione-monotone:hamster-face"></iconify-icon> Hamster</a>
            <a href="/?kategori=7"><iconify-icon icon="mdi:spider"></iconify-icon> Serangga</a>
        </div>
    </div>