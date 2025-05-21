<?php
session_start();
include 'include/database.php'; // Kết nối CSDL nếu cần

// Lấy id_table và bill_id từ session
$id_table = isset($_SESSION['id_table']) ? $_SESSION['id_table'] : null;
$id_bill = isset($_SESSION['bill_id']) ? $_SESSION['bill_id'] : null;

// Lấy giỏ hàng từ session tương ứng với bill_id
$cart = [];
if ($id_bill && isset($_SESSION['cart'][$id_bill])) {
    $cart = $_SESSION['cart'][$id_bill];  // Giỏ hàng của bill_id
}

// Truy vấn các món đã order từ CSDL nếu có id_bill
$cart_odder = [];
if ($id_bill > 0) {
    $sql = "SELECT bi.id_food, f.food_name, bi.quantity, bi.price, bi.status, bi.id_bill
            FROM tbl_bill_info bi
            JOIN tbl_food f ON bi.id_food = f.id_food
            WHERE bi.id_bill = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_bill);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cart_odder[] = $row;
    }

    $stmt->close();
}

$conn->close();
// print_r($_SESSION['cart']);
// print_r($cart_odder);
// print_r($_SESSION['cart'][$id_bill]);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<style>
    .tabcontent {
        display: none;
    }

    .tab {
        display: flex;
    }

    .tab button {
        width: 100%;
        padding: 0.5rem 1rem;
        border: none;
    }

    .active {
        color: red;
        border-bottom: 2px solid red !important;
    }

    .provisional {
        width: 100%;

    }

    .list_dish {
        background: #faf4f4;
        margin: 0.5rem 1.5rem;
        box-sizing: border-box;
        padding: 0.5rem;
        border-radius: 10px;
        align-items: center;
        box-shadow: 0px 2px 8px #737373;
    }

    .content_list {
        flex: 1;
    }

    /* order */
    .tip {
        background-color: antiquewhite;
        padding: 0.5rem 1rem;
        margin: 10px 0px;
        align-items: center;
        justify-content: space-between;
    }

    .tip i {
        color: #3fe116;
    }

    .tip button {
        background-color: #3fe116;
        border: none;
        white-space: nowrap;
        color: #fff;
        border-radius: 5px;
        padding: 0.5rem 1rem;
    }

    /*  danh sách order css */
    .list_order_flex {
        display: flex;
        justify-content: space-evenly;
        margin: 0.5rem;
        padding-bottom: .2rem;
        border-bottom: 2px solid rgba(246, 238, 238, 0.89);
    }

    .list_order_flex>span {
        margin-left: .4rem;
    }

    /* tổng tiền */
    .sum {
        background: #141111;
        color: #fff;
    }

    .sum button {
        border: none;
        background-color: #efe7e7;
        border-radius: .5rem;
        padding: .5rem 1rem;
    }

    @media (min-width: 992px) {
        .provisional {
            width: 70%;
            margin: 0px auto;
        }

        .list_dish {
            width: 50%;
            margin: 1rem auto;
        }

        .list_dish img {
            width: 12%;
        }

    }
</style>

