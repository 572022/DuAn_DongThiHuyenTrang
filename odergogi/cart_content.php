<?php
if (!isset($_SESSION)) {
    session_start();
}

$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
        ?>
        <div class="list_dish d-flex">
            <img src="uploads/<?php echo $item['image']; ?>" alt="" class="img-fluid rounded-2 me-3" width="60">
            <div class="content_list">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                    <span class="remove-item" data-id="<?php echo $item['id']; ?>" style="cursor: pointer;">&times;</span>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-dark btn-sm px-2 py-0 update-quantity" data-id="<?php echo $item['id']; ?>" data-action="decrease">-</button>
                        <input type="number" class="form-control text-center mx-2 px-2 py-0 quantity" value="<?php echo $item['quantity']; ?>" min="1" data-id="<?php echo $item['id']; ?>" style="width: 60px;">
                        <button class="btn btn-outline-dark btn-sm px-2 py-0 update-quantity" data-id="<?php echo $item['id']; ?>" data-action="increase">+</button>
                    </div>                  
                    <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.') . 'đ'; ?></span>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="empty_dish"><img src="/img/nullcart.jpg" alt="" width="200" class="mt-3"><p>Bạn chưa có món nào</p></div>';
}
?>
<div class="provisional p-3 fixed-bottom">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tạm tính</h5>
        <span class="text-danger fw-bold"><?php echo number_format($total, 0, ',', '.') . 'đ'; ?></span>
    </div>
    <div class="d-flex gap-2 mt-2">
        <button class="btn btn-outline-dark flex-grow-1">Thanh toán</button>
        <button class="btn btn-dark flex-grow-1">Gọi món ngay</button>
    </div>
</div>
