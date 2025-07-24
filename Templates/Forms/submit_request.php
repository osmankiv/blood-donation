<?php
session_start();
require_once '../../Core/db.php'; 


$hospital_name  = trim($_POST['hospital_name']);
$city           = trim($_POST['city']);
$blood_type     = $_POST['blood_type'];
$bags           = (int) $_POST['bags'];
$contact_number = trim($_POST['contact_number']);
$notes          = trim($_POST['notes']);
$notes          = trim($_POST['notes']);
$urgency        = $_POST['urgency'] ?? 'عادية'; // افتراضيًا 'عادية' إذا لم يتم تحديد


$errors = [];

// التحقق من القيم المدخلة
if (empty($hospital_name) || empty($city) || empty($blood_type) || empty($bags) || empty($contact_number)) {
    $errors[] = "جميع الحقول الأساسية مطلوبة.";
}

if ($bags <= 0) {
    $errors[] = "عدد الأكياس يجب أن يكون رقمًا موجبًا.";
}

if (!preg_match('/^0\d{8}$/', $contact_number)) {
    $errors[] = "رقم التواصل غير صحيح. يجب أن يبدأ بـ 0 ويتكون من 10 أرقام.";
}

if (empty($errors)) {
   $sql = "INSERT INTO blood_requests (hospital_name, city, blood_type, bags, contact_number, notes, urgency)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssisss", $hospital_name, $city, $blood_type, $bags, $contact_number, $notes, $urgency);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "تم إرسال طلب التبرع بنجاح!";
        header("Location: request_form.html?success=1");
        exit;
    } else {
        $errors[] = "حدث خطأ أثناء إرسال الطلب: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

// عرض الأخطاء إن وجدت
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
    echo '<p><a href="request_form.php">العودة إلى نموذج الطلب</a></p>';
}
?>
