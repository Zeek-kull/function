<?php
  include 'header.php';
  include 'lib/connection.php';

  // Check if product ID is provided
  if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // Fetch product details
    $sql = "SELECT * FROM product WHERE p_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
      $product = $result->fetch_assoc();
    } else {
      echo "<script>alert('Product not found!'); window.location.href='index.php';</script>";
      exit();
    }
  } else {
    echo "<script>window.location.href='index.php';</script>";
    exit();
  }

  // Handle add to cart from product detail page
  if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['auth']) && $_SESSION['auth'] == 1) { 
      $user_id = $_SESSION['userid'];
      $product_name = $_POST['product_name'];
      $product_price = $_POST['product_price'];
      $product_id = $_POST['product_id'];
      $product_quantity = $_POST['quantity'];

      // Check if the product is already in the cart
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE productid = '$product_id' AND userid = '$user_id'");
      if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'Product already added to cart';
      } else {
        // Insert product into cart
        $insert_product = mysqli_query($conn, "INSERT INTO `cart`(userid, productid, name, quantity, price) VALUES('$user_id', '$product_id', '$product_name', '$product_quantity', '$product_price')");
        $message[] = 'Product added to cart successfully';
        header('Location: product_detail.php?id=' . $product_id);
        exit();
      }
    } else {
      header("Location: login.php");
      exit();
    }
  }
?>

<!-- Product Detail Section -->
<div class="container mt-5">
  <div class="row">
    <div class="col-md-6">
      <img src="admin/product_img/<?php echo $product['imgname']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>">
    </div>
    <div class="col-md-6">
      <h2><?php echo $product['name']; ?></h2>
      <p class="text-muted">Product ID: <?php echo $product['p_id']; ?></p>
      <h3 class="text-primary">$<?php echo $product['Price']; ?></h3>
      <p><?php echo $product['description'] ?? 'No description available.'; ?></p>
      
      <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $product_id; ?>" method="post">
        <input type="hidden" name="product_id" value="<?php echo $product['p_id']; ?>">
        <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
        <input type="hidden" name="product_price" value="<?php echo $product['Price']; ?>">
        
        <div class="form-group">
          <label for="quantity">Quantity:</label>
          <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="10">
        </div>
        
        <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] == 1): ?>
          <button type="submit" class="btn btn-primary" name="add_to_cart">Add to Cart</button>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">Login to Add to Cart</a>
        <?php endif; ?>
      </form>
    </div>
  </div>
</div>

<!-- Related Products Section -->
<div class="container mt-5">
  <h3>Related Products</h3>
  <div class="row">
    <?php
      // Fetch related products (same category or random)
      $related_sql = "SELECT * FROM product WHERE p_id != ? ORDER BY RAND() LIMIT 4";
      $stmt = $conn->prepare($related_sql);
      $stmt->bind_param("i", $product_id);
      $stmt->execute();
      $related_result = $stmt->get_result();
      
      while ($related = $related_result->fetch_assoc()) {
    ?>
    <div class="col-md-3">
      <a href="product_detail.php?id=<?php echo $related['p_id']; ?>" class="text-decoration-none">
        <div class="card">
          <img src="admin/product_img/<?php echo $related['imgname']; ?>" class="card-img-top" alt="<?php echo $related['name']; ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo $related['name']; ?></h5>
            <p class="card-text">$<?php echo $related['Price']; ?></p>
          </div>
        </div>
      </a>
    </div>
    <?php } ?>
  </div>
</div>

<?php
  include 'footer/footer.php';
?>
