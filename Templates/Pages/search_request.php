<?php
require_once '../../Core/db.php'; // اتصال قاعدة البيانات
$city = $_GET['city'] ?? '';
$blood_type = $_GET['blood_type'] ?? '';
$status = $_GET['status'] ?? '';

$query = "SELECT * FROM blood_requests WHERE 1=1 ";
$params = [];
$types = "";

if (!empty($city)) {
    $query .= " AND city LIKE ?";
    $params[] = "%$city%";
    $types .= "s";
}

if (!empty($blood_type)) {
    $query .= " AND blood_type = ?";
    $params[] = $blood_type;
    $types .= "s";
}

if (!empty($status)) {
    $query .= " AND urgency = ?";
    $params[] = $status;
    $types .= "s";
}

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <title>البحث عن طلبات الدم</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../Static/css/search_request.css">
    </head>

    <body>
        <?php include "../../public/header.php"?>
        <div class="container py-5">
            <h2 class="mb-4 text-center text-danger">البحث عن طلبات التبرع بالدم</h2>

            <div class="search-box">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">المدينة</label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($city) ?>"
                                placeholder="مثل: الخرطوم">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">فصيلة الدم</label>
                            <select name="blood_type" class="form-select">
                                <option value="">اختر</option>
                                <?php
                        $bloodTypes = ["+A", "+B", "+O", "+AB", "-A", "-B", "-O", "-AB"];
                        foreach ($bloodTypes as $type) {
                            $selected = $blood_type == $type ? "selected" : "";
                            echo "<option value=\"$type\" $selected>$type</option>";
                        }
                        ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">الكل</option>
                                <option value="طارئة" <?= $status == "طارئة" ? "selected" : "" ?>>طارئة</option>
                                <option value="عادية" <?= $status == "عادية" ? "selected" : "" ?>>عادية</option>
                            </select>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-danger px-5">🔍 بحث</button>
                        </div>
                    </div>
                </form>
            </div>

            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="result-card mt-4 p-3 border rounded shadow-sm">
                <h5><?= htmlspecialchars($row['city']) ?> - <?= htmlspecialchars($row['hospital_name']) ?></h5>
                <p><strong>فصيلة الدم:</strong> <?= htmlspecialchars($row['blood_type']) ?></p>
                <p><strong>عدد الأكياس:</strong> <?= htmlspecialchars($row['bags']) ?></p>
                <p><strong>الحالة:</strong>
                    <span class="<?= $row['urgency'] === 'طارئة' ? 'text-danger fw-bold' : '' ?>">
                        <?= htmlspecialchars($row['urgency']) ?>
                    </span>
                </p>
                <a href="details_request.php?id=<?= $row['id'] ?>" class="btn btn-details">عرض التفاصيل</a>
            </div>
            <?php endwhile; ?>

        </div>
    </body>

</html>
