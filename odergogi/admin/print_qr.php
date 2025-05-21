<?php
include '../include/phpqrcode/qrlib.php';

if (isset($_GET['id_table']) && isset($_GET['bill_id']) &&isset($_GET['admin_id']) ) {
    $id_table = $_GET['id_table'];
    $bill_id = $_GET['bill_id'];
    $admin_id= $_GET['admin_id'];

    // Link để khách hàng đặt món
    // $url = "http://localhost:3000/odergogi/order_menu.php?id_table=$id_table&bill_id=$bill_id&admin_id=$admin_id";
    $url = "http://localhost:3000/order_menu.php?id_table=$id_table&bill_id=$bill_id&admin_id=$admin_id";

    // Đường dẫn lưu mã QR
    $qr_file = "../img/qrcodes/table_{$id_table}_bill_{$bill_id}.png";

    // Tạo mã QR
    QRcode::png($url, $qr_file, QR_ECLEVEL_L, 10);

    echo "
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        top: 50px;
        left: 20px;
        text-align: center;
        width: 100%;
    }
}
</style>
";

echo "<div class='print-area'>";
echo "<h2>Mã QR cho bàn $id_table - Hóa đơn $bill_id</h2>";
echo "<img src='$qr_file' style='width:400px; height:400px;' />";
echo "</div>";

echo "<br><button onclick='window.print()'>In mã QR</button>";

}
?>
