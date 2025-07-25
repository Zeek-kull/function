<?php
include 'header.php';

if (isset($_SESSION['auth'])) {
    if ($_SESSION['auth'] != 1) {
        header("location:login.php");
    }
} else {
    header("location:login.php");
}

include 'lib/connection.php';
$k = $_SESSION['userid'];
$sql = "SELECT * FROM orders WHERE userid='$k'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link rel="stylesheet" href="css/pending_orders.css">
</head>
<body>

<div class="container pendingbody">
  <?php
    // Get user info (without address)
    $user_info = mysqli_query($conn, "SELECT f_name, l_name FROM users WHERE id='$k'");
    $user_row = mysqli_fetch_assoc($user_info);
    $user_name = isset($user_row['f_name']) ? $user_row['f_name'] : (isset($_SESSION['username']) ? $_SESSION['username'] : '');
    $user_lname = isset($user_row['l_name']) ? $user_row['l_name'] : '';
    // Get address from latest order
    $address_row = mysqli_query($conn, "SELECT address FROM orders WHERE userid='$k' ORDER BY id DESC LIMIT 1");
    $order_address = '';
    if ($address_row && mysqli_num_rows($address_row) > 0) {
      $order_address = mysqli_fetch_assoc($address_row)['address'];
    }

    // Handle address update
    if (isset($_POST['update_profile_address_btn'])) {
      $new_profile_address = mysqli_real_escape_string($conn, $_POST['profile_new_address']);
      // Update address in all user's orders (or you can update only latest order)
      mysqli_query($conn, "UPDATE orders SET address = '$new_profile_address' WHERE userid = '$k'");
      // Optionally, update the address in users table if you have that column
      // mysqli_query($conn, "UPDATE users SET address = '$new_profile_address' WHERE id = '$k'");
      echo "<script>window.location.href='profile.php';</script>";
      exit();
    }
  ?>
  <div class="mb-3">
    <h4>Name: <?php echo htmlspecialchars($user_name . ' ' . $user_lname); ?></h4>
    <form action="" method="post" class="form-inline">
      <label for="profile_new_address" class="mr-2"><h5>Address:</h5></label>
      <input type="text" name="profile_new_address" id="profile_new_address" value="<?php echo htmlspecialchars($order_address); ?>" class="form-control form-control-sm mr-2" style="width:220px;">
      <button type="submit" name="update_profile_address_btn" class="btn btn-info btn-sm">Update Address</button>
    </form>
  </div>
  <h5>My Orders</h5>
  <table class="table">
    <thead>
      <tr>
        <th scope="col">Date</th>
        <th scope="col">Name</th>
        <th scope="col">Address</th>
        <th scope="col">Phone</th>
        <th scope="col">Total Product</th>
        <th scope="col">Total Price</th>
        <th scope="col">Payment Method</th>
        <th scope="col">Status</th>
        <th scope="col">Track</th>
        <th scope="col">Edit Shipping</th>
      </tr>
    </thead>
    <tbody>
    <?php
    // Handle shipping address update
    if (isset($_POST['update_address_btn'])) {
        $update_id = $_POST['order_id'];
        $new_address = $_POST['new_address'];
        $update_query = mysqli_query($conn, "UPDATE `orders` SET address = '" . mysqli_real_escape_string($conn, $new_address) . "' WHERE id = '$update_id' AND userid = '$k'");
        if ($update_query) {
            echo "<script>window.location.href='profile.php';</script>";
            exit();
        }
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
              <td>
                <?php 
                // Format the date with timezone adjustment using 'created_at' column
                if (!empty($row["created_at"])) {
                  $date = new DateTime($row["created_at"]);
                  $date->setTimezone(new DateTimeZone('Asia/Manila'));
                  echo $date->format("F j, Y, g:i A"); 
                } else {
                  echo "N/A";
                }
                ?>
              </td>
              <td><?php echo htmlspecialchars($row["name"]); ?></td>
              <td><?php echo htmlspecialchars($row["address"]); ?></td>
              <td>
                <?php
                  // If phone is encrypted, show mobnumber instead
                  $phone = $row["phone"];
                  if (preg_match('/^[0-9]+$/', $phone) && strlen($phone) >= 7 && strlen($phone) <= 15) {
                    echo htmlspecialchars($phone);
                  } elseif (!empty($row["mobnumber"])) {
                    echo htmlspecialchars($row["mobnumber"]);
                  } else {
                    echo "N/A";
                  }
                ?>
              </td>
              <td>
                <?php
                  // Sum all product quantities for this order
                  $products = explode(',', $row["totalproduct"]);
                  $total_quantity = 0;
                  foreach ($products as $prod) {
                    if (preg_match('/\((\d+)\)/', $prod, $matches)) {
                      $total_quantity += (int)$matches[1];
                    }
                  }
                  echo htmlspecialchars($total_quantity);
                ?>
              </td>
              <td><?php echo "₱" . number_format($row["totalprice"], 2); ?></td>
              <td><?php echo htmlspecialchars($row["payment_method"]); ?></td>
              <td><?php echo htmlspecialchars($row["status"]); ?></td>
              <td>
                <?php
                  // Simple order tracking status
                  if ($row["status"] == "Pending") {
                    echo "<span class='badge badge-warning'>Order is being processed</span>";
                  } elseif ($row["status"] == "Confirmed") {
                    echo "<span class='badge badge-info'>Order confirmed</span>";
                  } elseif ($row["status"] == "Delivered") {
                    echo "<span class='badge badge-success'>Delivered</span>";
                  } elseif ($row["status"] == "Cancel") {
                    echo "<span class='badge badge-danger'>Cancelled</span>";
                  } else {
                    echo htmlspecialchars($row["status"]);
                  }
                ?>
              </td>
              <td>
                <!-- Edit shipping location is handled above -->
              </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='10' class='text-center'>No orders found</td></tr>";
    }
    ?>
    </tbody>
  </table>
</div>
    
</body>
</html>

<?php
include 'footer.php';
?>
