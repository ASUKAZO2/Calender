<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $event_date = $_POST['event_date'];
    
    $start_time = $event_date . " 00:00:00";
    $end_time = $event_date . " 23:59:59";
    
    try {
        $conn->beginTransaction();

        $stmtNote = $conn->prepare("INSERT INTO notes (title, content) VALUES (:title, :content)");
        $stmtNote->execute([':title' => $title, ':content' => $content]);
        $note_id = $conn->lastInsertId();

        $stmtEvent = $conn->prepare("INSERT INTO events (title, start_time, end_time, note_id) VALUES (:title, :start, :end, :note_id)");
        $stmtEvent->execute([':title' => $title, ':start' => $start_time, ':end' => $end_time, ':note_id' => $note_id]);
        $event_id = $conn->lastInsertId();

        // --- ส่วนที่แก้ไข: วนลูปจัดการอัปโหลดหลายไฟล์ ---
        if (isset($_FILES['attachments']['name'][0]) && $_FILES['attachments']['name'][0] != '') {
            $file_count = count($_FILES['attachments']['name']); // นับว่าแนบมาทั้งหมดกี่ไฟล์
            
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['attachments']['error'][$i] == 0) {
                    // เติมเวลาและลำดับ ($i) ป้องกันชื่อไฟล์ซ้ำกัน
                    $file_name = time() . '_' . $i . '_' . $_FILES['attachments']['name'][$i];
                    $target_dir = "uploads/";
                    $target_file = $target_dir . basename($file_name);
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    // ตรวจสอบความถูกต้องของอาร์เรย์ tmp_name[$i] อย่างรัดกุม
                    if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $target_file)) {
                        $stmtFile = $conn->prepare("INSERT INTO attachments (file_url, file_type, note_id, event_id) VALUES (:url, :type, :note_id, :event_id)");
                        $stmtFile->execute([':url' => $target_file, ':type' => $file_type, ':note_id' => $note_id, ':event_id' => $event_id]);
                    }
                }
            }
        }

        $conn->commit();
        echo "<script>alert('บันทึกข้อมูลสำเร็จ!'); window.location.href='index.php';</script>";

    } catch(PDOException $e) {
        $conn->rollBack();
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}
?>