<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $pass = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        header("Location: admin/dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<form method="POST">
    <h2>Login</h2>
    <p><?= $error ?></p>
    Email: <input name="email"><br>
    Password: <input type="password" name="password"><br>
    <button type="submit">Login</button>
</form>
