<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user'] = $user;
            header("Location: pos.php");
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body {
            font-family: sans-serif;
            background: #012A6A;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            display: flex;
            background-color: #021d45;
            border-radius: 10px;
            overflow: hidden;
            width: 600px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }

        .login-logo {
            width: 50%;
            background: #012A6A;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-logo img {
            width: 80%;
            height: auto;
        }

        .login-box {
            width: 50%;
            padding: 30px;
            color: white;
            box-sizing: border-box;
        }

        .login-box h2 {
            margin-bottom: 20px;
        }

        .login-box input, .login-box button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
        }

        .login-box button {
            background-color: #0066cc;
            color: white;
            cursor: pointer;
        }

        .login-box button:hover {
            background-color: #004d99;
        }

        .login-box a {
            color: #aad8ff;
            display: block;
            margin-top: 10px;
            text-decoration: none;
        }

        .login-box a:hover {
            text-decoration: underline;
        }

        .login-box p {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="login-container">
        <div class="login-logo">
            <img src="assets/logo.png" alt="Logo">
        </div>
        <div class="login-box">
            <form method="POST">
                <h2>Log In</h2>
                <input type="text" name="username" placeholder="Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">LOGIN</button>
                <?php 
                    if (isset($error)) echo "<p>$error</p>";
                    if (isset($_GET['registered'])) echo "<p style='color:lightgreen;'>Account created! Please log in.</p>";
                ?>
            </form>
            <a href="register.php">Create an Account</a>
        </div>
    </div>
</div>

</body>
</html>
