<?php
echo "# SID: C113181114<br>";
echo "# Name: 郭韋廷<br>";
echo "EX02<br>";
?>
<hr>

<?php

$total = 0;

for ($i = 1; $i <= 10; $i++) {
    echo " | " . $i;
    $total += $i;
}

echo "<HR>";
echo "總和：" . $total;

?>