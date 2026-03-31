<?php
$servername = "localhost";
$username = "root"; // ชื่อผู้ใช้เริ่มต้นของ XAMPP
$password = ""; // รหัสผ่านเริ่มต้นของ XAMPP จะปล่อยว่างไว้
$dbname = "calenda"; // ชื่อฐานข้อมูลของคุณ

try {
    // แนะนำให้ใช้ PDO ในการเชื่อมต่อ เพราะปลอดภัยจากการถูกแฮ็กแบบ SQL Injection และจัดการ Error ได้ดี
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // ตั้งค่าให้ระบบแสดงข้อความแจ้งเตือนเมื่อมี Error เกิดขึ้น
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // หมายเหตุ: หากต้องการทดสอบว่าเชื่อมต่อสำเร็จจริงๆ ไหม ให้เอาเครื่องหมาย // ข้างหน้าบรรทัดด้านล่างออก
    // echo "เชื่อมต่อฐานข้อมูล 'calenda' สำเร็จแล้ว!"; 
    
} catch(PDOException $e) {
    // ถ้าเชื่อมต่อไม่ได้ ระบบจะแสดงข้อความแจ้งสาเหตุ
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
}
?>