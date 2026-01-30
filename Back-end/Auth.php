<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "connectti");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$email = $_POST['email'];
$senha = $_POST['senha'];

session_start();

/* ===============================
   1️⃣ TENTA LOGIN COMO ALUNO
================================ */
$sqlAluno = "
    SELECT * 
    FROM Aluno 
    WHERE email = '$email'
";

$resultAluno = $conn->query($sqlAluno);

if ($resultAluno && $resultAluno->num_rows === 1) {

    $aluno = $resultAluno->fetch_assoc();

    if (password_verify($senha, $aluno['senha'])) {

        $_SESSION['aluno_id'] = $aluno['id'];
        $_SESSION['nome']     = $aluno['nome'];
        $_SESSION['email']    = $aluno['email'];
        $_SESSION['tabela']   = 'Aluno';

        header("Location: painel_aluno.php");
        exit();
    }
}

/* ===============================
   2️⃣ TENTA LOGIN COMO DOCENTE
================================ */
$sqlDocente = "
    SELECT * 
    FROM docentes 
    WHERE email_profissional = '$email'
";

$resultDocente = $conn->query($sqlDocente);

if ($resultDocente && $resultDocente->num_rows === 1) {

    $docente = $resultDocente->fetch_assoc();

    if (password_verify($senha, $docente['senha'])) {

        $_SESSION['docente_id'] = $docente['id'];
        $_SESSION['nome']       = $docente['nome_completo'];
        $_SESSION['email']      = $docente['email_profissional'];
        $_SESSION['tabela']     = 'docentes';

        header("Location: painel_docente.php");
        exit();
    }
}

/* ===============================
   ERRO
================================ */
echo "
<script>
    alert('E-mail ou senha inválidos');
    window.history.back();
</script>
";
exit();
?>
