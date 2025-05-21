<?php
$servername = "localhost";
$username = "root"; // Mặc định của XAMPP
$password = ""; // XAMPP không có mật khẩu mặc định
$dbname = "db_ordergogi";

// Kết nối MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
