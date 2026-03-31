<?php
require_once 'db_connect.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // ค้นหา note_id ที่เชื่อมกับกิจกรรมนี้ก่อนเพื่อลบให้ครบทั้งระบบ
        $stmt = $conn->prepare("SELECT note_id FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event && $event['note_id']) {
            // ลบที่ table notes (ระบบจะลบ events และ attachments ที่เกี่ยวข้องให้อัตโนมัติด้วย CASCADE)
            $del = $conn->prepare("DELETE FROM notes WHERE id = :nid");
            $del->execute([':nid' => $event['note_id']]);
        } else {
            // กรณีไม่มี note เชื่อมอยู่ ให้ลบแค่ event ตรงๆ
            $del = $conn->prepare("DELETE FROM events WHERE id = :id");
            $del->execute([':id' => $id]);
        }

        echo "success";
    } catch(PDOException $e) {
        echo "error: " . $e->getMessage();
    }
}
?>