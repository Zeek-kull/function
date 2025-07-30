<?php
  include 'header.php';
  include 'lib/connection.php'; // Make sure this file includes your database connection logic.

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['auth']) && $_SESSION['auth'] == 1) {
        $user_id = $_SESSION['userid'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_id = $_POST['product_id'];
        $product_quantity = 1;

        // Check if the product is already in the cart
        $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE productid = '$product_id' AND userid = '$user_id'");
        if (mysqli_num_rows($select_cart) > 0) {
            // Product already in cart, increment quantity
            $cart_row = mysqli_fetch_assoc($select_cart);
            $new_quantity = $cart_row['quantity'] + 1;
            mysqli_query($conn, "UPDATE `cart` SET quantity = '$new_quantity' WHERE id = '{$cart_row['id']}'");
            header("Location: Clothing.php");
            exit();
        } else {
            $insert_product = mysqli_query($conn, "INSERT INTO `cart`(userid, productid, name, quantity, price) VALUES('$user_id', '$product_id', '$product_name', '$product_quantity', '$product_price')");
            header("Location: Clothing.php");
            exit();
        }
    } else {
        // Redirect to login if the user is not logged in
        header("Location: login.php");
        exit();
    }
}

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
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="col-md-3 col-sm-6 col-6">
                            <div>
                                <img src="admin/product_img/<?php echo $row['imgname']; ?>" alt="<?php echo $row['name']; ?>">
                            </div>
                            <div>
                                <h6><?php echo $row["name"]; ?></h6>
                                <span><?php echo number_format($row["Price"], 2); ?></span> <!-- Remove the dollar sign -->
                                <input type="hidden" name="product_id" value="<?php echo $row['p_id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
                                <input type="hidden" name="product_price" value="<?php echo $row['Price']; ?>">

                                <!-- Only show the 'Add to Cart' button if the user is logged in -->
                                <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] == 1): ?>
                                    <input type="submit" class="btn btn-primary" value="Add to Cart" name="add_to_cart">
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
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
