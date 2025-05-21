<?php
include '../include/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Truy vấn thông tin hóa đơn cùng thông tin khách hàng và nhân viên
    $sql_bill = "
        SELECT b.*, 
               a.admin_name, a.admin_phone, 
               c.fullname AS customer_name, c.phone AS customer_phone
        FROM tbl_bill b
        LEFT JOIN tbl_admin a ON b.id_admin = a.id_admin
        LEFT JOIN tbl_customer c ON b.id_customer = c.id_customer
        WHERE b.id_bill = $id
        ORDER BY b.date_check_in DESC
        LIMIT 1";
    $result_bill = $conn->query($sql_bill);

    if ($result_bill && $result_bill->num_rows > 0) {
        $bill = $result_bill->fetch_assoc();

        echo "<div style='margin-bottom: 30px; padding: 20px; background-color: #fff; border-radius: 8px; font-size: 16px;text-align:left;'>";

        echo "<h2>Chi tiết hóa đơn</h2>";

        echo "<p><strong>Mã hóa đơn:</strong> #" . $bill['id_bill'] . "</p>";
        echo "<p><strong>ID Bàn:</strong> " . $bill['id_table'] . "</p>";
        echo "<p><strong>Khách hàng:</strong> " . $bill['customer_name'] . "</p>";
        echo "<p><strong>Nhân viên phục vụ:</strong> " . $bill['admin_name'] . " (SDT: " . $bill['admin_phone'] . ")</p>";

        echo "<div style='display: flex; justify-content: space-between;'>";
        echo "<span><strong>Ngày vào:</strong> " . $bill['date_check_in'] . "</span>";
        echo "<span><strong>Ngày ra:</strong> " . $bill['date_check_out'] . "</span>";
       
        echo "</div>";
        echo "</div>";
        // Lấy danh sách món ăn trong hóa đơn
        $sql_detail = "SELECT bi.*, f.food_name 
                       FROM tbl_bill_info bi
                       LEFT JOIN tbl_food f ON bi.id_food = f.id_food
                       WHERE bi.id_bill = $id
                       ORDER BY bi.status ASC";

        $result_detail = $conn->query($sql_detail);

        if ($result_detail && $result_detail->num_rows > 0) {
            echo "<h3 style='text-align:center;';>Danh sách món ăn</h3>";
            echo "<table border='1' cellpadding='8' cellspacing='0' width='100%' style='border-collapse: collapse;'>";
            echo "<tr style='background-color: #f2f2f2;'>
                    <th>Tên món</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                  </tr>";

            $total = 0;
            while ($row = $result_detail->fetch_assoc()) {
                $subtotal = $row['quantity'] * $row['price'];
                $total += $subtotal;

                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['food_name']) . "</td>";
                echo "<td>" . number_format($row['price']) . "đ</td>";
                echo "<td>" . $row['quantity'] . "</td>";
                echo "<td>" . number_format($subtotal) . "đ</td>";
                echo "</tr>";
            }

            echo "<tr style='font-weight: bold;'>
                    <td colspan='3' style='text-align: right;'>Tổng cộng:</td>
                    <td>" . number_format($total) . "đ</td>
                  </tr>";
            echo "</table>";
            echo "<div style='margin-top: 20px; text-align: right;'>
        <button onclick='window.print()' style='padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer;'>
            In hóa đơn
        </button>
      </div>";
        } else {
            echo "<p>Không có món nào trong hóa đơn này.</p>";
        }

    } else {
        echo "<p style='color: red;'>Không tìm thấy hóa đơn.</p>";
    }
} else {
    echo "<p style='color: red;'>Thiếu ID hóa đơn.</p>";
}
?>
