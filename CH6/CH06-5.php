<?php
include "about_me.php";
echo "EX01<br/>";

include "CH06-5-1.inc";
require_once("CH06-5-2.inc");

$amount = 1000;
$rate = 28.1;

// 呼叫函數
$result = rateExchange($amount, $rate);
print "1000美金兌換新台幣=" . $result . "元<br/>";

require("CH06-5-1.inc");

$file = "CH06-5-1.inc";
require $file;
?>