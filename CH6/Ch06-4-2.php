<?php
function printHeader()
{
    echo "<h3>SID: C113181114</h3>";
    echo "<h3>Name: 郭韋廷</h3>";
    echo "<h3>Ch06-4-2</h3>";
    echo "<hr>";
}

printHeader();

$weight = 75;

$bmi = function ($height) use ($weight) {
    $height = $height / 100.0;
    return $weight / $height / $height;
};

$h = 175;

echo "BMI = " . $bmi($h) . "<br/>";

?>
