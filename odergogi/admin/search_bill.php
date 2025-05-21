<?php
include '../include/database.php';
header('Content-Type: application/json');

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$start_date = $data['start_date'] ?? null;
$end_date = $data['end_date'] ?? null;
$keyword = $data['khoa_tim_kiem'] ?? null;
$start = $data['start'] ?? 0;
$limit = $data['limit'] ?? 10;

if (!$start_date && !$end_date && !$keyword) {
    echo json_encode(["success" => false, "error" => "Không có dữ liệu tìm kiếm"]);
    exit;
}

$sql = "
SELECT b.*, t.name_table, a.admin_name, c.fullname
FROM tbl_bill b
LEFT JOIN tbl_table t ON b.id_table = t.id_table
LEFT JOIN tbl_admin a ON b.id_admin = a.id_admin
LEFT JOIN tbl_customer c ON b.id_customer = c.id_customer
WHERE 1=1
";

$params = [];
$types = "";

// Lọc theo ngày
if ($start_date && $end_date) {
    $sql .= " AND b.date_check_in >= ? AND b.date_check_out <= ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= "ss";
}

if ($keyword) {
    $sql .= " AND (
        b.id_bill LIKE ? OR
        t.name_table LIKE ? OR
        a.admin_name LIKE ? OR
        c.fullname LIKE ? OR
        b.date_check_in LIKE ? OR
        b.date_check_out LIKE ? OR
        b.status LIKE ? OR
        b.total_amount LIKE ?
    )";
    for ($i = 0; $i < 8; $i++) {
        $params[] = "%$keyword%";
        $types .= "s";
    }
}

$sql .= " ORDER BY b.date_check_in DESC LIMIT ?, ?";
$params[] = $start;
$params[] = $limit;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$bills = [];
while ($row = $result->fetch_assoc()) {
    $bills[] = $row;
}

echo json_encode([
    "success" => true,
    "bills" => $bills
]);

$stmt->close();
$conn->close();
?>
