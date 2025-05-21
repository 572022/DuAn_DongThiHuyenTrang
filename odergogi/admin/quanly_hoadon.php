<?php
include '../include/database.php';

$limit = 8; // Hiển thị 10 hóa đơn mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// Lấy tổng số hóa đơn để tính tổng số trang
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM tbl_bill");
$totalRow = $totalResult->fetch_assoc();
$totalBills = $totalRow['total'];
$totalPages = ceil($totalBills / $limit);

$sql = "SELECT b.*, t.name_table, a.admin_name, c.fullname
        FROM tbl_bill b
        LEFT JOIN tbl_table t ON b.id_table = t.id_table
        LEFT JOIN tbl_admin a ON b.id_admin = a.id_admin
        LEFT JOIN tbl_customer c ON b.id_customer = c.id_customer
        ORDER BY b.date_check_in DESC
        LIMIT $start, $limit";

$result = $conn->query($sql);
?>
<style>
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
        border-bottom: 2px solid #007BFF;
        padding-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 auto 20px auto;
        font-family: Arial, sans-serif;
        font-size: 14px;
        background-color: #fff;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    th, td {
        padding: 4px 4px;
        border: 1px solid #ddd;
        text-align: center;
    }

    thead {
        background-color: #f5f5f5;
        font-weight: bold;
        color: #333;
    }

    tbody tr:hover {
        background-color: #f1f1f1;
    }
    
a {
    margin: 0px;
    font-weight: bold;
    padding: 5px;
    color: #263544;
}
    .btn {
        padding: 6px 10px;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin: 0 3px;
    }

    .detail-btn {
        background-color: #007BFF;
        color: white;
    }

    .delete-btn {
        background-color: #dc3545;
        color: white;
    }

    .modal {
        display: none; 
        position: fixed;
        z-index: 999;
        padding-top: 80px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fff;
        margin: auto;
        padding: 20px;
        border-radius: 6px;
        width: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: #000;
    }

    #bill-detail-content {
        margin-top: 15px;
    }

    .pagination a {
        padding: 5px 10px;
        text-decoration: none;
        color: #007BFF;
        border: 1px solid #ddd;
        margin: 0 3px;
        border-radius: 4px;
    }

    .pagination a:hover {
        background-color: #007BFF;
        color: white;
    }

    .pagination a[style*="bold"] {
        background-color: #007BFF;
        color: white;
        font-weight: bold;
    }
   
</style>
<h2 style="text-align:center;">Danh sách hóa đơn</h2>
<table>
    <thead>
        <tr>
            <th>MHD</th>
            <th>Bàn</th>
            <th>Khách hàng</th>
            <th>Nhân viên</th>
            <th>Ngày vào</th>
            <th>Ngày ra</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody id="invoice-table">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_bill'] ?></td>
                    <td><?= htmlspecialchars($row['name_table']) ?></td>
                    <td><?= htmlspecialchars($row['fullname'] ?? '---') ?></td>
                    <td><?= htmlspecialchars($row['admin_name'] ?? '---') ?></td>
                    <td><?= $row['date_check_in'] ?></td>
                    <td><?= $row['date_check_out'] ?></td>
                    <td><?= number_format($row['total_amount']) ?>.000đ</td>
                    <td><?= $row['status'] == 1 ? 'Đã thanh toán' : 'Chưa thanh toán' ?></td>
                    <td>
                    <button type="button" class="btn detail-btn" onclick="loadBillDetail(<?= $row['id_bill'] ?>)">
                        Xem chi tiết
                    </button>
                    <button class="btn delete-btn" onclick="deleteBill(<?= $row['id_bill'] ?>)">Xóa</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">Không có hóa đơn nào</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<!-- chi tiết hóa đơn -->
<div id="billDetailModal" class="modal">       
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="bill-detail-content">
            <p>Đang tải...</p>
        </div>
    </div>
</div>
<div style="margin-top: 10px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>#user"<?= $i == $page ? 'font-weight: bold;' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>

<script>
// Mở modal và tải chi tiết hóa đơn
function loadBillDetail(billId) {
    // Mở modal
    document.getElementById('billDetailModal').style.display = 'block';

    fetch('view_bill_inf.php?id=' + billId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('bill-detail-content').innerHTML = data;
        })
        .catch(error => {
            document.getElementById('bill-detail-content').innerHTML = 'Lỗi khi tải chi tiết hóa đơn.';
            console.error('Error:', error);
        });
}

function deleteBill(id) {
    if (confirm("Bạn có chắc muốn xóa hóa đơn này?")) {
        fetch('delete_bill.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // hoặc thông báo xóa thành công
            // Sau khi xóa xong, có thể reload lại bảng:
            location.reload();
        })
        .catch(error => {
            console.error('Lỗi khi xóa:', error);
            alert('Đã xảy ra lỗi khi xóa.');
        });
    }
}


// Đóng modal khi nhấn nút đóng
function closeModal() {
    document.getElementById('billDetailModal').style.display = 'none';
}

</script>