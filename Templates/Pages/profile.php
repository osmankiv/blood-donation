<?php
session_start();
require_once '../../Core/db.php';

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Forms/login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// جلب بيانات المستخدم
$query = "SELECT name, phone, city, blood_type, last_donation_date, points FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $phone, $city, $blood_type, $lastDonation, $points);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

  <head>
    <meta charset="UTF-8">
    <title>الملف الشخصي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../../Static/css/profile.css">
  </head>

  <body>

    <div class="nav-links">
      <a href="../../home.html">🏠 الرئيسية</a>
      <a href="../Forms/request_form.html">➕ طلب دم</a>
      <a href="#" onclick="toggleDarkMode()">🌓 الوضع الليلي</a>
      <a href="../../public/logout.php">🚪 تسجيل الخروج</a>
    </div>

    <div class="container">
      <h2 class="text-center">👤 الملف الشخصي</h2>

      <?php if (isset($_GET['updated'])): ?>
      <div class="alert alert-success">✅ تم حفظ التعديلات بنجاح</div>
      <?php endif; ?>

      <div id="alertBox"></div>

      <div class="points-card">
        🏅 لديك <span style="font-size: 28px; color: #d62828;"><?= $points ?></span> نقطة
        <small>استمر في التبرع لربح المزيد!</small>
      </div>

      <div class="card">
        <h4>معلوماتي</h4>
        <form id="profileForm" action="update_profile.php" method="POST">
          <div class="row g-3">
            <div class="col-md-6">
              <label>الاسم</label>
              <input type="text" name="name" class="readonly-input" value="<?= htmlspecialchars($name) ?>" readonly>
            </div>
            <div class="col-md-6">
              <label>رقم الهاتف</label>
              <input type="text" name="phone" class="readonly-input" value="<?= htmlspecialchars($phone) ?>" >
            </div>
            <div class="col-md-6">
              <label>المدينة</label>
              <input type="text" name="city" class="readonly-input" value="<?=$city ? htmlspecialchars($city) : "غير محددة" ?>" >
            </div>
            <div class="col-md-6">
              <label>فصيلة الدم</label>

  <select name="blood_type" class="form-select readonly-input" aria-label="فصيلة الدم" onchange="this.previousElementSibling.value = this.value">
    <option value=""  <?= $blood_type ? '' : 'selected' ?>>اختر فصيلة الدم</option>
    <option value="+A" <?= $blood_type == '+A' ? 'selected' : '' ?>>+A</option>
    <option value="-A" <?= $blood_type == '-A' ? 'selected' : '' ?>>-A</option>
    <option value="+B" <?= $blood_type == '+B' ? 'selected' : '' ?>>+B</option>
    <option value="-B" <?= $blood_type == '-B' ? 'selected' : '' ?>>-B</option>
    <option value="+O" <?= $blood_type == '+O' ? 'selected' : '' ?>>+O</option>
    <option value="-O" <?= $blood_type == '-O' ? 'selected' : '' ?>>-O</option>
    <option value="+AB" <?= $blood_type == '+AB' ? 'selected' : '' ?>>+AB</option>
    <option value="-AB" <?= $blood_type == '-AB' ? 'selected' : '' ?>>-AB</option>
  </select>
</div>


            </div>
          </div>
          <button type="button" class="edit-btn" onclick="enableEditing()">✏️ تعديل المعلومات</button>
          <button type="submit" class="save-btn d-none">💾 حفظ التعديلات</button>
        </form>
      </div>

      <div class="card">
        <h4>⏳ الوقت المتبقي للتبرع القادم</h4>
        <p>آخر تبرع: <strong><?= $lastDonation ? $lastDonation : "غير محدد" ?></strong></p>
        <p>يمكنك التبرع مرة أخرى بعد:
          <span id="nextDonation" class="badge-time">جارٍ الحساب...</span>
        </p>
      </div>

      <div class="card">
        <h4>📋 سجل الطلبات</h4>
        <div class="info-row">
          <span>#4923 - مستشفى أحمد قاسم - بحري</span>
          <span class="text-success">تم التبرع</span>
        </div>
        <div class="info-row">
          <span>#4902 - مركز الهلال - أمدرمان</span>
          <span class="text-muted">ملغاة</span>
        </div>
      </div>

      <div class="card">
        <h4>💉 سجل التبرعات</h4>
        <ul>
          <li>تبرعت بتاريخ 10 مايو 2024 لمريض في مستشفى الشعب</li>
          <li>تبرعت بتاريخ 15 فبراير 2024 في مركز أمبدة</li>
        </ul>
      </div>

      <div class="card">
        <h4>📍 أقرب مركز تبرع</h4>
        <div class="map-box">
          <iframe
            src="https://maps.google.com/maps?q=bahri%20hospital%20sudan&t=&z=14&ie=UTF8&iwloc=&output=embed"></iframe>
        </div>
      </div>
    </div>

    <script>
      const lastDonationDate = new Date("<?= $lastDonation ?>");
      const today = new Date();
      const diff = today - lastDonationDate;
      const passedDays = Math.floor(diff / (1000 * 60 * 60 * 24));
      const waitDays = 90 - passedDays;
      document.getElementById("nextDonation").textContent = waitDays > 0 ? `${waitDays} يومًا` : "✅ يمكنك التبرع الآن";

      function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
      }

      function enableEditing() {
        document.querySelectorAll('.readonly-input').forEach(input => {
          input.removeAttribute('readonly');
          input.style.backgroundColor = '#fff';
          input.style.border = '1px solid #ccc';
        });
        document.querySelector('.save-btn').classList.remove('d-none');
      }

    </script>

  </body>

</html>
