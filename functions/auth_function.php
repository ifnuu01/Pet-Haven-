<?php

function registrasi($conn, $username, $nama_depan, $nama_belakang, $email, $password)
{

    if (empty($username) || empty($nama_depan) || empty($nama_belakang) || empty($email) || empty($password)) {
        return [
            "status" => false,
            "message" => "Data tidak boleh kosong"
        ];
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            "status" => false,
            "message" => "Format email tidak valid"
        ];
    }


    $query = "SELECT * FROM pengguna WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return [
            "status" => false,
            "message" => "Username ini sudah terdaftar. Masukkan username yang berbeda"
        ];
    }


    $query = "SELECT * FROM pengguna WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return [
            "status" => false,
            "message" => "Email ini sudah terdaftar. Masukkan email yang berbeda"
        ];
    }


    if (strlen($password) < 8) {
        return [
            "status" => false,
            "message" => "Password minimal 8 karakter"
        ];
    }


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $conn->begin_transaction();

    try {
    
        $query = "INSERT INTO pengguna (username, nama_depan, nama_belakang, email, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $username, $nama_depan, $nama_belakang, $email, $hashed_password);
        $stmt->execute();

    
        $id_pengguna = $conn->insert_id;

    
        $query_alamat = "INSERT INTO alamat (id_pengguna) VALUES (?)";
        $stmt_alamat = $conn->prepare($query_alamat);
        $stmt_alamat->bind_param("i", $id_pengguna);
        $stmt_alamat->execute();

    
        $conn->commit();

        return [
            "status" => true,
            "message" => "Registrasi berhasil"
        ];
    } catch (Exception $e) {
    
        $conn->rollback();

        return [
            "status" => false,
            "message" => "Registrasi gagal: " . $e->getMessage()
        ];
    }
}



function login($conn, $username, $password)
{
    if (empty($username) || empty($password))
    {
        return [
            "status" => false,
            "message" => "Data tidak boleh kosong"
        ];
        exit();
    }

    $query = "SELECT * FROM pengguna WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row)
    {
        return[
            "status" => false,
            "message" => "Username tidak ditemukan. Pastikan username anda benar"
        ];
        exit();
    }

    if ($row['status'] == "Tidak Aktif")
    {
        return [
            "status" => false,
            "message" => "Akun anda diblokir. Anda terdeteksi melakukan pelanggaran"
        ];
        exit();
    }

    if (password_verify($password, $row['password']))
    {
        $_SESSION['user'] = [
            "id" => $row['id'],
            "username" => $row['username'],
            "nama_depan" => $row['nama_depan'],
            "nama_belakang" => $row['nama_belakang'],
            "email" => $row['email'],
            "role" => $row['role'],
            "poto" => $row['path_poto']
        ];
        return [
            "status" => true,
            "message" => "Login berhasil"
        ];
        exit();
    }else{
        return [
            "status" => false,
            "message" => "Password salah. Silahkan ingat kembali"
        ];
        exit();
    }
}

function logout()
{
    session_destroy();
}

?>