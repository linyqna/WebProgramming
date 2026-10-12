<?php
session_start();
require __DIR__ . '/../includes/connection.php';

$name = trim($_POST['name'] ?? '');
$memberId = trim($_POST['member_id'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$errors = [];
if ($name === '') {
    $errors[] = "Name is required.";
}
if ($memberId === '') {
    $errors[] = "Member ID is required.";
}
if ($address === '') {
    $errors[] = "Address is required.";
}
if ($phone !== '' && !preg_match('/^[0-9]+$/', $phone)) {
    $errors[] = "Phone number must contain only numbers.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => implode(' ', $errors)];
    header('Location: add.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO members (name, member_id, address, phone)
     VALUES (:name, :member_id, :address, :phone)
     RETURNING id"
);

try {
    $stmt->execute([
        'name' => $name,
        'member_id' => $member_id,
        'address' => $address,
        'phone' => $phone
    ]);
    
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
    header('Location: list.php');
    exit;

} catch (PDOException $e) {
    if ($e->getCode() == '23505') {
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'No. Member sudah dipakai, gunakan nomor lain.'];
        header('Location: add.php');
        exit;
    } else {
        die("Terjadi kesalahan database: " . $e->getMessage());
    }
}

$_SESSION['flash'] = ['type' => 'success', 'message' => 'Member added successfully.'];
header('Location: list.php');
exit;
