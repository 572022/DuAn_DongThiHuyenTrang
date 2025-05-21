<?php
session_start();

include '../include/database.php';
// Lấy danh sách bàn
$sql = "SELECT * FROM tbl_table";
$result = $conn->query($sql);

$sql_order = "SELECT id_table 
              FROM tbl_table 
              WHERE status = 1 
              ORDER BY id_table";

$result_order = $conn->query($sql_order);

if (!$result_order) {
    die("Lỗi truy vấn: " . $conn->error);
}
$tables = [];
if ($result_order && $result_order->num_rows > 0) {
    while ($row = $result_order->fetch_assoc()) {
        $id_table = $row['id_table']; // Lưu mã bàn

        // Truy vấn mã hóa đơn cho bàn này
        $sql_bill = "SELECT id_bill FROM tbl_bill WHERE id_table = ? ORDER BY id_bill DESC LIMIT 1";
        $stmt = $conn->prepare($sql_bill);
        $stmt->bind_param("i", $id_table);
        $stmt->execute();
        $result_bill = $stmt->get_result();
        $id_bill = null;
        if ($result_bill && $result_bill->num_rows > 0) {
            $row_bill = $result_bill->fetch_assoc();
            $id_bill = $row_bill['id_bill'];
            $print_id = $id_bill;
        } else {
            $print_id = "Không có hóa đơn";
        }
        $stmt->close();

        $food_items = [];
        if ($id_bill) {
            $sql_items = "SELECT f.food_name, bi.quantity, bi.price, bi.status, bi.id_bill_info
                          FROM tbl_bill_info bi
                          JOIN tbl_food f ON bi.id_food = f.id_food
                          WHERE bi.id_bill = ?";
            $stmt_items = $conn->prepare($sql_items);
            $stmt_items->bind_param("i", $id_bill);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            if ($result_items && $result_items->num_rows > 0) {
                while ($item = $result_items->fetch_assoc()) {
                    $food_items[] = $item; // Lưu thông tin món ăn vào mảng
                }
            }
            $stmt_items->close();
        }

        // Lưu vào mảng $tables với id_table, print_id và thông tin món ăn
        $tables[] = [
            'id_table' => $id_table,
            'print_id' => $print_id,
            'food_items' => $food_items
        ];
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý bàn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="/admin/admin_style.css">
</head>

<body>

    <!-- Tabs -->
    <div class="tab" style="text-align: center;">
        <!-- <button class="tablinks">
           <img src="/img/logo_gogi.png" alt="" with="100px">
        </button> -->
        <button class="tablinks" id="defaultOpen" onclick="tabclick(event, 'table')">
            <!-- id="defaultOpen" -->
            <i class="fa-solid fa-table"></i> <span>Quản lý bàn</span>
        </button>
        <button class="tablinks" onclick="tabclick(event, 'tb')">
            <i class="fa-solid fa-bell"></i> <span>Thông báo</span>
        </button>
        <button class="tablinks" onclick="tabclick(event, 'qltrangthai')">
            <i class="fa-solid fa-circle-check"></i><span>Quản lý trạng thái</span>
        </button>
        <button class="tablinks" onclick="tabclick(event, 'qlhoadon')">
            <i class="fa-solid fa-file-invoice"></i> <span>Quản lý hóa đơn</span>
        </button>
        <button class="tablinks" onclick="tabclick(event, 'user')">
            <i class="fa-solid fa-user"></i><span>Thông tin cá nhân</span>
        </button>
    </div>

    <!-- =============================== -->
    <div id="table" class="tabcontent">
        <h3 style="
    color: #1a2a3a;
    text-align: center;">Danh sách bàn</h3>
        <div class="list_table">
            <div class="table-flex">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status_class = $row['status'] ? "occupied" : "available";
                        $status_text = $row['status'] ? "Đã đặt" : "Trống";
                        $bg_color = $row['status'] ? "#ffcccc" : "#ccffcc"; // Đỏ nếu đã đặt, xanh nếu trống

                        echo "<div class='table' style='background-color: $bg_color' 
                                onclick=\"handleTableClick({$row['id_table']}, {$row['status']})\">
                                <strong>Mã bàn: {$row['id_table']}</strong><br>
                                {$row['name_table']}<br>
                                <span class='$status_class'>Trạng thái: $status_text</span>
                            </div>";
                    }
                } else {
                    echo "<p>Không có bàn nào!</p>";
                }
                ?>
            </div>
            <div class="tab_QR"></div>

        </div>
    </div>
    <div id="tb" class="tabcontent">
        <h3>
            Thông báo của khách hàng
        </h3>
        <div id="call-list"></div>
    </div>
    <div id="qltrangthai" class="tabcontent">
        <h3 style="text-align:center; color:#263544; border-bottom:2px solid #263544; padding-bottom:5px;">Quản lý trạng thái</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; margin-top: 20px;">
            <thead style="background-color:#dadada; color: #000; ">
                <tr>
                    <th>Mã bàn</th>
                    <th>Mã hóa đơn</th>
                    <th>Tên món ăn</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables as $table): ?>
                    <?php if (!empty($table['food_items'])): ?>
                        <?php foreach ($table['food_items'] as $index => $item): ?>
                            <tr>
                                <?php if ($index === 0): ?>
                                    <td rowspan="<?= count($table['food_items']) ?>"><?= $table['id_table'] ?></td>
                                    <td rowspan="<?= count($table['food_items']) ?>"><?= $table['print_id'] ?></td>
                                <?php endif; ?>
                                <td><?= $item['food_name'] ?></td>
                                <td><?= intval($item['quantity']) ?></td>
                                <td><?= number_format($item['price'], 0, ',', '.') ?> VND</td>
                                <td>
                                    <form method="POST" action="update_status.php" style="margin: 0;">
                                        <select name="status[<?= $item['id_bill_info'] ?>]" id="status_<?= $item['id_bill_info'] ?>">
                                            <option value="1" <?= $item['status'] == 1 ? 'selected' : '' ?>>Đang chờ</option>
                                            <option value="2" <?= $item['status'] == 2 ? 'selected' : '' ?>>Đang chế biến</option>
                                            <option value="3" <?= $item['status'] == 3 ? 'selected' : '' ?>>Đã phục vụ</option>
                                        </select>
                                </td>
                                <td>
                                    <button type="submit" name="update_status" value="<?= $item['id_bill_info'] ?>" style="background-color: #4CAF50; color: white; border: none; padding: 2px 5px; font-size: 15px; cursor: pointer;">Lưu</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td><?= $table['id_table'] ?></td>
                            <td><?= $table['print_id'] ?></td>
                            <td colspan="5" style="text-align: center;">Khách chưa chọn món</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="qlhoadon" class="tabcontent">
        <form id="searchForm">
            <label for="start_date">Từ ngày :</label>
            <input type="date" id="start_date" name="start_date">

            <label for="end_date">Đến ngày </label>
            <input type="date" id="end_date" name="end_date">
            <input type="text" name="khoa_tim_kiem" placeholder="Nhập từ khóa...">

            <button type="submit">Tìm kiếm</button>
        </form>

        <div id="searchResults"></div>
        <?php include 'quanly_hoadon.php'; ?>

    </div>
    <div id="user" class="tabcontent">
        <?php if (!isset($_SESSION['admin_id'])): ?>
            <h1 style="color: red; text-align: center; margin-top:2rem;">Bạn cần phải <a href="/admin/login.php"> Đăng nhập</a>
                để thực hiện các chức năng trên</h1>
            <div style="font-size: -1rem;text-align: center;margin-top: 1rem;">
                <!-- <a href="/admin/register.php" class="btn btn-primary">Đăng ký</a> -->
            </div>
        <?php else: ?>
            <?php
            include '../include/database.php';
            $admin_id = $_SESSION['admin_id'];

            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['admin_phone'])) {
                $new_phone = trim($_POST['admin_phone']);
                $update_stmt = $conn->prepare("UPDATE tbl_admin SET admin_phone = ? WHERE id_admin = ?");
                $update_stmt->bind_param("si", $new_phone, $admin_id);
                $update_stmt->execute();
            }

            $stmt = $conn->prepare("SELECT id_admin, username_admin, admin_phone, admin_name FROM tbl_admin WHERE id_admin = ?");
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            ?>
            <h1 style="color: #263544; text-align: center;">
                Chào mừng trở lại <?php echo htmlspecialchars($admin['username_admin']); ?>!
            </h1>

            <div class="admin-info-container">
                <h2 style="text-align:left;">Thông tin nhân viên</h2>
                <p><strong>Mã số nhân viên:</strong> <?php echo $admin['id_admin']; ?></p>
                <p><strong>Tên người dùng:</strong> <?php echo $admin['admin_name']; ?></p>
                <p><strong>Số điện thoại hiện tại:</strong> <?php echo htmlspecialchars($admin['admin_phone']); ?></p>

                <form method="POST">
                    <span>Cập nhật số điện thoại</span>
                    <input type="text" name="admin_phone" value="<?php echo htmlspecialchars($admin['admin_phone']); ?>" required>
                    <div class="btn-center" style="text-align: center; margin-top:1rem;">
                        <button type="submit" class="btn-ad btn-update">Cập nhật</button>
                        <a href="/admin/logout.php" class="btn-ad btn-logout">Đăng xuất</a>

                    </div>

                </form>


            </div>
        <?php endif; ?>

    </div>

    <script>
        const islogin = <?php echo isset($_SESSION['admin_id']) ? 'true' : 'false'; ?>;

        function tabclick(evt, tabName) {
            if (!islogin && tabName !== 'user') {
                window.location.href = "login.php";
                return;
            }

            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            tablinks = document.getElementsByClassName("tablinks");

            // Ẩn tất cả các tab
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            // Xóa lớp "active" khỏi tất cả tab
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }

            // Hiển thị tab được chọn
            var selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = "block";
            }

            // Thêm lớp "active" vào tab được chọn
            if (evt && evt.currentTarget) {
                evt.currentTarget.classList.add("active");
            }

            // Nếu là tab 'tb', gọi hàm loadCallList()
            if (tabName === 'tb') {
                loadCallList();
            }
        }

        window.onload = function() {
            var hash = window.location.hash.substring(1);
            var tab = document.getElementById(hash);

            // Nếu có tab trong URL hash, hiển thị tab đó
            if (tab) {
                tabclick(null, hash);
            } else {
                // Nếu không có hash, tự động mở tab mặc định (có id="defaultOpen")
                document.getElementById("defaultOpen").click();
            }
        };


        function opentab(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("loginModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        // document.getElementById("defaultOpen").click();
        var adminId = <?php echo isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 'null'; ?>;

        function handleTableClick(tableId, tableStatus) {
            if (tableStatus === 0) {
                // Nếu bàn trống → tạo mới hóa đơn
                $.ajax({
                    url: "create_bill.php",
                    type: "POST",
                    data: {
                        id_table: tableId,
                        admin_id: adminId
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            loadQRCode(tableId, response.bill_id);
                        } else {
                            $(".tab_QR").html("<p style='color:red;'>Lỗi tạo hóa đơn.</p>");
                        }
                    },
                    error: function() {
                        $(".tab_QR").html("<p style='color:red;'>Không thể tạo hóa đơn.</p>");
                    }
                });
            } else {
                // Nếu bàn đã có khách → tìm hóa đơn chưa thanh toán gần nhất
                $.ajax({
                    url: "get_current_bill.php",
                    type: "POST",
                    data: {
                        id_table: tableId,
                        admin_id: adminId
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            loadQRCode(tableId, response.bill_id);

                        } else {
                            $(".tab_QR").html("<p style='color:red;'>Không tìm thấy hóa đơn chưa thanh toán.</p>");
                        }
                    },
                    error: function() {
                        $(".tab_QR").html("<p style='color:red;'>Bàn đang được đặt. Không thể tạo hóa đơn.</p>");
                    }
                });
            }
        }

        function loadQRCode(tableId, billId) {
            $.ajax({
                url: "print_qr.php",
                type: "GET",
                data: {
                    id_table: tableId,
                    bill_id: billId,
                    admin_id: adminId
                },
                success: function(qrHtml) {
                    $(".tab_QR").html(qrHtml);
                },
                error: function() {
                    $(".tab_QR").html("<p style='color:white;'>Không thể tải mã QR</p>");
                }
            });
        }

        // thông báo gọi nhân viên 
        function loadCallList() {
            $.ajax({
                url: 'load_call_list.php',
                method: 'GET',
                success: function(response) {
                    $('#call-list').html(response);
                },
                error: function() {
                    console.error("Không thể tải danh sách gọi nhân viên.");
                }
            });
        }

        // Hàm xóa yêu cầu gọi nhân viên
        function deletecall(id) {
            if (!confirm("Bạn có chắc muốn xóa yêu cầu này?")) return;

            $.ajax({
                url: 'delete_call.php',
                method: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        $('#call-' + id).remove();
                    } else {
                        alert("Lỗi: " + (response.error || "Không rõ lỗi"));
                    }
                },
                error: function() {
                    alert("Không thể gửi yêu cầu xóa.");
                }
            });
        }
        loadCallList();
        setInterval(loadCallList, 5000);

        // ===================search 
        document.getElementById("searchForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const startDate = document.getElementById("start_date").value;
            const endDate = document.getElementById("end_date").value;
            const keyword = document.querySelector("input[name='khoa_tim_kiem']").value;

            // Debugging log để kiểm tra giá trị gửi đi
            console.log("Start Date:", startDate);
            console.log("End Date:", endDate);
            console.log("Keyword:", keyword);

            fetch("search_bill.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        start_date: startDate,
                        end_date: endDate,
                        khoa_tim_kiem: keyword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const resultsDiv = document.getElementById("searchResults");

                    // Kiểm tra nếu có dữ liệu trả về từ PHP
                    if (data.success && data.bills.length > 0) {
                        let html = "<table border='1' style='width:100%; text-align:center'><tr><th>MHD</th><th>Bàn</th><th>Khách hàng</th><th>Nhân viên</th><th>Ngày vào</th><th>Ngày ra</th><th>Tổng tiền</th><th>Trạng thái</th></tr>";
                        data.bills.forEach(bill => {
                            html += `<tr>
                                        <td>${bill.id_bill}</td>
                                <td>${bill.name_table}</td>
                                <td>${bill.fullname}</td>
                                <td>${bill.admin_name}</td>
                                <td>${bill.date_check_in}</td>
                                <td>${bill.date_check_out ?? '---'}</td>
                                <td>${bill.total_amount}.000đ</td>
                                <td>${bill.status == 1 ? 'Đã thanh toán' : 'Chưa thanh toán'}</td>
                                <td><button class="delete-btn" data-id="${bill.id_bill}" onclick="deleteBill(${bill.id_bill})">X</button></td>

                            </tr>`;
                        });
                        html += "</table>";
                        resultsDiv.innerHTML = html;
                    } else {
                        resultsDiv.innerHTML = "<p style='color:red;'>Không có kết quả tìm kiếm.</p>";
                    }
                })
                .catch(error => {
                    console.error("Lỗi:", error);
                    document.getElementById("searchResults").innerHTML = "<p style='color:red;'>Lỗi kết nối.</p>";
                });
        });
        // xóa
        function deleteBill(id) {
            if (confirm("Bạn có chắc muốn xóa hóa đơn này?")) {
                console.log("Đang gửi yêu cầu xóa hóa đơn với ID:", id); // Log để debug

                fetch('delete_bill.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + id
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log("Phản hồi từ PHP:", data); // Log phản hồi từ PHP
                        alert(data); // Thông báo kết quả xóa
                        location.reload(); // Làm mới lại trang để cập nhật bảng
                    })
                    .catch(error => {
                        console.error('Lỗi khi xóa:', error);
                        alert('Đã xảy ra lỗi khi xóa.');
                    });
            }
        }
    </script>

    <div id="billModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Chi tiết hóa đơn</h2>
            <div id="billDetails"></div>
        </div>
    </div>


</body>

</html>