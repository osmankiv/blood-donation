<?php
session_start();
require_once '../../Core/db.php';

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// التحقق من إرسال البيانات بطريقة POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // جلب البيانات من النموذج وتأكيدها
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $city = trim($_POST['city']);
    $blood_type = trim($_POST['blood_type']);

    // التحقق من القيم قبل التحديث (يمكنك تحسين التحقق)
    if (!empty($name) && !empty($phone) && !empty($city) && !empty($blood_type)) {
        $sql = "UPDATE users SET name = ?, phone = ?, city = ?, blood_type = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $phone, $city, $blood_type, $user_id);

        if ($stmt->execute()) {
            // تم التحديث بنجاح
            header("Location: profile.php?updated=1");
            exit;
        } else {
            echo "حدث خطأ أثناء التحديث: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "يرجى ملء جميع الحقول المطلوبة.";
    }
} else {
    echo "طلب غير صالح.";
}
?>
