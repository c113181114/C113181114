<?php

function printHeader()
{
    echo "<h3>SID: C113181114</h3>";
    echo "<h3>Name: 郭韋廷</h3>";   
    echo "<h3>EX02</h3>"; 
    echo "<hr>";
}

function rateExchange($amount, $rate)
{
    $result = $amount * $rate;
    return $result;
}

$rate = rateExchange(1000, 32.5);

printHeader();

echo "Money: " . $rate;

?>
```