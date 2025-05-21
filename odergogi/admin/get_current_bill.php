<?php
ob_clean();
header('Content-Type: application/json');
include '../include/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_table = $_POST['id_table'];

    $sql = "SELECT id FROM tbl_bill WHERE id_table = '$id_table' AND status = 0 ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'bill_id' => $row['id']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy hóa đơn']);
    }
}

$conn->close();
?>
