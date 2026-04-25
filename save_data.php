<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'] ?: "00:00";
    $color = $_POST['event_color'] ?: "#0d6efd";

    $start_time = $event_date . " " . $event_time . ":00";
    $end_time = $event_date . " 23:59:59";

    try {
        $sql = "INSERT INTO events (title, content, start_time, end_time, color) VALUES (:title, :content, :start, :end, :color)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':start' => $start_time,
            ':end' => $end_time,
            ':color' => $color
        ]);
        
        $event_id = $conn->lastInsertId();

        if (isset($_FILES['attachments']['name'][0]) && $_FILES['attachments']['name'][0] != '') {
            $file_count = count($_FILES['attachments']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['attachments']['error'][$i] == 0) {
                    $file_name = time() . '_' . $i . '_' . $_FILES['attachments']['name'][$i];
                    $target_file = "uploads/" . basename($file_name);
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    if (move_uploaded_file($_FILES['attachments']['tmp_name'][$i], $target_file)) {
                        $stmtFile = $conn->prepare("INSERT INTO attachments (file_url, file_type, event_id) VALUES (:url, :type, :event_id)");
                        $stmtFile->execute([':url' => $target_file, ':type' => $file_type, ':event_id' => $event_id]);
                    }
                }
            }
        }
        header("Location: index.php");
    } catch(PDOException $e) { echo "Error: " . $e->getMessage(); }
}
?>