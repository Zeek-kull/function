<?php
 include'header.php';
 include'lib/connection.php';
 
 // Fix undefined array key warning
 $name = isset($_POST['name']) ? $_POST['name'] : '';
 
 if (!empty($name)) {
     $sql = "SELECT * FROM product where name='$name'  OR category='$name'";
     $result = $conn -> query ($sql);
 } else {
     $result = $conn -> query ("SELECT * FROM product LIMIT 0");
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="admin/css/pending_orders.css">
    <style>
        .product-card {
            cursor: pointer;
            transition: transform 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-info {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-top: 10px;
        }
        .product-info h6 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 16px;
        }
        .product-info span {
            font-weight: bold;
            color: #007bff;
            font-size: 14px;
        }
        .product-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container pendingbody">
  <h5>Search Result</h5>
  <div class="container">
   <div class="row">
   <?php
          if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {
              ?>
            <div class="col-md-3 col-sm-6 col-6">
              <a href="product_detail.php?id=<?php echo $row['p_id']; ?>" class="product-card">
                <div>
                  <img src="admin/product_img/<?php echo $row['imgname']; ?>" class="product-image" alt="<?php echo htmlspecialchars($row['name']); ?>">
                </div>
                <div class="product-info">
                  <h6><?php echo htmlspecialchars($row["name"]); ?></h6> 
                  <span>$<?php echo number_format($row["price"], 2); ?></span>
                </div>
              </a>
            </div>
            <?php 
            }
        } 
        else {
            echo "<div class='col-12'><p class='text-center'>No products found matching your search.</p></div>";
        }
        ?>
   </div>
  </div>
</div>
    
</body>
</html>
