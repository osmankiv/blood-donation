<?php
$status ="";

if(isset($_GET['success'])&& $_GET['success']==1){
     $status_message = "✅ تم تسجيل طبك بنجاح! شكراً لك.";
    $status_class = "success";

} 
elseif(isset($_GET['success'])){
    $status_message = "❌ حدث خطأ أثناء التسجيل: " ;
    $status_class = "error";
}
        
    

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>طلب تبرع</title>
    <link rel="stylesheet" href="../../Static/css/request_form.css">

</head>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <title>طلب تبرع</title>
        <link rel="stylesheet" href="../../Static/css/request_form.css">
    </head>

    <body>
        <?php include "../../public/header.php";
        if(!empty($status_message)){
            echo"$status_message";
        }
        ?>
        

        <div class="form-container">
            <h2>طلب تبرع بالدم</h2>

            <form method="post" action="submit_request.php">
                <label>اسم المستشفى</label>
                <input type="text" name="hospital_name" required>

                <label>المدينة</label>
                <input type="text" name="city" required>

                <label>فصيلة الدم المطلوبة</label>
                <select name="blood_type" required>
                    <option value="">اختر</option>
                    <option value="+A">+A</option>
                    <option value="-A">-A</option>
                    <option value="+B">+B</option>
                    <option value="-B">-B</option>
                    <option value="+O">+O</option>
                    <option value="-O">-O</option>
                    <option value="+AB">+AB</option>
                    <option value="-AB">-AB</option>
                </select>

                <label>عدد الأكياس</label>
                <input type="number" name="bags" min="1" required>

                <label>رقم التواصل</label>
                <input type="tel" name="contact_number" pattern="[0-9+]{7,15}" required>
                <!-- <label>تاريخ الحاجة للتبرع</label>
                <input type="date" name="donation_date" required> -->
                <label class="form-label">الحالة</label>
                <select name="urgency" class="form-select">
                    <option value="">اختر الحالة</option>
                    <option value="طارئة" <?= $status == "طارئة" ? "selected" : "" ?>>طارئة</option>
                    <option value="عادية" <?= $status == "عادية" ? "selected" : "" ?>>عادية</option>
                </select>
                


                <label>ملاحظات إضافية</label>
                <textarea name="notes" rows="3"></textarea>

                <button type="submit">إرسال الطلب</button>
            </form>
        </div>

    </body>



</html>