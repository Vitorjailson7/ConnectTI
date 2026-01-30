<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "connectti");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$isDocente = isset($_POST['docente']);

/* ===============================
   CADASTRO DOCENTE
================================ */
if ($isDocente) {

    if (empty($_POST['senha_docente'])) {
        die("Senha do docente não informada");
    }

    $nome_completo      = $_POST['nome_completo'];
    $formacao           = $_POST['formacao'];
    $email_profissional = $_POST['email_profissional'];
    $registro           = $_POST['registro'];
    $instituicao        = $_POST['instituicao'];
    $senha              = password_hash($_POST['senha_docente'], PASSWORD_DEFAULT);

    // valida registro
    $check = $conn->query("
        SELECT 1 FROM registro_docente WHERE codigo = '$registro'
    ");

    if ($check->num_rows === 0) {
        echo "<script>alert('Registro docente inválido');history.back();</script>";
        exit;
    }

    // insere docente
    $sql = "
        INSERT INTO docentes
        (nome_completo, formacao_academica, email_profissional, senha, registro_academico, instituicao)
        VALUES
        ('$nome_completo', '$formacao', '$email_profissional', '$senha', '$registro', '$instituicao')
    ";

    if (!$conn->query($sql)) {
        die("Erro docente: " . $conn->error);
    }

    header("Location: login.php");
    exit;
}

/* ===============================
   CADASTRO ALUNO
================================ */
if (empty($_POST['senha_aluno'])) {
    die("Senha do aluno não informada");
}

$nome  = $_POST['nome'];
$email = $_POST['email'];
$data  = $_POST['data_nascimento'];
$senha = password_hash($_POST['senha_aluno'], PASSWORD_DEFAULT);

$sql = "
    INSERT INTO Aluno (nome, email, senha, data_nascimento)
    VALUES ('$nome', '$email', '$senha', '$data')
";

if (!$conn->query($sql)) {
    die("Erro aluno: " . $conn->error);
}

header("Location: login.php");
exit;
