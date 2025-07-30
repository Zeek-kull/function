<?php
include "lib/connection.php";

$result = null;
$email_error = null;

if (isset($_POST['u_submit'])) {
    $f_name = $_POST['u_name'];
    $l_name = $_POST['l_name'];
    $email = $_POST['email'];
    $raw_pass = $_POST['pass'];
    $raw_cpass = $_POST['c_pass'];
    $region = $_POST['region_text'];
    $province = $_POST['province_text'];
    $city = $_POST['city_text'];
    $barangay = $_POST['barangay_text'];
    $street = $_POST['street'];
    $zone = isset($_POST['zone']) ? $_POST['zone'] : NULL;
    $phone = $_POST['phone'];

    // Phone number validation for Philippines
    $phone_error = null;
    $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric characters
    if (strlen($phone) < 10 || strlen($phone) > 11) {
        $phone_error = "Phone number must be 10-11 digits (e.g., 09123456789 or 9123456789)";
    } elseif (!preg_match('/^(09|\+639|639|9)\d{9}$/', $phone)) {
        $phone_error = "Invalid Philippine phone number format. Use format: 09123456789";
    }

    // Password strength check
    $pass_error = null;
    if (
        strlen($raw_pass) < 8 ||
        !preg_match('/[A-Z]/', $raw_pass) ||      // at least one uppercase
        !preg_match('/[a-z]/', $raw_pass) ||      // at least one lowercase
        !preg_match('/[0-9]/', $raw_pass) ||      // at least one digit
        !preg_match('/[\W_]/', $raw_pass)         // at least one special char
    ) {
        $pass_error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
    }

    if ($pass_error || $phone_error) {
        $result = $pass_error ? $pass_error : $phone_error;
    } else {
        $pass = md5($raw_pass);
        $cpass = md5($raw_cpass);

        // Check if email already exists
        $email_check_sql = "SELECT email FROM users WHERE email = ?";
        if ($stmt_check = $conn->prepare($email_check_sql)) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $email_error = "Email is already taken.";
            } else {
                // Proceed if the email is available
                if ($pass == $cpass) {
                    // Prepared statement to avoid SQL Injection
                    $insertSql = "INSERT INTO users (f_name, l_name, email, pass, region, province, city, barangay, street, zone, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    if ($stmt = $conn->prepare($insertSql)) {
                        $stmt->bind_param("sssssssssss", $f_name, $l_name, $email, $pass, $region, $province, $city, $barangay, $street, $zone, $phone);

                        if ($stmt->execute()) {
                            $result = "Account Open success";
                            header("location: login.php");
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
    <!-- Custom Styles -->
    <style>
        .card {
            border-radius: 15px;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-7 mx-auto">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                                    <?php if ($result) {
                                        echo "<div class='alert alert-info'>$result</div>";
                                    } ?>
                                    <?php if ($email_error) {
                                        echo "<div class='alert alert-danger'>$email_error</div>";
                                    } ?>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label for="fname">First Name</label>
                                        <input type="text" class="form-control form-control-user" id="exampleFirstName" placeholder="First Name" name="u_name" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="lname">Last Name</label>
                                        <input type="text" class="form-control form-control-user" id="exampleLastName" placeholder="Last Name" name="l_name" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="street">Street Number/House Number</label>
                                    <input type="text" class="form-control" id="street" name="street" required>
                                </div>
                                <div class="form-group">
                                    <label for="zone">Zone/Block (optional)</label>
                                    <input type="text" class="form-control" id="zone" name="zone">
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
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control form-control-user" id="phone" placeholder="09123456789" name="phone" pattern="[0-9]{11}" maxlength="11" required>
                                    <small class="form-text text-muted">Format: 09123456789 (11 digits starting with 09)</small>
                                </div>
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
                                <button type="submit" class="btn btn-primary btn-user btn-block" name="u_submit">Register Account</button>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="login.php">Already have an account? Login!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS -->
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/ph-address-selector.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            var pass = document.getElementById('exampleInputPassword').value;
            var cpass = document.getElementById('exampleRepeatPassword').value;
            var phone = document.getElementById('phone').value;
            var error = "";

            // Phone number validation
            var phoneClean = phone.replace(/[^0-9]/g, '');
            if (phoneClean.length !== 11) {
                error = "Phone number must be exactly 11 digits.";
            } else if (!phoneClean.startsWith('09')) {
                error = "Phone number must start with '09'.";
            } else if (!/^09\d{9}$/.test(phoneClean)) {
                error = "Invalid phone number format. Use: 09123456789";
            }

            // Password validation
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

            if (error) {
                alert(error);
                e.preventDefault();
            }
        });

        // Real-time phone number formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            var value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
