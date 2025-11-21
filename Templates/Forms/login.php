<!DOCTYPE html>
<html lang="ar" dir="rtl">

        <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>تسجيل الدخول</title>
                <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap"
                        rel="stylesheet" />
                <link rel="stylesheet" href="../../Static/css/login.css" />
        </head>

        <body>

                <div class="login-container">
                        <h2>تسجيل الدخول</h2>
                        

                        <form action="log_in_proces.php" method="post">
                                <input type="text" name="identifier" placeholder="رقم الهاتف أو البريد الإلكتروني"
                                        required />
                                <input type="password" name="password" placeholder="كلمة المرور" required />
                                <button type="submit" class="btn">تسجيل دخول</button>
                        </form>

                        <div class="links">
                                <p>ليس لديك حساب؟</p>
                                <a href="SignUp.html">انشاء حساب جديد</a>
                        </div>
                </div>

        </body>

</html>
