<?php
$conn = new mysqli("localhost", "root", "", "connectti");

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

if ($conn->query($sql)) {
    header("Location: login.php");
} else {
    echo "Erro ao cadastrar!";
}
?>
