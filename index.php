<?php
  include 'header.php';
  include 'lib/connection.php';

  // Query to fetch all products
  $sql = "SELECT * FROM product";
  $result = $conn->query($sql);
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
    <div class="row">
      <?php
        if (mysqli_num_rows($result) > 0) {
          // Loop through products
          while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="col-md-3 col-sm-6 col-6 m-auto py-3">
              <a href="product_detail.php?id=<?php echo $row['p_id']; ?>" class="text-decoration-none text-dark">
                <div class="product-card">
                  <div class="product-image">
                    <img src="admin/product_img/<?php echo $row['imgname']; ?>" class="img-fluid" alt="<?php echo $row['name']; ?>">
                  </div>
                  <div class="product-info">
                    <h6><?php echo $row["name"] ?></h6>
                    <span class="price">$<?php echo $row["price"] ?></span>
                  </div>
                </div>
              </a>
              
              <!-- Quick add button removed -->
            </div>
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
