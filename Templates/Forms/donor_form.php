<?php 
include_once "../../Core/db.php";

// رسالة حالة التسجيل
$status_message = "";
$status_class = ""; // لتحديد لون الرسالة (نجاح أو خطأ)

// 2. التحقق مما إذا تم إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_donor'])) {
    
    // 3. جمع البيانات من النموذج وتصفيتها
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $blood_type = htmlspecialchars(trim($_POST['blood_type']));
    $city = htmlspecialchars(trim($_POST['city']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));

    // التحقق من أن جميع الحقول المطلوبة غير فارغة
    if (!empty($full_name) && !empty($blood_type) && !empty($city) && !empty($phone_number)) {
        
        
        // التحقق من فشل الاتصال
        if ($conn->connect_error) {
            die("فشل الاتصال: " . $conn->connect_error);
        }

        // 5. إعداد استعلام الإدخال باستخدام العبارات المُعدَّة (Prepared Statements)
        $stmt = $conn->prepare("INSERT INTO donors (full_name, blood_type, city, phone_number) VALUES (?, ?, ?, ?)");
        
        // ssss تعني أن جميع المعاملات الأربعة هي سلاسل نصية (strings)
        $stmt->bind_param("ssss", $full_name, $blood_type, $city, $phone_number);

        // 6. تنفيذ الاستعلام
        if ($stmt->execute()) {
            $status_message = "✅ تم تسجيلك كمتبرع بنجاح! شكراً لك.";
            $status_class = "success";
            
            // مسح قيم النموذج بعد التسجيل الناجح
            $_POST = array(); 
        } else {
            $status_message = "❌ حدث خطأ أثناء التسجيل: " . $stmt->error;
            $status_class = "error";
        }
        
        // 7. إغلاق العبارة والاتصال
        $stmt->close();
        $conn->close();
        
    } else {
        $status_message = "يرجى ملء جميع الحقول المطلوبة.";
        $status_class = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>التسجيل كمتبرع</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #d9534f;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* لضمان أن العرض يشمل الحشوات */
        }

        button[type="submit"] {
            background-color: #d9534f;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #c9302c;
        }

        /* تنسيق رسائل الحالة */
        .status-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<?php  include "../../public/header.php";?>

<body>

    <div class="form-container">
        <h2>سجل كمتبرع</h2>
        
        <?php 
        
        if (!empty($status_message)): ?>

            <p class="status-message <?php echo $status_class; ?>">
                <?php echo $status_message; ?>
            </p>
        <?php endif; ?>
        
        <form method="post" action="">
            <label for="full_name">الاسم الكامل</label>
            <input type="text" id="full_name" name="full_name" required 
                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">

            <label for="blood_type">فصيلة الدم</label>
            <select id="blood_type" name="blood_type" required>
                <option value="">اختر</option>
                <?php
                $blood_types = ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"];
                $selected_blood_type = isset($_POST['blood_type']) ? $_POST['blood_type'] : '';
                foreach ($blood_types as $type) {
                    // التحقق مما إذا كانت القيمة هي القيمة التي أدخلها المستخدم مسبقاً
                    $selected = ($type == $selected_blood_type) ? 'selected' : '';
                    echo "<option value=\"$type\" $selected>$type</option>";
                }
                ?>
            </select>

            <label for="city">المدينة</label>
            <input type="text" id="city" name="city" required
                   value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">

            <label for="phone_number">رقم الهاتف</label>
            <input type="tel" id="phone_number" name="phone_number" required
                   value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">

            <button type="submit" name="register_donor">التسجيل كمتبرع</button>
        </form>
    </div>

</body>

</html>