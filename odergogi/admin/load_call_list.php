<?php
include '../include/database.php';

$result = $conn->query("SELECT * FROM tbl_call_staff ORDER BY id DESC"); // Sắp xếp mới nhất

if ($result && $result->num_rows > 0) {
    while ($call = $result->fetch_assoc()) {
        echo "<div class='call-box' id='call-{$call['id']}'>
                <div class='call-info'>
                    <p><strong>Bàn số:</strong> {$call['id_table']},  <strong>Hóa đơn:</strong> {$call['bill_id']}</p>                 
                    <p><strong>Thời gian gọi:</strong> {$call['call_time']}</p>
                </div>
                <button class='delete-btn' onclick='deletecall({$call['id']})'>&times;</button>
              </div>";
    }
} else {
    echo "<p class='no-call'>Không có cuộc gọi nào.</p>";
}

$conn->close();
?>
<style>
    .call-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f9f9f9;
    padding: 15px 20px;
    margin: 10px auto;
    width: 95%;
    max-width: 600px;
    border-left: 5px solid #007BFF;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    font-size: 16px;
    position: relative;
}

.call-info p {
    margin: 4px 0;
    line-height: 1.4;
}

.delete-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    font-size: 20px;
    padding: 6px 12px;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.delete-btn:hover {
    background-color: #c82333;
}

.no-call {
    text-align: center;
    margin-top: 30px;
    color: #666;
}
</style>