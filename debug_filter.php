<?php
include 'lib/connection.php';

// Check available categories
$sql = "SELECT DISTINCT category FROM product ORDER BY category";
$result = mysqli_query($conn, $sql);
echo "Available categories:\n";
$categories = [];
while($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['category'];
    echo $row['category'] . "\n";
}

// Test each category
foreach($categories as $category) {
    echo "\nTesting filter with '$category':\n";
    $sql = "SELECT * FROM product WHERE category = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    echo "Found " . mysqli_num_rows($result) . " products\n";
}

// Test empty category (all products)
echo "\nTesting filter with empty category (all products):\n";
$sql = "SELECT * FROM product";
$result = mysqli_query($conn, $sql);
echo "Found " . mysqli_num_rows($result) . " products total\n";

mysqli_close($conn);
?>
