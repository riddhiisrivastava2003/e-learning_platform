<?php
// Minimal registration test
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
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, "student")');
    if ($stmt->execute([$username, $email, $password])) {
        echo '<p style="color:green">Registration successful!</p>';
    } else {
        echo '<p style="color:red">Registration failed!</p>';
    }
}
?>
<form method="post">
    <input name="username" placeholder="Username" required><br>
    <input name="email" type="email" placeholder="Email" required><br>
    <input name="password" type="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form> 