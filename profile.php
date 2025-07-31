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
$query = "SELECT name, phone, city, blood_type, points, latitude, longitude FROM users WHERE id = ?";$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $phone, $city, $blood_type, $points, $latitude, $longitude);$stmt->fetch();
$stmt->close();

// جلب آخر تبرع للمستخدم
$lastDonation = null;
$query = "
    SELECT donated_at 
    FROM donations 
    WHERE user_id = ? AND status = 'completed'
    ORDER BY donated_at DESC 
    LIMIT 1
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($lastDonation);
$stmt->fetch();
$stmt->close();

// سجل التبرعات
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

// سجل الطلبات الخاصة بالمستخدم
$myRequests = [];
$query = "SELECT id, hospital_name, city, urgency FROM blood_requests WHERE create_by = ? ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $myRequests[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>الملف الشخصي</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="../../
dist/leaflet.css"
  integrity="sha256-sA+Rd5B6fMkDoQmxXZ7j2vZJH9PaqGfBYY7uh0P4K9o="
  crossorigin=""/>
<script src="../../dist/leaflet.js"
  integrity="sha256-o9N1j8n7HV+e6GIPhsFLJKf6BuDZpE3C2F7FA+iq9Hg="
  crossorigin=""></script>
  <link rel="stylesheet" href="../../Static/css/profile.css">
</head>
<body>

<div class="nav-links">
  <a href="../../index.php">🏠 الرئيسية</a>
  <a href="../Forms/request_form.html">➕ طلب دم</a>
  <a href="#" onclick="toggleDarkMode()">🌓 الوضع الليلي</a>
  <a href="settings.html" class="btn btn-outline-secondary">⚙️ إعدادات الحساب</a>
  <a href="../../public/logout.php">🚪 تسجيل الخروج</a>
</div>

<div class="container">
  <h2 class="text-center">👤 الملف الشخصي</h2>

  <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">✅ تم حفظ التعديلات بنجاح</div>
  <?php endif; ?>

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
          <input type="text" name="phone" class="readonly-input" value="<?= htmlspecialchars($phone) ?>">
        </div>
        <div class="col-md-6">
          <label>المدينة</label>
          <input type="text" name="city" class="readonly-input" value="<?= $city ? htmlspecialchars($city) : "غير محددة" ?>">
        </div>
        <div class="col-md-6">
          <label>فصيلة الدم</label>
          <select name="blood_type" class="form-select readonly-input">
            <option value="" <?= $blood_type ? '' : 'selected' ?>>اختر فصيلة الدم</option>
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
      <button type="button" class="edit-btn" onclick="enableEditing()">✏️ تعديل المعلومات</button>
      <button type="submit" class="save-btn d-none">💾 حفظ التعديلات</button>
    </form>
  </div>

  <div class="card">
    <h4>⏳ الوقت المتبقي للتبرع القادم</h4>
    <p>آخر تبرع: 
      <strong><?= $lastDonation ? date('d-m-Y', strtotime($lastDonation)) : "غير محدد" ?></strong>
    </p>
    <p>يمكنك التبرع مرة أخرى بعد:
      <span id="nextDonation" class="badge-time">جارٍ الحساب...</span>
    </p>
  </div>

  <div class="card">
    <h4>📋 سجل الطلبات</h4>
    <?php if (empty($myRequests)): ?>
      <p>لا توجد طلبات مسجلة.</p>
    <?php else: ?>
      <?php foreach ($myRequests as $req): ?>
        <div class="info-row">
          <a href="request_donations.php?request_id=<?= $req['id'] ?>">
            #<?= htmlspecialchars($req['id']) ?> - <?= htmlspecialchars($req['hospital_name']) ?> - <?= htmlspecialchars($req['city']) ?>
          </a>
          <span class="<?= $req['urgency'] == 'active' ? 'text-warning' : ($req['urgency'] == 'completed' ? 'text-success' : 'text-muted') ?>">
            <?= $req['urgency'] == 'completed' ? 'تم التبرع' : ($req['urgency'] == 'canceled' ? 'ملغاة' : 'قيد الانتظار') ?>
          </span>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="card">
    <h4>💉 سجل التبرعات</h4>
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
  </div>

  <div class="card">
    <h4>📍 أقرب مركز تبرع</h4>
      <div class="map-box">
<?php
$lat = $latitude; // خط العرض
$lng = $longitude; // خط الطول
?>

<iframe
  src="https://maps.google.com/maps?q=<?php echo $lat; ?>,<?php echo $lng; ?>&z=14&output=embed"
  width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy">
</iframe>
    </div>
</div>

<script>
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

  <?php if ($lastDonation): ?>
    const lastDonationDate = new Date("<?= $lastDonation ?>");
    const today = new Date();
    const diff = today - lastDonationDate;
    const passedDays = Math.floor(diff / (1000 * 60 * 60 * 24));
    const waitDays = 90 - passedDays;
    document.getElementById("nextDonation").textContent = waitDays > 0 ? `${waitDays} يومًا` : "✅ يمكنك التبرع الآن";
  <?php else: ?>
    document.getElementById("nextDonation").textContent = "غير محدد";
  <?php endif; ?>

// إحداثيات المستخدم من PHP (تأكد أنها موجودة في المتغيرات $latitude و $longitude)
const userLat = <?= $latitude ? $latitude : 'null' ?>;
const userLng = <?= $longitude ? $longitude : 'null' ?>;

if (userLat === null || userLng === null) {
  document.getElementById('map').innerHTML = "⚠️ لم يتم تحديد موقعك بعد.";
} else {
  // إنشاء الخريطة مركزها موقع المستخدم
  const map = L.map('map').setView([userLat, userLng], 13);

  // إضافة طبقة الخريطة من OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // علامة موقع المستخدم
  const userMarker = L.marker([userLat, userLng]).addTo(map)
    .bindPopup('موقعك الحالي')
    .openPopup();

  // مثال: قائمة مراكز تبرع يدوية (يمكنك تعديلها أو جلبها من قاعدة البيانات)
  const donationCenters = [
    { name: "مركز تبرع بحري", lat: 15.605, lng: 32.535 },
    { name: "مركز تبرع الخرطوم", lat: 15.589, lng: 32.535 },
    { name: "مركز تبرع أم درمان", lat: 15.588, lng: 32.507 },
  ];

  // إضافة علامات مراكز التبرع
  donationCenters.forEach(center => {
    L.marker([center.lat, center.lng]).addTo(map)
      .bindPopup(center.name);
  });

  // حساب أقرب مركز تبرع (بناءً على المسافة)
  function getDistance(lat1, lng1, lat2, lng2) {
    const R = 6371; // نصف قطر الأرض بالكيلومتر
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = 
      Math.sin(dLat/2) * Math.sin(dLat/2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }

  let minDistance = Infinity;
  let nearestCenter = null;

  donationCenters.forEach(center => {
    const dist = getDistance(userLat, userLng, center.lat, center.lng);
    if (dist < minDistance) {
      minDistance = dist;
      nearestCenter = center;
    }
  });

  if (nearestCenter) {
    const popupContent = `أقرب مركز تبرع: <b>${nearestCenter.name}</b><br>على بعد ${minDistance.toFixed(2)} كم`;
    L.popup()
      .setLatLng([userLat, userLng])
      .setContent(popupContent)
      .openOn(map);
  }
}
if (!window.L) {
  alert("❌ مكتبة Leaflet لم يتم تحميلها. تأكد من الاتصال بالإنترنت.");
} else {
  alert("✅ Leaflet تم تحميله بنجاح");
}
</script>



</body>
</html>