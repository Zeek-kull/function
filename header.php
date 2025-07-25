<?php 
  // Check if a session is already started before starting it
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }

  include "lib/connection.php";

  // Initialize variables
  $id = null; 
  $result = null;

  // Check if 'userid' exists in the session (i.e., the user is logged in)
  if (isset($_SESSION['userid'])) {
      $id = $_SESSION['userid'];
      
      // Run query only if $id is set
      $sql = "SELECT * FROM cart WHERE userid='$id'";
      $result = $conn->query($sql);
  }

  // Calculate total items in the cart
  $total = 0;
  if ($result && mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
          $total++;
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fashion">
    <meta name="keywords" content="Fashion, Clothing, Bootstrap">
    <meta name="author" content="Anik">
    <title>A & M Closet</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" >
    <link href="css/css2.css?family=Raleway:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
<!-- Header -->
<header class="bg-light py-3">
    <div class="container text-center">
        <a href="index.php">
            <img src="img/Vogue_processed.png" alt="Fashion Logo" class="img-fluid" style="max-width: 250px;">
        </a>
    </div>
</header>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <!--<a class="navbar-brand font-weight-bold" href="index.php">Fa</a>-->
        <button class="navbar-toggler" type="button" data-toggle="collapse" 
                data-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Fashion</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="Clothing.php">Clothing</a></li>
                <li class="nav-item"><a class="nav-link" href="Trends.php"></a></li>
            </ul>
            <form class="form-inline my-2 my-lg-0" action="search(1).php" method="post">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" name="name">
                <button class="btn btn-outline-dark my-2 my-sm-0" type="submit">
                    <img src="img/search.png" alt="Search">
                </button>
            </form>

            <!-- Show Cart icon only if the user is logged in -->
            <?php if (isset($_SESSION['userid'])): ?>
                <a class="btn btn-outline-dark ml-3" href="cart(1).php">
                    <img src="img/cart.png" alt="Cart" style="width: 20px;"> 
                    <span class="badge badge-pill badge-secondary"><?php echo $total; ?></span>
                </a>
            <?php endif; ?>
            
            <div class="ml-3">
                <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] == 1): ?>
                    <span class="text-muted"><?php echo $_SESSION['username']; ?></span>
                    <a class="btn btn-outline-primary btn-sm ml-2" href="profile.php">My Orders</a>
                    <a class="btn btn-outline-danger btn-sm ml-2" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="btn btn-outline-primary btn-sm" href="login.php">Login</a>
                    <a class="btn btn-outline-success btn-sm ml-2" href="Register.php">Signup</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Optional Bootstrap JS -->
<script src="js/jquery-3.5.1.slim.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
