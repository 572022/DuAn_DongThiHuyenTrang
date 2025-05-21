<?php
session_start();
include 'include/database.php'; // Kết nối CSDL

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

$results = [];

if (!empty($keyword)) {
    $sql = "SELECT * FROM tbl_food WHERE food_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $likeKeyword = '%' . $keyword . '%';
    $stmt->bind_param("s", $likeKeyword);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }

    $stmt->close();
}

// Trả về HTML các món ăn (giống như giao diện bạn dùng)
foreach ($results as $row) {
    echo '
        
    <div class="col-6 col-sm-6 col-md-4 col-lg-3 p-1">
        <div class="col_product">
            <img src="./uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['food_name']) . '" class="img-fluid rounded-2">
            <div class="product_content text-start pt-1">
                <h6>' . htmlspecialchars($row['food_name']) . '</h6>
                <div class="product-flex">
                    <span class="price">' . number_format($row['price'], 0, ',', '.') . 'đ</span>
                    <i class="fa-solid fa-plus plus_search" data-id="' . $row['id_food'] . '"></i>
                </div>
            </div>
        </div>
    </div>

    ';
    
}

if (empty($results)) {
    echo '<div class="col-12 text-center text-muted">Không tìm thấy món ăn phù hợp</div>';
}
?>
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".plus").forEach(button => {
                button.addEventListener("click", function() {
                    let id_food = this.getAttribute("data-id");
                    fetch("add_to_cart.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                id_food: id_food
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                let cartCountElement = document.getElementById("cart_count");
                                let totalItems = 0;
                                data.cart.forEach(item => totalItems += item.quantity);
                                cartCountElement.textContent = totalItems; // Cập nhật số lượng hiển thị
                            } else {
                                alert("Lỗi: " + data.message);
                            }
                        })
                        .catch(error => console.error("Error:", error));
                });
            });
        });
</script> -->
