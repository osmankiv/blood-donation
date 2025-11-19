<?php// include"";?>
<head>
    <meta charset="UTF-8">
 
    <link rel="stylesheet" href="../../Static/css/header.css">
  </head>

  <body>
<div class="nav-links">
      <a href="../../index.php">🏠 الرئيسية</a>
      <a href="../Forms/request_form.php">➕ طلب دم</a>
      <a href="#" onclick="toggleDarkMode()">🌓 الوضع الليلي</a>
      <a href="../../public/logout.php">🚪 تسجيل الخروج</a>
    </div>

 
        <h2> </h2>

        <?php
         if (!empty($status_message)): ?>
            <p class="status-message <?php echo $status_class; ?>">
                <?php echo $status_message; ?>
            </p>
        <?php endif; ?>
<script>
      function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
      }

</script>

</body>