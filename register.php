<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = strtolower(trim($_POST['username'])); // Trim and convert to lowercase
    $pass = trim($_POST['password']);

    // Hash the password before storing
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // Check if the username already exists (case-insensitive)
    $check_sql = "SELECT id FROM users WHERE LOWER(username) = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $user);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Username already exists. Please choose another.";
    } else {
        $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ss", $user, $hashed_password);

        if ($stmt->execute()) {
            header("Location: index.php?registered=1");
            exit();
        } else {
            $error = "An error occurred during registration.";
        }

        $stmt->close();
    }

    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account</title>
    <style>
        body {
            font-family: sans-serif;
            background: #012A6A;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            display: flex;
            background-color: #021d45;
            border-radius: 10px;
            overflow: hidden;
            width: 600px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }

        .register-logo {
            width: 50%;
            background: #012A6A;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-logo img {
            width: 80%;
            height: auto;
        }

        .register-box {
            width: 50%;
            padding: 30px;
            color: white;
            box-sizing: border-box;
        }

        .register-box h2 {
            margin-bottom: 20px;
        }

        .register-box input, .register-box button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            box-sizing: border-box;
        }

        .register-box button {
            background-color: #0066cc;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .register-box button:hover {
            background-color: #004d99;
        }

        .register-box a {
            color: #aad8ff;
            display: block;
            margin-top: 10px;
            text-decoration: none;
        }

        .register-box a:hover {
            text-decoration: underline;
        }

        .register-box p.error {
            color: red;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-logo">
        <img src="assets/logo.png" alt="Logo">
    </div>
    <div class="register-box">
        <form method="POST">
            <h2>Create Account</h2>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
        <a href="index.php">Back to Login</a>
    </div>
</div>

</body>
</html>
