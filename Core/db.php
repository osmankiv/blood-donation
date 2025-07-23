<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$database = "blood-donation";

// إنشاء اتصال آمن بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $database);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// ضبط ترميز الاتصال إلى UTF-8
$conn->set_charset("utf8");
?>