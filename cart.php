<?php
  include 'header.php';
  include 'lib/connection.php';

  // Check if user is authenticated
  if (!isset($_SESSION['auth']) || $_SESSION['auth'] != 1) {
      header("location:login.php");
      exit();
  }

  // Order handling
  // Get user's latest address from orders table
  $latest_address = '';
  $user_id_for_address = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
  if ($user_id_for_address) {
      $address_query = mysqli_query($conn, "SELECT address FROM orders WHERE userid='$user_id_for_address' ORDER BY id DESC LIMIT 1");
      if ($address_query && mysqli_num_rows($address_query) > 0) {
          $latest_address = mysqli_fetch_assoc($address_query)['address'];
      }
  }
  if (isset($_POST['order_btn'])) {
      $userid = $_POST['user_id'];
      $name = $_POST['user_name'];
      $number = $_POST['number'];
      $address = $_POST['address'];
      $mobnumber = isset($_POST['mobnumber']) ? $_POST['mobnumber'] : '';
      $payment_method = $_POST['payment_method']; // User-selected payment method
      $status = "pending";
      $order_date = date('Y-m-d H:i:s'); // Current date and time

      $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE userid='$userid'");
      $price_total = 0;
      $product_name = [];

      // Calculate total price and update stock
      if (mysqli_num_rows($cart_query) > 0) {
          while ($product_item = mysqli_fetch_assoc($cart_query)) {
              $product_name[] = $product_item['productid'] . ' (' . $product_item['quantity'] . ')';
              $product_price = number_format($product_item['price'] * $product_item['quantity']);
              $price_total += $product_price;

              // Update product stock
              $sql = "SELECT * FROM product WHERE p_id = '{$product_item['productid']}'";
              $result = $conn->query($sql);
              if (mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      if ($product_item['quantity'] <= $row['quantity']) {
                          $update_quantity = $row['quantity'] - $product_item['quantity'];
                          $update_query = mysqli_query($conn, "UPDATE `product` SET quantity = '$update_quantity' WHERE p_id = '{$row['p_id']}'");
                      } else {
                          echo "Out of stock: " . $row['name'] . " Quantity: " . $row['quantity'];
                      }
                  }
              }
          }

          // Insert order if products are available
          $total_product = implode(', ', $product_name);
          // Use only the correct column 'created_at' for the order date
          $detail_query = mysqli_query($conn, "INSERT INTO `orders`(userid, name, address, phone, mobnumber, payment_method, totalproduct, totalprice, status, created_at) 
              VALUES('$userid','$name','$address','$number','$mobnumber','$payment_method','$total_product','$price_total','$status', '$order_date')");

          // Empty cart after successful order
          $cart_query1 = mysqli_query($conn, "DELETE FROM `cart` WHERE userid='$userid'");
          header("location:index.php");
          exit();
      }
  }

  // Get user's cart
  $id = $_SESSION['userid'];
  $sql = "SELECT * FROM cart WHERE userid='$id'";
  $result = $conn->query($sql);

  // Update cart quantity
  if (isset($_POST['update_update_btn'])) {
      $update_value = $_POST['update_quantity'];
      $update_id = $_POST['update_quantity_id'];
      $update_quantity_query = mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_value' WHERE c_id = '$update_id'");
      if ($update_quantity_query) {
          header('location:cart.php');
          exit();
      }
  }

  // Remove item from cart
  if (isset($_GET['remove'])) {
      $remove_id = $_GET['remove'];
      mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'");
      header('location:cart.php');
      exit();
  }
?>

<div class="container mt-4">
  <h3 class="text-center mb-4">Your Cart</h3>

  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $total = 0;
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
      ?>
      <tr>
        <td><?php echo $row["name"]; ?></td>
        <td>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="update_quantity_id" value="<?php echo $row['c_id']; ?>">
            <input type="number" name="update_quantity" min="1" value="<?php echo $row['quantity']; ?>" class="form-control w-50 d-inline">
            <input type="submit" value="Update" name="update_update_btn" class="btn btn-info btn-sm ml-2">
          </form>
        </td>
        <td><?php echo number_format($row["price"] * $row["quantity"], 2); ?></td>
        <?php $total += $row["price"] * $row["quantity"]; ?>
        <td><a href="cart.php?remove=<?php echo $row['c_id']; ?>" class="btn btn-danger btn-sm">Remove</a></td>
      </tr>
      <?php
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No Products in the Cart</td></tr>";
        }
      ?>
    </tbody>
  </table>

  <div class="text-right my-4">
    <h4>Total Amount: <span class="text-danger"><?php echo number_format($total, 2); ?> </span></h4>
  </div>

  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="orderForm" class="border p-4 rounded">
    <input type="hidden" name="total" value="<?php echo $total ?>">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['userid']; ?>">
    <input type="hidden" name="user_name" value="<?php echo $_SESSION['username']; ?>">

    <div class="form-group">
      <div class="input-group">
        <input type="text" class="form-control" name="address" id="addressInput" placeholder="Shipping Address" value="<?php echo htmlspecialchars($latest_address); ?>" required <?php echo empty($latest_address) ? '' : 'readonly'; ?>>
        <div class="input-group-append">
          <button type="button" class="btn btn-secondary" id="editAddressBtn" <?php echo empty($latest_address) ? 'style="display:none;"' : ''; ?>>Edit</button>
        </div>
      </div>
    </div>
    <div class="form-group">
      <input type="text" class="form-control" name="mobnumber" placeholder="Phone Number" pattern="[0-9]{7,15}" required>
    </div>
    <div class="form-group">
      <select name="payment_method" id="payment_method" class="form-control" required>
        <option value="" disabled selected>Select Payment Method</option>
        <option value="COD">Cash on Delivery (COD)</option>
        <option value="PayPal">PayPal</option>
      </select>
    </div>

    <button type="submit" name="order_btn" class="btn btn-lg btn-block" id="orderButton" disabled>Place Order</button>
  </form>
</div>

<?php
  include 'footer.php';
?>

<!-- Custom styles for clean look -->
<style>
  .table th, .table td {
    vertical-align: middle;
    text-align: center;
  }
  .table td {
    font-size: 1.1em;
  }
  .btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
  }
  .btn-danger {
    background-color: #e74c3c;
    border-color: #e74c3c;
  }
  .btn-info {
    background-color: #3498db;
    border-color: #3498db;
  }
  .btn-success {
    background-color: #2ecc71;
    border-color: #2ecc71;
  }
  .btn:disabled {
    background-color: #ddd;
    cursor: not-allowed;
  }
</style>

<!-- JavaScript for Form Validation -->
<script>
  // Redirect to profile.php when Edit button is clicked
  document.getElementById('editAddressBtn')?.addEventListener('click', function () {
    window.location.href = 'profile.php';
  });
</script>
<script>
  document.getElementById('orderForm').addEventListener('input', function () {
      var address = document.querySelector('input[name="address"]').value;
      var mobnumber = document.querySelector('input[name="mobnumber"]').value;
      var payment_method = document.querySelector('select[name="payment_method"]').value;

      // Validate phone number pattern
      var phoneValid = /^[0-9]{7,15}$/.test(mobnumber);

      if (address && mobnumber && payment_method && phoneValid) {
          document.getElementById('orderButton').disabled = false;
          document.getElementById('orderButton').style.backgroundColor = '#2ecc71';  // Green
      } else {
          document.getElementById('orderButton').disabled = true;
          document.getElementById('orderButton').style.backgroundColor = '#ddd'; // Disabled gray
      }
  });
</script>
