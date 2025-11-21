<?php
session_start();
require_once '../../Core/db.php';

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Forms/login.php");
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

////////////////
// جلب تبرعات المستخدم


$donations = [];
$query = "
    SELECT d.donated_at, d.status, r.hospital_name
    FROM donations d
    JOIN blood_requests r ON d.request_id = r.id
    WHERE d.user_id = ?
    ORDER BY d.donated_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $donations[] = $row;
}

$stmt->close();
$blood_requests=[];
$query= "SELECT id ,hospital_name, city, blood_type, bags, contact_number, notes, urgency from blood_requests  WHERE user_id = ?";
$result_blood_requests=$conn->prepare($query);
$result_blood_requests->bind_param("i", $user_id);
$result_blood_requests->execute();
$result = $result_blood_requests->get_result();
while ($row = $result->fetch_assoc()) {
    $blood_requests[] = $row;
}
$result_blood_requests->close();



?>
<?php include "../../public/header.php"?>

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
          <ul>
         <?php if(empty($donations)){
         echo" <span>لاتوجد تبرعات مسجلة حتى الآن.</span>";
         }
         else{
            
          foreach($blood_requests as $blood_request): ?>
          <li>
            في مستشفى <?= htmlspecialchars($blood_request['hospital_name']) ; ?>
          </li>
          
            
        
    
          <span class="text-success">  </span>
        </div>
      </div>
        <?php endforeach; 
         }
          ?>
        </ul>
          
      <div class="card">
        <h4>💉 سجل التبرعات</h4>
        <ul>
          <ul>
  <?php if (empty($donations)): ?>
    <li>لا توجد تبرعات مسجلة حتى الآن.</li>
  <?php else: ?>
    <?php foreach ($donations as $donation): ?>
      <li>
        <?= date('d-m-Y', strtotime($donation['donated_at'])) ?>
        في مستشفى <?= htmlspecialchars($donation['hospital_name']) ?>
        - الحالة: 
        <strong class="<?= $donation['status'] == 'completed' ? 'text-success' : 'text-warning' ?>">
          <?= $donation['status'] == 'completed' ? 'تم التبرع' : 'قيد الانتظار' ?>
        </strong>
      </li>
    <?php endforeach; ?>
  <?php endif; ?>
</ul>

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
