<?php
session_start();
$conn = new mysqli("localhost", "root", "", "connectti");

$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE email='$email' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($senha, $user['senha'])) {
        $_SESSION['user'] = $user['nome'];
        header("Location: painel.php");
        exit;
    }
}

echo "<h3>Email ou senha incorretos!</h3>";
