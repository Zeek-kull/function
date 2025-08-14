<?php
// Include the database connection file
include "lib/connection.php";
// Initialize variables to store potential results and errors
$result      = null;
$email_error = null;
$phone_error = null;
$pass_error  = null;
$name_error  = null;
// Check if the registration form has been submitted
if (isset($_POST['u_submit'])) {
    // Retrieve POST variables from the registration form
    $f_name    = ucfirst(strtolower(trim($_POST['u_name'])));
    $l_name    = ucfirst(strtolower(trim($_POST['l_name'])));
    $email     = trim($_POST['email']);
    $raw_pass  = $_POST['pass'];
    $raw_cpass = $_POST['c_pass'];
    $region    = $_POST['region_text'];
    $province  = $_POST['province_text'];
    $city      = $_POST['city_text'];
    $barangay  = $_POST['barangay_text'];
    $street    = trim($_POST['street']);
    $zone      = isset($_POST['zone']) ? trim($_POST['zone']) : null;
    $phone     = trim($_POST['phone']);
    // Server-side validation for first and last name (letters and spaces only)
    if (!preg_match('/^[A-Za-z\s]+$/', $f_name)) {
        $name_error = "First name must contain only letters and spaces (no numbers or special characters).";
    } elseif (!preg_match('/^[A-Za-z\s]+$/', $l_name)) {
        $name_error = "Last name must contain only letters and spaces (no numbers or special characters).";
    }
    // Process and validate phone number
    $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric characters
    // Validate phone length and format for Philippine numbers
    if (strlen($phone) < 10 || strlen($phone) > 11) {
        $phone_error = "Phone number must be 10-11 digits (e.g., 09123456789 or 9123456789)";
    } elseif (!preg_match('/^(09|\+639|639|9)\d{9}$/', $phone)) {
        $phone_error = "Invalid Philippine phone number format. Use format: 09123456789";
    }
    // Password strength validation
    if (
        strlen($raw_pass) < 8 ||
        !preg_match('/[A-Z]/', $raw_pass) ||   // Ensure at least one uppercase letter
        !preg_match('/[a-z]/', $raw_pass) ||      // Ensure at least one lowercase letter
        !preg_match('/[0-9]/', $raw_pass) ||      // Ensure at least one digit
        !preg_match('/[\W_]/', $raw_pass)          // Ensure at least one special character
    ) {
        $pass_error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    }
    // Merge errors
    if ($name_error || $pass_error || $phone_error) {
        $result = $name_error ? $name_error : ($pass_error ? $pass_error : $phone_error);
    } else {
        // Hash the password securely using password_hash()
        $pass = password_hash($raw_pass, PASSWORD_DEFAULT);
        // Check if the email already exists in the database
        $email_check_sql = "SELECT email FROM users WHERE email = ?";
        if ($stmt_check = $conn->prepare($email_check_sql)) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) { // Email is already registered
                $email_error = "Email is already taken.";
            } else {
                // Verify that the passwords match using password_verify()
                if (password_verify($raw_cpass, $pass)) {
                    // Prepare insert SQL statement for account registration
                    $insertSql = "INSERT INTO users (f_name, l_name, email, pass, region, province, city, barangay, street, zone, phone)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($stmt = $conn->prepare($insertSql)) {
                        $stmt->bind_param(
                            "sssssssssss",
                            $f_name,
                            $l_name,
                            $email,
                            $pass,
                            $region,
                            $province,
                            $city,
                            $barangay,
                            $street,
                            $zone,
                            $phone
                        );
                        // Execute the prepared statement
                        if ($stmt->execute()) {
                            $result = "Account Open success";
                            // Redirect to login page upon successful registration
                            header("Location: login.php");
                        } else {
                            $result = "Error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $result = "Error preparing statement: " . $conn->error;
                    }
                } else {
                    $result = "Password Not Match";
                }
            }
            $stmt_check->close();
        } else {
            $result = "Error checking email: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Create Account</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles for card rounding -->
    <style>
        .card {
            border-radius: 15px;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <!-- Registration form starts here -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Row wrapper for form content -->
                    <div class="row">
                        <div class="col-lg-7 mx-auto">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                                    <!-- Display result or error alerts if they exist -->
                                    <?php
                                      if ($result) {
                                          echo "<div class='alert alert-info'>{$result}</div>";
                                      }
                                      if ($email_error) {
                                          echo "<div class='alert alert-danger'>{$email_error}</div>";
                                      }
                                    ?>
                                </div>
                                <!-- Name fields with client-side validation (only letters) -->
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label for="fname">First Name</label>
                                        <input type="text" class="form-control form-control-user" id="exampleFirstName" placeholder="First Name" name="u_name" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed (no numbers or special characters)." required value="<?php echo isset($_POST['u_name']) ? htmlspecialchars(ucfirst(strtolower($_POST['u_name']))) : ''; ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="lname">Last Name</label>
                                        <input type="text" class="form-control form-control-user" id="exampleLastName" placeholder="Last Name" name="l_name" pattern="[A-Za-z\s]+" title="Only letters and spaces are allowed (no numbers or special characters)." required value="<?php echo isset($_POST['l_name']) ? htmlspecialchars(ucfirst(strtolower($_POST['l_name']))) : ''; ?>">
                                    </div>
                                </div>
                                <!-- Address fields -->
                                <div class="form-group">
                                    <label for="street">Street Number/House Number</label>
                                    <input type="text" class="form-control" id="street" name="street" required value="<?php echo isset($_POST['street']) ? htmlspecialchars($_POST['street']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="zone">Zone (optional)</label>
                                    <input type="text" class="form-control" id="zone" name="zone" value="<?php echo isset($_POST['zone']) ? htmlspecialchars($_POST['zone']) : ''; ?>">
                                </div>
                                <!-- Philippine Address Dropdowns START -->
                                <div class="form-group">
                                    <label for="region">Region</label>
                                    <select id="region" class="form-control" required></select>
                                    <input type="hidden" name="region_text" id="region-text">
                                </div>
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <select id="province" class="form-control" required></select>
                                    <input type="hidden" name="province_text" id="province-text">
                                </div>
                                <div class="form-group">
                                    <label for="city">City/Municipality</label>
                                    <select id="city" class="form-control" required></select>
                                    <input type="hidden" name="city_text" id="city-text">
                                </div>
                                <div class="form-group">
                                    <label for="barangay">Barangay</label>
                                    <select id="barangay" class="form-control" required></select>
                                    <input type="hidden" name="barangay_text" id="barangay-text">
                                </div>
                                <!-- Philippine Address Dropdowns END -->
                                <!-- Email Field -->
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                </div>
                                <!-- Phone Field -->
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control form-control-user" id="phone" placeholder="09123456789" name="phone" pattern="[0-9]{11}" maxlength="11" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                    <small class="form-text text-muted">Format: 09123456789 (11 digits starting with 09)</small>
                                </div>
                                <!-- Password Fields -->
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label for="pass">Password</label>
                                        <input type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" name="pass" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="c_pass">Confirm Password</label>
                                        <input type="password" class="form-control form-control-user" id="exampleRepeatPassword" placeholder="Repeat Password" name="c_pass" required>
                                    </div>
                                </div>
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-user btn-block" name="u_submit">Register Account</button>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="login.php">Already have an account? Login!</a>
                                </div>
                            </div><!-- end p-5 -->
                        </div><!-- end col-lg-7 -->
                    </div><!-- end row -->
                </div><!-- end card-body -->
            </div><!-- end card -->
        </form>
    </div><!-- end container -->
    <!-- Bootstrap JS, jQuery and custom JS for validation -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/ph-address-selector.js"></script>
    <script>
        // Validate phone number and password on form submission
        document.querySelector('form').addEventListener('submit', function (e) {
            var pass  = document.getElementById('exampleInputPassword').value;
            var cpass = document.getElementById('exampleRepeatPassword').value;
            var phone = document.getElementById('phone').value;
            var error = "";
            // Clean phone number (remove non-digit characters)
            var phoneClean = phone.replace(/[^0-9]/g, '');
            if (phoneClean.length !== 11) {
                error = "Phone number must be exactly 11 digits.";
            } else if (!phoneClean.startsWith('09')) {
                error = "Phone number must start with '09'.";
            } else if (!/^09\d{9}$/.test(phoneClean)) {
                error = "Invalid phone number format. Use: 09123456789";
            }
            // Password strength and match validation
            if (!error) {
                if (
                    pass.length < 8 ||
                    !/[A-Z]/.test(pass) ||
                    !/[a-z]/.test(pass) ||
                    !/[0-9]/.test(pass) ||
                    !/[\W_]/.test(pass)
                ) {
                    error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
                } else if (pass !== cpass) {
                    error = "Passwords do not match.";
                }
            }
            // Prevent form submission if there is any validation error
            if (error) {
                alert(error);
                e.preventDefault();
            }
        });
        // Real-time phone number formatting on input
        document.getElementById('phone').addEventListener('input', function (e) {
            var value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
