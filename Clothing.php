<?php
  include 'header.php';
  include 'lib/connection.php'; // Make sure this file includes your database connection logic.

  // Query to fetch products from the database
  $sql = "SELECT * FROM product"; // Make sure this query is correct
  $result = mysqli_query($conn, $sql); // Execute the query and store the result

  if (!$result) {
    // If the query fails, output an error and exit
    die('Query failed: ' . mysqli_error($conn));
  }
?>

<div class="container">
    <h5>CLOTHING</h5>
    <div class="container">
        <div class="row">
            <?php
            // Check if there are any products
            if (mysqli_num_rows($result) > 0) {
                // Loop through each product
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="product-item">
                            <a href="product_detail.php?id=<?php echo $row['p_id']; ?>" class="product-link">
                                <div class="product-image">
                                    <img src="admin/product_img/<?php echo $row['imgname']; ?>" alt="<?php echo $row['name']; ?>">
                                </div>
                                <div class="product-info">
                                    <h6><?php echo $row["name"]; ?></h6>
                                    <span class="price"><?php echo number_format($row["Price"], 2); ?></span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "No products available.";
            }
            ?>
        </div>
    </div>
</div>

<?php
  include 'footer.php';
?>
