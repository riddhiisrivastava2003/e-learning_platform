<?php
// Minimal login test
error_reporting(E_ALL); ini_set('display_errors', 1);
try {
    $pdo = new PDO('mysql:host=localhost;dbname=edutech_pro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<p style="color:green">DB CONNECTED</p>';
} catch (Exception $e) {
    die('<p style="color:red">DB ERROR: ' . $e->getMessage() . '</p>');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? AND role = "student"');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        echo '<p style="color:green">Login successful!</p>';
    } else {
        echo '<p style="color:red">Login failed!</p>';
    }
}
?>
<form method="post">
    <input name="username" placeholder="Username" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form> 