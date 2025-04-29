<?php
echo "<h1>Debug Information</h1>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "alogin.php exists: " . (file_exists('alogin.php') ? 'Yes' : 'No') . "\n";
echo "alogin.php readable: " . (is_readable('alogin.php') ? 'Yes' : 'No') . "\n";
echo "</pre>";
?>