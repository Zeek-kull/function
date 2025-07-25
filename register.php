<?php
include "lib/connection.php";

$result = null;
$email_error = null;

if (isset($_POST['u_submit'])) {
    $f_name = $_POST['u_name'];
    $l_name = $_POST['l_name'];
    $email = $_POST['email'];
    $pass = md5($_POST['pass']);
    $cpass = md5($_POST['c_pass']);

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
                $insertSql = "INSERT INTO users (f_name, l_name, email, pass) VALUES (?, ?, ?, ?)";
    
                if ($stmt = $conn->prepare($insertSql)) {
                    $stmt->bind_param("ssss", $f_name, $l_name, $email, $pass);
    
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
        .bg-gradient-primary {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            color: white;
        }

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
                                        <input type="text" class="form-control form-control-user" id="exampleFirstName" placeholder="First Name" name="u_name" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="exampleLastName" placeholder="Last Name" name="l_name" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address" name="email" required>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" name="pass" required>
                                    </div>
                                    <div class="col-sm-6">
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
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
