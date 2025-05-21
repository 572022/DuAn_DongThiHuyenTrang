<?php
session_start();
include 'include/database.php';

$_SESSION['id_table'] = isset($_GET['id_table']) ? $_GET['id_table'] : null;
$_SESSION['bill_id'] = isset($_GET['bill_id']) ? $_GET['bill_id'] : null;

$bill_id = isset($_SESSION['bill_id']) ? intval($_SESSION['bill_id']) : 0;
// kiểm tra đã thanh toán chưa
if ($bill_id) {
    $query = "SELECT status FROM tbl_bill WHERE id_bill = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra trạng thái
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['status'] === 1) {
            // Nếu đã thanh toán thì chuyển đến trang success
            header("Location: success.php");
            exit();
        }
    }
}

$cart_count = 0;
if (isset($_SESSION['cart'][$bill_id])) {
    foreach ($_SESSION['cart'][$bill_id] as $item) {
        $cart_count += $item['quantity'];
    }
}
$sql = "SELECT * FROM tbl_food_category";
$result = mysqli_query($conn, $sql);
$query_foods = "SELECT tbl_food.*, tbl_food_category.category_name 
        FROM tbl_food 
        JOIN tbl_food_category ON tbl_food.id_category = tbl_food_category.id_category 
        WHERE tbl_food.status = 1 
        ORDER BY tbl_food.id_category ASC";
$result_foods = mysqli_query($conn, $query_foods);
$current_category = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style/trangchu.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="menu">
        <div class="menu-top">
            <ul class="p-2 pb-0 m-0">
                <li class="text-nowrap">
                    <img src="img/logo_gogi.png" alt="">
                    <span class="menu_span">Bàn
                        <?php echo $_SESSION['id_table'] ?>
                    </span>
                    <span class="menu_span" id="call-button" onclick='callStaff("<?php echo $_SESSION['id_table'] ?>", "<?php echo $_SESSION['bill_id'] ?>")'>
                        <i class="fa-regular fa-bell"></i> Gọi NV
                    </span>

                </li>
                <li class="">
                    <span class="menu_span">
                        <a href="/cart.php"> <i class="fa-solid fa-cart-shopping"></i></a>
                        <span id="cart_count" class="badge bg-danger"><?php echo $cart_count; ?></span>

                    </span>
                    <span class="menu_span" >
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="user_info.php?id_table=<?= $_SESSION['id_table'] ?>&bill_id=<?= $_SESSION['bill_id'] ?>"
                                title="Xem thông tin cá nhân" style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                        <?php else: ?>
                            <span id="userIcon">
                                    <i class="fa-solid fa-user"></i>
                            </span>
                           
                        <?php endif; ?>
                    </span>

                    <div class="dropdown-menu" id="dropdownMenu">
                        <button class="btn btn-primary "><a href="/login.php">Đăng nhập</a> </button>
                        <button class="btn btn-primary "><a href="/register.php"> Đăng ký</a></button>
                    </div>

                </li>
            </ul>
            <!-- phần search món ăn -->
            <div class="menu_bottom d-flex align-items-center">
                <ul class="menu_bottom_scroll ds_loai_menu mb-2">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<li class="menu_span text-nowrap"> 
                            <a href="#' . htmlspecialchars($row["id_category"]) . '">'
                                . htmlspecialchars($row["category_name"]) .
                                '</a>
                        </li>';
                        }
                    } else {
                        echo '<li class="menu_span text-nowrap">Không có danh mục</li>';
                    }
                    ?>
                </ul>
                <div class="search-icon" >
                    <i class="fa-solid fa-magnifying-glass" onclick="openNav()"></i>
                </div>
            </div>

            <div id="search" class="search">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

                <div class="container mt-3">
                    <div class="search-expand">
                        <input type="text" id="searchInput" placeholder="Tìm kiếm...">
                            <button onclick="searchsp()"><i class="fa fa-search"></i></button>
                            <div id="kq_search" class="row mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- ==phần hiển thị sản phẩm -->
    <div class="container d-flex show_product">
        <div class="row text-center">
            <?php
            if ($result_foods->num_rows > 0) {
                while ($row = $result_foods->fetch_assoc()) {
                    if ($current_category != $row["category_name"]) {
                        $current_category = $row["category_name"];
                        echo '<li class="menu_span text-nowrap mt-4">
                    <a name="' . htmlspecialchars($row["id_category"]) . '">'
                            . htmlspecialchars($row["category_name"]) .
                            '</a>
                </li>';
                    }
            ?>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-3 p-1">
                        <div class="col_product">
                            <img src="./uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>" class="img-fluid rounded-2">
                            <div class="product_content text-start pt-1">
                                <h6><?php echo htmlspecialchars($row['food_name']); ?></h6>
                                <div class="product-flex">
                                    <span class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</span>
                                    <i class="fa-solid fa-plus plus" data-id="<?php echo $row['id_food']; ?>"></i>

                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p class='text-center'>Không có sản phẩm nào!</p>";
            }
            ?>
        </div>
    </div>

    <script>

document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("plus") || event.target.classList.contains("plus_search")) {
            const id_food = event.target.getAttribute("data-id");

            fetch("add_to_cart.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id_food: id_food })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    const cartCountElement = document.getElementById("cart_count");
                    let totalItems = 0;
                    data.cart.forEach(item => totalItems += item.quantity);
                    cartCountElement.textContent = totalItems;
                } else {
                    alert("Lỗi: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});


// ========= Phần tìm kiếm sản phẩm =========
function searchsp() {
    const keyword = document.getElementById('searchInput').value;
    if (!keyword) return;

    fetch('search_sp.php?q=' + encodeURIComponent(keyword))
        .then(res => res.text())
        .then(html => {
            document.getElementById('kq_search').innerHTML = html;
            // Không cần gắn lại sự kiện click vì đã dùng event delegation ở trên
        });
}


// ==================icon user
        document.getElementById("userIcon").addEventListener("click", function(event) {
            var menu = document.getElementById("dropdownMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        });

        function openNav() {
            document.getElementById("search").style.width = "100%";
        }

        function closeNav() {
            document.getElementById("search").style.width = "0";
        }

        // Giữ trạng thái mở nếu có tìm kiếm trước đó
        if (window.location.search.includes("search=")) {
            openNav();
        }

        // gọi nhân viên
        function callStaff(tableId, billId) {
            console.log("Gửi id_table: " + tableId);
            console.log("Gửi bill_id: " + billId);

            $.ajax({
                url: 'call_staff.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    id_table: tableId,
                    bill_id: billId
                }),
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $('#call-button').addClass('called');
                        alert('Nhân viên đã nhận yêu cầu.');
                    } else {
                        alert('Lỗi khi gọi nhân viên: ' + (response.error || 'Không có thông tin lỗi.'));
                    }
                },
                error: function() {
                    alert('Không thể gửi yêu cầu gọi nhân viên.');
                }
            });
        }
    </script>
</body>

</html>