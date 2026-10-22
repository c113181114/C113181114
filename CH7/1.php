<?php
include "about_me.php";

echo "EX02<br/>";

$total = 0;
$scores = array(22, 16, 30, 24);

foreach ($scores as $element) {
    echo "element: $element <br/>";
    $total = $total + $element;
}

echo "Total:" . $total;

print_r($scores);
?>