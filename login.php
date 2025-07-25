<?php 
session_start();

// Redirect if already authenticated
if (isset($_SESSION['auth']) && $_SESSION['auth'] == 1) {
    header("location:index.php");
    exit;
}

include "lib/connection.php";

if (isset($_POST['submit'])) {
    // Sanitize user inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $pass = md5($_POST['password']); // Consider using stronger hash algorithms like bcrypt

    // Prepared statement to prevent SQL injection
    $loginquery = "SELECT * FROM users WHERE email = ? AND pass = ?";
    $stmt = $conn->prepare($loginquery);
    $stmt->bind_param('ss', $email, $pass);
    $stmt->execute();
    $loginres = $stmt->get_result();

    // Check login result
    if ($loginres->num_rows > 0) {
        // Fetch user data and store it in session
        $result = $loginres->fetch_assoc();
        $_SESSION['username'] = $result['f_name'];
        $_SESSION['userid'] = $result['id'];
        $_SESSION['auth'] = 1;

        header("location:index.php");
        exit;
    } else {
        $error_message = "Invalid email or password";
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
    <title>Login</title>
    <!-- Bootstrap 4 or 5 CDN (for modern UI components) -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; /* "Dirty White" background color */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #6a11cb;
            border: none;
        }
        .btn-primary:hover {
            background-color: #2575fc;
        }
        .form-control {
            border-radius: 5px;
            box-shadow: none;
        }
        .text-center a {
            color: #6a11cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <!-- Outer Row -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="w-100" style="max-width: 400px;">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h1 class="h4 text-gray-900">Welcome Back!</h1>
                                <p>Please login to continue</p>
                            </div>
                            
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Enter Email Address" required>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password" required>
                            </div>
                            <input class="btn btn-primary btn-user btn-block" type="submit" name="submit" value="Login">
                            <hr>
                            <div class="text-center">
                                <a class="small" href="register.php">Create an Account!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
