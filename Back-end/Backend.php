<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão
$conn = new mysqli("localhost", "root", "", "connectti");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

/* ===============================
   IDENTIFICA TIPO
================================ */
$isDocente = isset($_POST['docente']);

/* ===============================
   VALIDA SENHA
================================ */
if ($isDocente) {

    if (!isset($_POST['senha_docente']) || strlen($_POST['senha_docente']) < 6) {
        echo "
        <script>
            alert('A senha do docente deve ter no mínimo 6 caracteres.');
            window.history.back();
        </script>
        ";
        exit;
    }

    $senha = password_hash($_POST['senha_docente'], PASSWORD_DEFAULT);

} else {

    if (!isset($_POST['senha_aluno']) || strlen($_POST['senha_aluno']) < 6) {
        echo "
        <script>
            alert('A senha do aluno deve ter no mínimo 6 caracteres.');
            window.history.back();
        </script>
        ";
        exit;
    }

    $senha = password_hash($_POST['senha_aluno'], PASSWORD_DEFAULT);
}

/* ===============================
   SE FOR DOCENTE
================================ */
if ($isDocente) {

    $nome_completo      = $_POST['nome_completo'];
    $formacao           = $_POST['formacao'];
    $email_profissional = $_POST['email_profissional'];
    $registro           = $_POST['registro'];
    $instituicao        = $_POST['instituicao'];

    /* VALIDA REGISTRO */
    $sqlRegistro = "
        SELECT 1 
        FROM registro_docente 
        WHERE codigo = '$registro'
    ";

    $result = $conn->query($sqlRegistro);

    if (!$result || $result->num_rows === 0) {
        echo "
        <script>
            alert('Registro docente inválido!');
            window.history.back();
        </script>
        ";
        exit;
    }

    /* INSERE DOCENTE */
    $sqlDocente = "
        INSERT INTO docentes
        (nome_completo, formacao_academica, email_profissional, senha, registro_academico, instituicao)
        VALUES
        ('$nome_completo', '$formacao', '$email_profissional', '$senha', '$registro', '$instituicao')
    ";

    if (!$conn->query($sqlDocente)) {
        echo 'Erro ao cadastrar docente: ' . $conn->error;
        exit;
    }

    header("Location: login.php");
    exit();
}

/* ===============================
   SE FOR ALUNO
================================ */
$nome  = $_POST['nome'];
$email = $_POST['email'];
$data_nascimento = $_POST['data_nascimento'];

$sqlAluno = "
    INSERT INTO aluno
    (nome, email, senha, data_nascimento)
    VALUES
    ('$nome', '$email', '$senha', '$data_nascimento')
";

if (!$conn->query($sqlAluno)) {
    echo "Erro ao cadastrar aluno: " . $conn->error;
    exit;
}

header("Location: login.php");
exit();
?>