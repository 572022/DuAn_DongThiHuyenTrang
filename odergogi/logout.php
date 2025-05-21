<?php
session_start();
$id_table = isset($_SESSION['id_table']) ? $_SESSION['id_table'] : null;
$bill_id = isset($_SESSION['bill_id']) ? $_SESSION['bill_id'] : null;
unset($_SESSION['user_id']);
unset($_SESSION['username']);
header("Location: order_menu.php?id_table=$id_table&bill_id=$bill_id");
exit();
?>
