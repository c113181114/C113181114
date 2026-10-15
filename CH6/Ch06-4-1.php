<?php
function printHeader()
{
    echo "<h3>SID: C113181114</h3>";
    echo "<h3>Name: 郭韋廷</h3>";
    echo "<h3>Ch06-4-1</h3>";
    echo "<hr>";
}

function callFunc1(Closure $closure)
{
    $closure();
}

function callFunc2(callable $callback)
{
    $callback();
}

$hello = function () {
    echo "Hello, World!<br/>";
};

function hi()
{
    echo "Hi, World!<br/>";
}

printHeader();

// callFunc1("hi");

callFunc1($hello);

callFunc2("hi");

callFunc2($hello);

?>
