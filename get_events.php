<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
// ดึงข้อมูลพื้นฐานรวมถึง content และ color
$sql = "SELECT id, title, content, color, start_time AS start, end_time AS end FROM events";

if ($search !== '') {
    $sql .= " WHERE title LIKE :search OR content LIKE :search";
}

$stmt = $conn->prepare($sql);
if ($search !== '') {
    $stmt->execute([':search' => '%' . $search . '%']);
} else {
    $stmt->execute();
}

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงไฟล์แนบมาใส่ในแต่ละ Event (สำคัญมากเพื่อให้รูปขึ้น)
foreach ($events as &$event) {
    $stmtFile = $conn->prepare("SELECT id, file_url AS url, file_type AS type FROM attachments WHERE event_id = :id");
    $stmtFile->execute([':id' => $event['id']]);
    $event['attachments'] = $stmtFile->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($events);
?>