<?php
session_start();
require_once '../../Core/db.php'; 

$identifier = trim($_POST['identifier']);
$password = $_POST['password'];

if (empty($identifier) || empty($password)) {
    die("يرجى ملء جميع الحقول.");
}

$sql = "SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();


    if (password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'] ?? 'User';

       
        header("Location:../Pages/profile.php?message=تم تسجيل الدخول بنجاح");
        exit;
    } else {
                header("Location:login.php?message=كلمة المرور غير صحيحة");

    }
} else {
                header("Location:login.php?message=لا يوجد حساب مطابق للمعرف المدخل");
  
}

$stmt->close();
$conn->close();
?>
