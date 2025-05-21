<?php
session_start();
header('Content-Type: application/json'); 

// Kiểm tra nếu dữ liệu JSON được gửi
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra có nhận được id_food và id_bill không
if (!isset($data['id_food']) || !isset($data['id_bill'])) {
    echo json_encode(['success' => false, 'error' => 'Thiếu id_food hoặc id_bill']);
    exit();
}

$id_food = $data['id_food'];
$id_bill = $data['id_bill'];

// Kiểm tra nếu giỏ hàng trong session có chứa id_bill
if (isset($_SESSION['cart'][$id_bill])) {
    // Kiểm tra nếu sản phẩm tồn tại trong giỏ hàng
    if (isset($_SESSION['cart'][$id_bill][$id_food])) {
        // Xóa sản phẩm khỏi giỏ hàng
        unset($_SESSION['cart'][$id_bill][$id_food]);
        
        // Cập nhật lại mảng giỏ hàng (nếu cần)
        $_SESSION['cart'][$id_bill] = array_values($_SESSION['cart'][$id_bill]);
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Sản phẩm không tồn tại trong giỏ hàng']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Giỏ hàng không tồn tại']);
}
exit();
?>
