<?php
include 'include/database.php';
header('Content-Type: application/json');

// Lấy dữ liệu POST từ JSON
$data = json_decode(file_get_contents("php://input"), true);

$id_table = $data['id_table'] ?? null;
$bill_id = $data['bill_id'] ?? null;

$response = [];

if ($id_table && $bill_id) {
    $stmt = $conn->prepare("INSERT INTO tbl_call_staff (id_table, bill_id, call_time) VALUES (?, ?, NOW())");
    if ($stmt->execute([$id_table, $bill_id])) {
        $response = ['success' => true];
    } else {
        $response = ['success' => false, 'error' => 'Không thể lưu yêu cầu'];
    }
} else {
    $response = ['success' => false, 'error' => 'Thiếu dữ liệu'];
}

echo json_encode($response);
$conn->close();
?>
