<?php
require "config.php";
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = [
            "id" => $user["id"],
            "username" => $user["username"]
        ];
        header("Location: dashboard.php");
        exit;
    }
    $error = "Username atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Kasir</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<div class="login-box">
    <h1>KasirKu</h1>
    <p>Silakan login untuk masuk</p>
    <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn primary full">Login</button>
    </form>
    <small>Login awal: admin / admin123</small>
</div>
</body>
</html>