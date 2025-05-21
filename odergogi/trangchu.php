<?php
// Kết nối MySQL
$servername = "localhost:3306";
$username = "root";
$password = "";
$database = "db_ordergogi";

$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql_menutop = "SELECT * FROM tbl_food_category";
$result_menutop = $conn->query($sql_menutop);

// Truy vấn lấy món ăn kèm theo loại món ăn
$sql = "SELECT tbl_food.*, tbl_food_category.category_name 
        FROM tbl_food 
        JOIN tbl_food_category ON tbl_food.id_category = tbl_food_category.id_category 
        WHERE tbl_food.status = 1 
        ORDER BY tbl_food.id_category ASC";

// hiển thị tên loại
$result = $conn->query($sql);
$current_category = ""; 



?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="trangchu.css">
</head>
<body>

    <div class="menu">
        <div class="menu-top">
            <ul class="p-2 pb-0 m-0">
                <li class="text-nowrap">
                    <img src="/img/logo_gogi.png" alt="">
                    <span class="menu_span">Bàn x</span>
                    <span class="menu_span"><i class="fa-regular fa-bell"></i> Gọi NV</span>
                </li>
                <li>
                    <span class="menu_span"><i class="fa-solid fa-cart-shopping"></i></span>
                    <span class="menu_span" id="userIcon"><i class="fa-solid fa-user"></i></span>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <button class="btn btn-primary">Đăng nhập</button>
                        <button class="btn btn-primary">Đăng ký</button>
                    </div>
                </li>
            </ul>
            <div class="menu_bottom d-flex align-items-center">
                <ul class="menu_bottom_scroll ds_loai_menu mb-2">
                <?php
                if ($result_menutop->num_rows > 0) {
                     while ($row = $result_menutop->fetch_assoc()) {
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

                <div class="search-icon">
                    <i class="fa-solid fa-magnifying-glass" onclick="openNav()"></i>                
                </div>
            </div>

            <div id="search" class="search">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                <div class="container mt-3">
                    <div class="search-expand">
                    <form action="" method="GET" id="searchForm">
                    <input type="text" name="search" id="searchInput" placeholder="Tìm kiếm...">
                    <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                    </div>
                    <?php 
                        // Kiểm tra nếu có dữ liệu từ ô tìm kiếm
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $search = $_GET['search'];
                            $search = $conn->real_escape_string($search);

                            $sql_search = "SELECT * FROM tbl_food WHERE food_name LIKE '%$search%'";
                            $result_search = $conn->query($sql_search);

                            if ($result_search->num_rows > 0) {
                                while ($row = $result_search->fetch_assoc()) { ?>
                                    <div class='col-6 col-sm-6 col-md-4 col-lg-3 p-1'>
                                        <div class='col_product'>
                                            <img src="<?php echo htmlspecialchars($row['image']); ?>" 
                                                alt="<?php echo htmlspecialchars($row['food_name']); ?>" 
                                                class="img-fluid rounded-2">
                                            <div class="product_content text-start pt-1">
                                                <h6><?php echo htmlspecialchars($row['food_name']); ?></h6>
                                                <div class="product-flex">
                                                    <span class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</span>
                                                    <i class="fa-solid fa-plus plus"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                            } else {
                                echo "<p class='text-center'>Không tìm thấy kết quả.</p>";
                            }
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container d-flex show_product" >
        <div class="row text-center">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
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
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['food_name']); ?>" class="img-fluid rounded-2">
                            <div class="product_content text-start pt-1">
                                <h6><?php echo htmlspecialchars($row['food_name']); ?></h6>
                                <div class="product-flex">
                                    <span class="price"><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</span>
                                    <i class="fa-solid fa-plus plus"></i>
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
        document.getElementById("userIcon").addEventListener("click", function(event){
            var menu = document.getElementById("dropdownMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        });

        function openNav() {
            document.getElementById("search").style.width = "100%";
        }

        function closeNav() {
            document.getElementById("search").style.width = "0";
        }

        var searchForm = document.getElementById("searchForm");
        searchForm.addEventListener("submit", function (event) {
        event.preventDefault(); 

        var searchValue = document.getElementById("searchInput").value;

        if (searchValue.trim() !== "") {
            fetch("trangchu.php?search=" + encodeURIComponent(searchValue))
                .then(response => response.text())
                .then(data => {
                    searchResults.innerHTML = data; // Hiển thị kết quả
                    openNav(); // Giữ khung tìm kiếm mở
                })
                .catch(error => console.error("Lỗi:", error));
        }
    });

    // Giữ trạng thái mở nếu có tìm kiếm trước đó
    if (window.location.search.includes("search=")) {
        openNav();
    }
    </script>

</body>
</html>

<?php
// Đóng kết nối MySQL sau khi hoàn thành
$conn->close();
?>
