<?php
require_once 'db_connect.php';
header('Content-Type: application/json');

try {
    // รับค่าคำค้นหา (ถ้ามี)
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $sql = "SELECT e.id, e.title, e.start_time as start, e.end_time as end,
                   n.content, a.id as file_id, a.file_url, a.file_type
            FROM events e
            LEFT JOIN notes n ON e.note_id = n.id
            LEFT JOIN attachments a ON e.id = a.event_id";
            
    // ถ้ามีการค้นหา ให้กรองข้อมูลที่มีคำนั้นใน หัวข้อ หรือ เนื้อหา
    if ($search !== '') {
        $sql .= " WHERE e.title LIKE :search OR n.content LIKE :search";
    }
            
    $stmt = $conn->prepare($sql);
    
    if ($search !== '') {
        $stmt->execute([':search' => '%' . $search . '%']);
    } else {
        $stmt->execute();
    }
    
    $raw_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $events_dict = [];
    foreach ($raw_events as $row) {
        $id = $row['id'];
        if (!isset($events_dict[$id])) {
            $events_dict[$id] = [
                'id' => $id, 'title' => $row['title'], 'start' => $row['start'], 'end' => $row['end'],
                'content' => $row['content'], 'attachments' => []
            ];
        }
        if ($row['file_url']) {
            $events_dict[$id]['attachments'][] = [
                'id' => $row['file_id'], // เพิ่ม ID ไฟล์เข้ามาสำหรับใช้ตอนกดลบ
                'url' => $row['file_url'],
                'type' => $row['file_type']
            ];
        }
    }
    
    echo json_encode(array_values($events_dict));

} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>