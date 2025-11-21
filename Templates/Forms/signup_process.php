<?php
session_start();
require_once '../../Core/db.php'; 

$name             = trim($_POST['name']);
$email            = trim($_POST['email']);
$phone            = trim($_POST['phone']);
$password         = $_POST['password'];
$confirm_password = $_POST['confirm_password'];


$errors = [];


if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
    $errors[] = "جميع الحقول مطلوبة.";
}

if (strlen($name) < 3 || strlen($name) > 50) {
    $errors[] = "الاسم يجب أن يكون بين 3 و 50 حرفًا.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "البريد الإلكتروني غير صالح.";
}


if (!preg_match('/^09\d{8}$/', $phone)) {
    $errors[] = "رقم الهاتف غير صحيح. يجب أن يبدأ بـ 09 ويتكون من 10 أرقام.";
}

if ($password !== $confirm_password) {
    $errors[] = "كلمتا المرور غير متطابقتين.";
}

if (empty($errors)) {
    $checkSql = "SELECT id FROM users WHERE email = ? OR phone = ? LIMIT 1";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "البريد الإلكتروني أو رقم الهاتف مستخدم من قبل.";
    }

    $stmt->close();
}


if (empty($errors)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertSql = "INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $name;

        $_SESSION['welcome_message'] = "مرحباً بك يا $name، تم إنشاء حسابك بنجاح!";

       
        header("Location: login.php?message= تم إنشاء حسابك بنجاح!");
        exit;
    } else {
        $errors[] = "حدث خطأ أثناء التسجيل: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();


if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
    echo '<p><a href="SignUp.html">العودة إلى صفحة التسجيل</a></p>';
}
?>
