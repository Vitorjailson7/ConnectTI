<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "connectti");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

session_start();

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

/* ===============================
   1️⃣ LOGIN COMO ALUNO
   TABELA: Aluno
================================ */
$sqlAluno = "SELECT * FROM Aluno WHERE email = '$email' LIMIT 1";
$resultAluno = $conn->query($sqlAluno);

if ($resultAluno && $resultAluno->num_rows === 1) {

    $aluno = $resultAluno->fetch_assoc();

    if (!empty($aluno['senha']) && password_verify($senha, $aluno['senha'])) {

        $_SESSION['id']    = $aluno['id'];
        $_SESSION['nome']  = $aluno['nome'];
        $_SESSION['email'] = $aluno['email'];
        $_SESSION['tipo']  = 'aluno';

        header("Location: painel.php");
        exit;
    }
}

/* ===============================
   2️⃣ LOGIN COMO DOCENTE
   TABELA: docentes
================================ */
$sqlDocente = "SELECT * FROM docentes WHERE email_profissional = '$email' LIMIT 1";
$resultDocente = $conn->query($sqlDocente);

if ($resultDocente && $resultDocente->num_rows === 1) {

    $docente = $resultDocente->fetch_assoc();

    if (!empty($docente['senha']) && password_verify($senha, $docente['senha'])) {

        $_SESSION['id']    = $docente['id'];
        $_SESSION['nome']  = $docente['nome_completo'];
        $_SESSION['email'] = $docente['email_profissional'];
        $_SESSION['tipo']  = 'docente';

        header("Location: painel.php");
        exit;
    }
}

/* ===============================
   ERRO FINAL
================================ */
echo "
<script>
    alert('E-mail ou senha inválidos');
    window.history.back();
</script>
";
exit;
