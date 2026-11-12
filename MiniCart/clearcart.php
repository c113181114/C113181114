<?php
session_start();

unset($_SESSION["Cart"]);
unset($_SESSION["Id"]);
unset($_SESSION["Name"]);
unset($_SESSION["Price"]);
unset($_SESSION["Quantity"]);

header("Location: shoppingcart.php");
exit();
?>