<?php
  include 'header.php';
  include 'lib/connection.php';

  // Query to fetch all products
  $sql = "SELECT * FROM product";
  $result = $conn->query($sql);

  // Check if user is logged in and trying to add to cart
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
        $message[] = 'Product already added to cart';
      } else {
        // Insert product into cart
        $insert_product = mysqli_query($conn, "INSERT INTO `cart`(userid, productid, name, quantity, price) VALUES('$user_id', '$product_id', '$product_name', '$product_quantity', '$product_price')");
        $message[] = 'Product added to cart successfully';
        header('Location: index.php'); // Refresh the page after adding the product
        exit();
      }
    } else {
      // Redirect to login if the user is not logged in
      header("Location: login.php");
      exit();
    }
  }
?>

<!-- Banner section -->
<div class="banner">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div class="banner-text">
          <p class="bt1"></p>
          <p class="bt2"><span class="bt3">Vogue</span>Vibes</p>
          <p class="bt2"><span class="bt3"></span>Be Bold,</p>
          <p class="bt2"><span class="bt3"></span>Be Vogue.</p>
        </div>
      </div>
      <div class="col-md-6">
        <img src="" class="img-fluid">
      </div>
    </div>
  </div>
</div>

<!-- Top sell section -->
<section>
  <div class="container">
    <div class="topsell-head">
      <div class="row">
        <div class="col-md-12 text-center">
          <img src="img/mark.png">
          <h4>All Products</h4>
          <p>All you need is here</p>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row" >
      <?php
        if (mysqli_num_rows($result) > 0) {
          // Loop through products
          while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
              <div  class="col-md-3 col-sm-6 col-6 m-auto py-3">
                <div >
                  <img src="admin/product_img/<?php echo $row['imgname']; ?>" >
                </div>
                <div>
                  <h6><?php echo $row["name"] ?></h6>
                  <span><?php echo $row["Price"] ?></span>
                  <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
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
</section>

<!-- Logo section -->
<div class="logo5">
  <div class="container">
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-2 text-center">
        <img src="img/logo1.png">
      </div>
      <div class="col-md-2 text-center">
        <img src="img/logo2.png">
      </div>
      <div class="col-md-2 text-center">
        <img src="img/logo3.png">
      </div>
      <div class="col-md-2 text-center">
        <img src="img/logo4.png">
      </div>
      <div class="col-md-2 text-center">
        <img src="img/logo5.png">
      </div>
      <div class="col-md-1"></div>
    </div>
  </div>
</div>

<!-- Welcome section -->
<div class="welcome">
  <div class="container">
    <div class="row">
      <div class="col-md-12 col-lg-6 col-sm-12">
        <span class="welcometitle">Recommendation</span>
        <img src="img/titleful.png">
        <img src="img/titleline.png" class="titleline">

        <div class="row" id="wel1">
          <div class="col-md-2 col-lg-2 col-2">
            <img src="img/w1.png" class="w" class="img-fluid">
          </div>
          <div class="col-md-10 col-lg-10 col-10">
            <h6 class="wh">24x7 online free support</h6>
         
          </div>
        </div>
      </div>
      <div class="col-md-12 col-lg-6 col-sm-12">
        <img src="img/Testimonial.png" class="img-fluid">
      </div>
    </div>
  </div>
</div>

<?php
  include 'footer/footer.php';
?>
