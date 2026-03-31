<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['file_id'])) {
    $file_id = $_POST['file_id'];

    try {
        // หา URL ของไฟล์เพื่อไปตามลบไฟล์จริงในเครื่อง
        $stmt = $conn->prepare("SELECT file_url FROM attachments WHERE id = :id");
        $stmt->execute([':id' => $file_id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            // ลบไฟล์ออกจากโฟลเดอร์ uploads/
            if (file_exists($file['file_url'])) {
                unlink($file['file_url']);
            }
            // ลบข้อมูลไฟล์ออกจากฐานข้อมูล
            $del = $conn->prepare("DELETE FROM attachments WHERE id = :id");
            $del->execute([':id' => $file_id]);
            
            echo "success";
        } else {
            echo "error: ไม่พบไฟล์";
        }
    } catch(PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>