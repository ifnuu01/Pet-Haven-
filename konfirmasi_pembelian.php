<?php
include 'template-admin/header.php';


$limit_per_halaman = 5;
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit_bawah = ($halaman - 1) * $limit_per_halaman;

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $nama_pengguna = $_GET['search'];
    $data = search_konfirmasi_pembelian($conn, $nama_pengguna, $limit_bawah, $limit_per_halaman);
    $total_data_search = $conn->query("SELECT COUNT(p.nama_depan) as total FROM transaksi t JOIN pengguna p ON t.id_pengguna = p.id WHERE p.nama_depan LIKE '%$nama_pengguna%' AND t.status = 'Menunggu'");
    $total_data = $total_data_search->fetch_assoc()['total'];
    $total_halaman = ceil($total_data / $limit_per_halaman);
} else {
    $data = get_konfirmasi_pembelian($conn, $limit_bawah, $limit_per_halaman);
    $total_data_query = $conn->query("SELECT COUNT(*) as total FROM transaksi t JOIN pengguna p ON t.id_pengguna = p.id WHERE t.status = 'Menunggu'");
    $total_data = $total_data_query->fetch_assoc()['total'];
    $total_halaman = ceil($total_data / $limit_per_halaman);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $no = $_POST['nomor_pembelian'];
    $status = konfirmasi_pembelian($conn, $no, $status);
    if($status['status']) {
        echo "<script>alertModal('konfirmasi_pembelian', '{$status['message']}', 'Lanjut', 'assets/logo/centang.png');</script>";
    }else{
        echo "<script>alertModal('konfirmasi_pembelian', '{$status['message']}', 'Lanjut', 'assets/logo/cancel.png');</script>";
    }
}

?>

<div class="container">
    <div class="search-container">
        <form action="" method="GET">
            <input type="text" name="search" class="search-input" placeholder="Cari Pengguna" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" hidden></button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No. Pembelian</th>
                    <th>Nama Pengguna</th>
                    <th>Foto User</th>
                    <th>Total Pembelian</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($data['status'] && count($data['data']) > 0): ?>
                <?php foreach ($data['data'] as $konfirmasi): ?>
                    <tr>
                        <td class="align-center"><?= htmlspecialchars($konfirmasi->no_pembelian); ?></td>
                        <td class="align-center"><?= htmlspecialchars($konfirmasi->nama_depan); ?></td>
                        <td><img src="<?= htmlspecialchars($konfirmasi->poto_pengguna); ?>" alt="Foto User" width="50"></td>
                        <td class="align-center">Rp <?= htmlspecialchars(number_format($konfirmasi->total_pembelian, 2)); ?></td>
                        <td class="align-center"><?= htmlspecialchars($konfirmasi->waktu_pembayaran); ?></td>
                        <td class="align-center"><button onclick="openDetailKonfirmasi(<?= htmlspecialchars(json_encode($konfirmasi), ENT_QUOTES, 'UTF-8') ?>)" class="detail-btn" >Detail</button></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">Data konfirmasi pembelian tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php if ($halaman > 1): ?>
            <a class="button" href="?page=<?php echo $halaman - 1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>">Sebelumnya</a>
        <?php else: ?>
            <span class="button red" hidden>Sebelumnya</span>
        <?php endif; ?>

        <!-- Nomor Halaman -->
        <?php
        $start_page = max(1, $halaman - 2);
        $end_page = min($total_halaman, $halaman + 2);
        if ($end_page - $start_page < 4) {
            $end_page = min($total_halaman, $start_page + 4);
        }
        if ($end_page - $start_page < 4) {
            $start_page = max(1, $end_page - 4);
        }
        ?>

        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <?php if ($i == $halaman): ?>
                <strong class="nomor"><?php echo $i; ?></strong>
            <?php else: ?>
                <a class="nomor" href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($halaman < $total_halaman): ?>
            <a class="button" href="?page=<?php echo $halaman + 1; ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?>">Selanjutnya</a>
        <?php else: ?>
            <span class="button" hidden>Selanjutnya</span>
        <?php endif; ?>
    </div>
</div>



<dialog class="container-modal-pembayaran" id="modal-pembayaran">
        <div class="container-pembayaran" id="container-pembayaran">
            <div class="header" id="header-pembayaran">
                <h1>Detail Pembayaran</h1>
                <button class="status oren" id="status-konfirmasi">Menunggu</button>
            </div>
            <div class="card" id="card-pembayaran">
                <div class="left" id="left-card">
                    <div class="transaction" id="transaction-image">
                        <img src="assets/img/pembayaran/pembayaran.png" alt="Pembayaran" width="297" height="578" id="pembayaran">
                    </div>
                </div>
                <div class="right" id="right-card">
                    <div class="top-info" id="top-info">
                        <img src="hamster.jpg" alt="Johnson" class="animal-img" width="221px" height="199px" id="animal-img">
                        <div class="info-text" id="info-text">
                            <h2 id="animal-name">Johnson</h2>
                            <p class="price" id="price">Rp25.000</p>
                            <p class="date" id="date">2024-09-11</p>
                        </div>
                    </div>
                    <ul id="payment-details">
                        <li><strong>Jenis Hewan</strong> <span id="animal-type">Hamster</span></li>
                        <li><strong>Tahapan Usia</strong> <span id="age-stage">Dewasa</span></li>
                        <li><strong>Warna</strong> <span id="color">Oren, Putih, Hitam</span></li>
                        <li><strong>Jenis Kelamin</strong> <span id="gender">Jantan</span></li>
                        <li><strong>Berat</strong> <span id="weight">5kg</span></li>
                        <li><strong>Pajak Admin</strong> <span id="admin-tax">Rp2.000</span></li>
                        <li><strong>Biaya Pengiriman</strong> <span id="shipping-fee">Rp20.000</span></li>
                        <li><strong>Total Pembayaran</strong> <span id="total-payment">Rp47.000</span></li>
                        <li><strong>Metode Pembayaran</strong> <span id="payment-method">Dana</span></li>
                        <li><strong>No Pembelian</strong> <span id="purchase-number">1234567891</span></li>
                    </ul>
                    <div class="buttons" id="buttons">
                        <button class="back confirm" id="back-button-pembayaran">Tolak</button>
                        <button class="konfirmasi-pembayaran" id="confirm-button-pembayaran">Terima</button>
                    </div>
                </div>
            </div>
        </div>
</dialog>

<form action="" method="POST" id="form-konfirmasi">
    <input type="text" hidden value="" name="status" id="status_konfirmasi">
    <input type="text" hidden value="" name="nomor_pembelian" id="nomor_pembelian">
</form>



<script src="assets/js/modal-konfirmasi-pembelian.js" ></script>

<?php

include 'template-admin/footer.php';
?>