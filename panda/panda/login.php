<?php
date_default_timezone_set('Africa/Cairo');
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define valid credentials for each role
    $adminUsers = array(
        "admin1" => "admin123",
        "admin2" => "admin123",
        "admin3" => "admin123"
    );

    $kitchenUsers = array(
        "kitchen1" => "kitchen123",
        "kitchen2" => "kitchen123",
        "kitchen3" => "kitchen123"
    );

    $waiterUsers = array(
        "waiter1" => "waiter123",
        "waiter2" => "waiter123",
        "waiter3" => "waiter123"
    );

    // Retrieve the username, password, and selected role from the form
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Validate credentials based on the selected role
    switch ($role) {
        case "admin":
            if (array_key_exists($username, $adminUsers) && $adminUsers[$username] === $password) {
                $_SESSION["username"] = $username;
                $_SESSION["role"] = "admin";
                header("Location: admin.php");
                exit;
            }
            break;

        case "kitchen":
            if (array_key_exists($username, $kitchenUsers) && $kitchenUsers[$username] === $password) {
                $_SESSION["username"] = $username;
                $_SESSION["role"] = "kitchen";
                header("Location: kitchen.php");
                exit;
            }
            break;

        case "waiter":
            if (array_key_exists($username, $waiterUsers) && $waiterUsers[$username] === $password) {
                $_SESSION["username"] = $username;
                $_SESSION["role"] = "waiter";
                header("Location: waiter.php");
                exit;
            }
            break;

        default:
            $_SESSION["error"] = "Invalid username or password";
            header("Location: login.php");
            exit;
    }

    // Invalid credentials for selected role
    $_SESSION["error"] = "Invalid username or password";
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="icon" href="./material/favicon.ico">
    <style>
        body {
            background-image: url('material/pexels-elevate-1267320.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .input-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .role-group {
            margin-bottom: 15px;
            text-align: left;
            color: #333;
        }

        .role-group label {
            display: block;
            margin-bottom: 5px;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php
        // Display error message if login failed
        if (isset($_SESSION["error"])) {
            echo '<div class="error-message">' . $_SESSION["error"] . '</div>';
            unset($_SESSION["error"]);
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="role-group">
                <label><input type="radio" name="role" value="admin" checked> Admin</label>
                <label><input type="radio" name="role" value="kitchen" > Kitchen</label>
                <label><input type="radio" name="role" value="waiter"> Waiter</label>
            </div>
            <div class="input-group">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
