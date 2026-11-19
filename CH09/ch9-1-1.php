
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>ch9-1-1.php</title>
</head>

<body>

<?php

require "about_me.php";

echo "W11 Ex01<br/>";
echo "<hr/>";

// Get file name and actual path
$file = basename($_SERVER["PHP_SELF"], ".php");
$path = realpath($file . ".php");

echo "real path: $path<br/>";

$parts = pathinfo($path);

echo "path: " . $parts["dirname"] . "<br/>";
echo "filename: " . $parts["basename"] . "<br/>";
echo "extension: " . $parts["extension"] . "<br/>";

echo "<hr/>";

// Check whether the file exists
if (file_exists($file . ".php"))
    print "file: $file.php exists<br/>";
else
    print "file: $file.php does not exist<br/>";

// Copy file
if (!copy($file . ".php", $file . ".bak"))
    print "file: $file.php copy to bak failed<br/>";
else
    print "file: $file.php copy to bak successful<br/>";

// Rename file
if (rename($file . ".bak", "test.txt"))
    print "file: $file.bak rename to test.txt successful<br/>";

// Delete file
if (file_exists("test.txt")) {
    unlink("test.txt");
    print "file: test.txt has been deleted<br/>";
}

?>

</body>
</html>
