<?php
include '../include/database.php'; 
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM tbl_call_staff WHERE id = ?");
    if ($stmt->execute([$id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không thể xóa']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Thiếu ID']);
}
