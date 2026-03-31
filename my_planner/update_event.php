<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $event_date = $_POST['event_date'];
    
    $start_time = $event_date . " 00:00:00";
    $end_time = $event_date . " 23:59:59";

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT note_id FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        $note_id = $event['note_id'];

        // 1. อัปเดตข้อความ
        if ($note_id) {
            $updNote = $conn->prepare("UPDATE notes SET title = :title, content = :content WHERE id = :nid");
            $updNote->execute([':title' => $title, ':content' => $content, ':nid' => $note_id]);
        }

        // 2. อัปเดตวันที่และหัวข้อปฏิทิน
        $updEvent = $conn->prepare("UPDATE events SET title = :title, start_time = :start, end_time = :end WHERE id = :id");
        $updEvent->execute([':title' => $title, ':start' => $start_time, ':end' => $end_time, ':id' => $id]);

        // 3. จัดการอัปโหลดไฟล์ "เพิ่มเติม" (ถ้ามีการเลือกไฟล์มาใหม่ตอนแก้ไข)
        if (isset($_FILES['new_attachments']['name'][0]) && $_FILES['new_attachments']['name'][0] != '') {
            $file_count = count($_FILES['new_attachments']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['new_attachments']['error'][$i] == 0) {
                    $file_name = time() . '_' . $i . '_edit_' . $_FILES['new_attachments']['name'][$i];
                    $target_file = "uploads/" . basename($file_name);
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    if (move_uploaded_file($_FILES['new_attachments']['tmp_name'][$i], $target_file)) {
                        $stmtFile = $conn->prepare("INSERT INTO attachments (file_url, file_type, note_id, event_id) VALUES (:url, :type, :note_id, :event_id)");
                        $stmtFile->execute([':url' => $target_file, ':type' => $file_type, ':note_id' => $note_id, ':event_id' => $id]);
                    }
                }
            }
        }

        $conn->commit();
        echo "success";
    } catch(PDOException $e) {
        $conn->rollBack();
        echo "error: " . $e->getMessage();
    }
}
?>