<body>
    <div class="container w-sm-100 w-lg-40">
        <div class="tab justify-content-evenly p-2 fixed">
            <!-- <span><i class="fa-solid fa-x"></i></span> -->
            <span>Gọi món của bạn</span>
            <a href="order_menu.php?id_table=<?php echo $id_table; ?>&bill_id=<?php echo $id_bill; ?>">
                <span>Gọi thêm</span>
            </a>
        </div>
        <div class="tab mb-3">
            <button class="tablinks active" onclick="openl(event, 'cart')">Giỏ đồ ăn</button>
            <button class="tablinks" onclick="openl(event, 'order')">Món đã gọi</button>
        </div>
        <!-- giỏ đò ăn -->
        <div class="content-cart-order text-center">
            <div id="cart" class="tabcontent" style="display: block;">
                <?php if (empty($cart)) : ?>
                    <div class="empty_dish" style="text-align: center;">
                        <img src="/img/nullcart.jpg" alt="Giỏ hàng trống" width="200" class="mt-3">
                        <p>Bạn chưa có món nào</p>
                    </div>
                <?php else : ?>
                    <!-- Danh sách giỏ hàng -->
                    <div class="list_dish">
                        <?php foreach ($cart as $item) : ?>
                            <div class="list_order_flex d-flex list_order_dish">
                                <img src="/uploads/<?= $item['image'] ?>" alt="<?= $item['food_name'] ?>" class="img-fluid rounded-2 me-3" width="60">
                                <div class="content_list flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold" id="food_name"><?= $item['food_name'] ?></span>
                                        <span style="display:none" id="id_name"><?= $item['id_food'] ?></span>
                                        <span style="display:none" id="price"><?= $item['price'] ?></span>
                                        <!-- <span id="id_bill"><?= $id_bill ?></span> -->
                                        <!-- <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?= $item['id_food'] ?>)">x</button> -->
                                        <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?= $item['id_food'] ?>, <?= $id_bill ?>)">x</button>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-outline-dark btn-sm px-2 py-0"
                                                onclick="updateQuantity(<?= $item['id_food'] ?>, -1, <?= $id_bill ?>)">-</button>

                                            <input
                                                id="quantity-<?= $item['id_food'] ?>"
                                                type="number"
                                                class="form-control text-center mx-2 px-2 py-0"
                                                value="<?= $item['quantity'] ?>"
                                                min="1"
                                                style="width: 60px;"
                                                oninput="validateQuantity(<?= $item['id_food'] ?>, <?= $id_bill ?>)">

                                            <button class="btn btn-outline-dark btn-sm px-2 py-0"
                                                onclick="updateQuantity(<?= $item['id_food'] ?>, 1, <?= $id_bill ?>)">+</button>
                                        </div>

                                        <span><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

            </div>
        <?php endif; ?>
        <!-- Tạm tính -->
        <div class="provisional p-3 fixed-bottom" style="display: block;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tạm tính</h5>
                <span id="total_price" class="text-danger fw-bold">
                    <?= number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)), 0, ',', '.') ?>đ
                </span>
            </div>
            <div class="d-flex gap-2 mt-2">
                <button id="btnCheckout" class="btn btn-outline-dark flex-grow-1">Thanh toán</button>
                <button id="now_order" class="btn btn-dark flex-grow-1">Gọi món ngay</button>
            </div>
        </div>
        </div>
        <!-- món đã gọi -->
        <div id="order" class="tabcontent">
            <h3>Món đã gọi</h3>
            <div class="tip d-flex gap-3">
                <i class="fa-solid fa-gift"></i>
                <p>Tiếp ngàn động lực cho nhân viên</p>
                <button>Tip ngay</button>
            </div>
            <!-- danh sách món đã gọi -->
            <?php if (empty($cart_odder)) : ?>
                <!-- Giỏ hàng trống -->
                <div class="empty_dish" style="text-align: center;">
                    <img src="/img/nullcart.jpg" alt="Giỏ hàng trống" width="200" class="mt-3">
                    <p>Bạn chưa có món nào</p>
                </div>
            <?php else : ?>
                <div class="list_order">
                    <?php foreach ($cart_odder as $item) : ?>
                        <div class="list_order_flex">
                            <!-- <span class="fw-bold"><?= intval($item['id_bill']) ?></span> -->

                            <span class="fw-bold"><?= intval($item['quantity']) ?></span>
                            <span class="fw-bold text-wrap"><?= $item['food_name'] ?></span>
                            <?php
                            $statusText = [
                                1 => 'Đang chờ',
                                2 => 'Đang chế biến',
                                3 => 'Đã phục vụ'
                            ];
                            ?>

                            <span class="text-muted text-nowrap"><?= $statusText[$item['status']] ?? 'Không rõ' ?></span>
                            <span><?= number_format($item['price'], 0, ',', '.') ?>đ</span>

                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- tổng tiền -->
                <div class="provisional p-3 fixed-bottom sum">

                    <div class="d-flex justify-content-between align-items-center ">
                        <span class="fw-bold" id="price_pay">
                            <?= number_format(array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart_odder)), 0, ',', '.') ?>đ
                        </span>
                        <button id="pay">Thanh toán ngay</button>
                    </div>
                </div>

        </div>
    <?php endif; ?>

    </div>
    </div>
    <script>
        function openl(evt, id) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }

            document.getElementById(id).style.display = "block";
            evt.currentTarget.className += " active";
        }

        function updateQuantity(id, change, bill_id) {
            let quantityInput = document.getElementById(`quantity-${id}`);
            let newQuantity = parseInt(quantityInput.value) + change;
            if (newQuantity < 1) return;

            quantityInput.value = newQuantity;

            fetch("updateQuantity.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        bill_id: bill_id, // <- Thêm dòng này
                        id_food: id,
                        quantity: newQuantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Cập nhật số lượng thành công");
                        window.location.reload();
                    } else {
                        alert("Lỗi: " + data.error);
                    }
                })
                .catch(error => console.error("Lỗi:", error));
        }

        function sendQuantityToServer(id, quantity) {
            fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_food: id,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("total_price").textContent = new Intl.NumberFormat('vi-VN').format(data.total) + "đ";
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        }


        function removeFromCart(id_food, id_bill) {
            fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_food: id_food,
                        id_bill: id_bill
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Tải lại giỏ hàng sau khi xóa
                        location.reload();
                    } else {
                        alert(data.error); // Nếu có lỗi
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        }




        let orderItems = [];
        document.querySelectorAll(".list_order_dish").forEach((item) => {

            let nameFoodElement = item.querySelector("#food_name");

            if (nameFoodElement) {
                let nameFood = nameFoodElement.textContent;
                console.log("Tên món ăn:", nameFood);
            } else {
                console.error("Không tìm thấy #food_name trong item:", item);
            }
            let nameFood = item.querySelector(`#food_name`).textContent;
            let idFoodValue = item.querySelector(`#id_name`).textContent;
            let quantity = document.getElementById(`quantity-${idFoodValue}`).value;
            let price = item.querySelector(`#price`).textContent;
            console.log(`ID: ${idFoodValue}, Tên món: ${nameFood}, Số lượng: ${quantity}, Giá: ${price}`);
            orderItems.push({
                id_food: idFoodValue,
                quantity: parseInt(quantity),
                price: parseInt(price),
                status: status
                // idbill:idbill
            });
        });

        // let id_table = 2; // Thay bằng ID bàn thực tế
        let id_account = 2; // Thay bằng ID tài khoản thực tế (nếu có đăng nhập)
        let idBill = <?php echo json_encode($id_bill, JSON_HEX_TAG); ?>;
        let id_table = <?php echo json_encode($id_table, JSON_HEX_TAG); ?>;
        console.log("idBill:", idBill);
        console.log("table:", id_table);
        // =========thanh toán 2========
        document.addEventListener("DOMContentLoaded", function() {
            document.addEventListener("click", function(event) {
                let idBill = <?php echo json_encode($id_bill, JSON_HEX_TAG); ?>;

                if (event.target && event.target.id === "pay") {
                    if (idBill === 0) {
                        alert("Không tìm thấy hóa đơn để thanh toán!");
                        return;
                    }

                    fetch("pay_bill.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                id_bill: idBill
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Server response:", data);

                            if (data.success) {
                                alert("Thanh toán thành công!");
                                window.location.href = "success.php";

                                // Xóa danh sách đơn hàng nếu có
                                let orderList = document.querySelector(".list_order");
                                if (orderList) {
                                    orderList.style.display = "none";
                                }

                                // Chuyển hướng đến trang thành công
                                window.location.href = "success.php";

                            } else {
                                alert("Lỗi: " + data.error);
                            }
                        })
                        .catch(error => {
                            console.error("Lỗi:", error);
                            // BỎ QUA LỖI & VẪN CHUYỂN HƯỚNG ĐẾN success.php
                            window.location.href = "success.php";
                        });
                }
            });

            // thanh toán 1 + tạo hóa đơn
            let btnCheckout = document.getElementById("btnCheckout");
            if (btnCheckout) {
                btnCheckout.addEventListener("click", function() {
                    let totalAmount = parseFloat(document.getElementById("total_price")?.innerText.replace('đ', '').replace(',', '')) || 0;
                    let idBill = <?php echo json_encode($id_bill, JSON_HEX_TAG); ?>;
                    let id_table = <?php echo json_encode($id_table, JSON_HEX_TAG); ?>;
                    // alert(idBill);
                    fetch("order_new.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                id_table: typeof id_table !== "undefined" ? id_table : null,
                                id_account: typeof id_account !== "undefined" ? id_account : null,
                                total_amount: totalAmount,
                                orderItems: typeof orderItems !== "undefined" ? orderItems : [],
                                id_billnew: idBill
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Phản hồi từ server:", data);

                            if (data.success) {
                                alert("Thanh toán hóa đơn thành công! Mã hóa đơn: " + data.id_bill);

                                let orderTab = document.querySelector("button[onclick=\"openl(event, 'order')\"]");
                                setTimeout(() => {
                                    if (orderTab) {
                                        orderTab.classList.add("active");
                                        openl({
                                            currentTarget: orderTab
                                        }, 'order');
                                    } else {
                                        console.error("Không tìm thấy tab Order!");
                                    }
                                }, 5000);
                            } else {
                                alert("Lỗi: " + data.error);
                            }
                        })
                        .catch(error => console.error("Lỗi:", error));
                });
            } else {
                console.error("Không tìm thấy nút btnCheckout!");
            }

            // Bắt sự kiện cho nút "Order Ngay"
            let nowOrderButton = document.getElementById("now_order");
            let totalAmount = parseFloat(document.getElementById("total_price")?.innerText.replace('đ', '').replace(',', '')) || 0;
            // alert(totalAmount);
            let idBill = <?php echo json_encode($id_bill, JSON_HEX_TAG); ?>;
            let id_table = <?php echo json_encode($id_table, JSON_HEX_TAG); ?>;
            if (nowOrderButton) {
                nowOrderButton.addEventListener("click", function() {
                    alert(idBill);
                    fetch("order_new.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                id_table: typeof id_table !== "undefined" ? id_table : null,
                                id_account: typeof id_account !== "undefined" ? id_account : null,
                                total_amount: totalAmount,
                                orderItems: typeof orderItems !== "undefined" ? orderItems : [],
                                id_billnew: idBill
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Đã order thành công" + data.id_bill);
                                window.location.href = `order_menu.php?id_table=${id_table}&bill_id=${idBill}`;
                            } else {
                                alert("Lỗi: " + data.error);
                            }
                        })
                        .catch(error => console.error("Lỗi:", error));
                });
            } else {
                console.error("Không tìm thấy nút now_order!");
            }

        });
    </script>
</body>

</html>