<?php
// الاتصال بقاعدة البيانات
require_once '../../Core/db.php';

// التحقق من وجود id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('رقم الطلب غير صالح');
}

$request_id = intval($_GET['id']);

// جلب البيانات من قاعدة البيانات
$stmt = $conn->prepare("SELECT * FROM blood_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('طلب التبرع غير موجود');
}

$request = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تفاصيل طلب الدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../Static/css/details_request.css">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        h2 {
            color: #d62828;
            text-align: center;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }

        .label {
            font-weight: bold;
            color: #444;
        }

        .value {
            color: #555;
        }

        .btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            color: white;
        }

        .btn-yes {
            background-color: #28a745;
        }

        .btn-no {
            background-color: #dc3545;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .confirmation {
            display: none;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2><i class="fas fa-droplet"></i> تفاصيل طلب التبرع بالدم</h2>

        <div class="info-row">
            <div class="label">المدينة:</div>
            <div class="value"><i class="fas fa-city"></i> <?= htmlspecialchars($request['city']) ?></div>
        </div>

        <div class="info-row">
            <div class="label">اسم المستشفى:</div>
            <div class="value"><i class="fas fa-hospital"></i> <?= htmlspecialchars($request['hospital_name']) ?></div>
        </div>

        <div class="info-row">
            <div class="label">فصيلة الدم المطلوبة:</div>
            <div class="value"><i class="fas fa-tint"></i> <?= htmlspecialchars($request['blood_type']) ?></div>
        </div>

        <div class="info-row">
            <div class="label">عدد الأكياس المطلوبة:</div>
            <div class="value"><i class="fas fa-boxes-stacked"></i> <?= (int)$request['bags'] ?></div>
        </div>

        <div class="info-row">
            <div class="label">الحالة:</div>
            <div class="value" style="color: #d62828; font-weight: bold;">
                <i class="fas fa-triangle-exclamation"></i> <?= htmlspecialchars($request['urgency']) ?>
            </div>
        </div>

        <div class="info-row">
            <div class="label">رقم للتواصل:</div>
            <div class="value"><i class="fas fa-phone"></i> <?= htmlspecialchars($request['contact_number']) ?></div>
        </div>

        <div class="info-row">
            <div class="label">ملاحظات إضافية:</div>
            <div class="value"><?= nl2br(htmlspecialchars($request['notes'])) ?></div>
        </div>

        <div class="btns">
            <button class="btn btn-yes" onclick="confirmDonation()"><i class="fas fa-hand-holding-medical me-1"></i> سأتبرع</button>
            <button class="btn btn-no" onclick="alert('شكرًا لك! نتمنى مشاركتك في المستقبل.')"><i class="fas fa-times-circle me-1"></i> لا أستطيع الآن</button>
        </div>

        <div class="confirmation" id="confirmationMessage">
            ✅ تم تسجيل استعدادك للتبرع. شكرًا لإنسانيتك ❤️
        </div>
    </div>

    <script>
        function confirmDonation() {
            document.getElementById('confirmationMessage').style.display = 'block';
        }
    </script>

</body>

</html>
