<?php
session_start();
require_once '../../Core/db.php';

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// التحقق من إرسال البيانات بطريقة POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // جلب البيانات من النموذج وتأكيدها
    $new_password = trim($_POST['new_password']);
    
    if (!empty($new_password)) { // ← تم تصحيح القوس هنا
        // من الأفضل تشفير كلمة المرور
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);

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