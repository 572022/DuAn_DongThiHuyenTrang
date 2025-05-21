
// session_start();

// $id_food = $_POST['id_food'] ?? 0;
// $action = $_POST['action'] ?? '';

// if (!isset($_SESSION['cart']) || $id_food <= 0) {
//     echo json_encode(["status" => "error", "message" => "Giỏ hàng trống hoặc sản phẩm không hợp lệ"]);
//     exit;
// }

// foreach ($_SESSION['cart'] as &$item) {
//     if ($item['id_food'] == $id_food) {
//         if ($action == "increase") {
//             $item['quantity'] += 1;
//         } elseif ($action == "decrease" && $item['quantity'] > 1) {
//             $item['quantity'] -= 1;
//         }
//         echo json_encode(["status" => "success"]);
//         exit;
//     }
// }

<?php
session_start();

$bill_id = $_SESSION['bill_id'] ?? null;  
$id_food = $_POST['id_food'] ?? 0;
$action = $_POST['action'] ?? '';

if (!isset($_SESSION['cart'][$bill_id]) || $id_food <= 0) {
    echo json_encode(["status" => "error", "message" => "Giỏ hàng trống hoặc sản phẩm không hợp lệ"]);
    exit;
}

if (!isset($_SESSION['cart'][$bill_id])) {
    $_SESSION['cart'][$bill_id] = [];  
}

foreach ($_SESSION['cart'][$bill_id] as &$item) {
    if ($item['id_food'] == $id_food) {
        if ($action == "increase") {
            $item['quantity'] += 1;  
        } elseif ($action == "decrease" && $item['quantity'] > 1) {
            $item['quantity'] -= 1;  
        }
        echo json_encode(["status" => "success", "cart" => $_SESSION['cart'][$bill_id]]);
        exit;
    }
}

// Nếu không tìm thấy món ăn, thêm mới vào giỏ hàng
$_SESSION['cart'][$bill_id][$id_food] = [
    'id_food' => $id_food,
    'food_name' => "Tên món ăn",  
    'price' => 100,  
    'image' => "path/to/image.jpg",  
    'quantity' => 1  
];

echo json_encode(["status" => "success", "cart" => $_SESSION['cart'][$bill_id]]);
?>
