<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

/* ===============================
   CONEXÃO
================================ */
$conn = new mysqli("localhost", "root", "", "connectti");
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

/* ===============================
   PROTEÇÃO
================================ */
if (!isset($_SESSION['id'], $_SESSION['tipo'])) {
    header("Location: login.php");
    exit;
}

$id   = (int)$_SESSION['id'];
$tipo = $_SESSION['tipo'];

/* ===============================
   BUSCAR USUÁRIO
================================ */
$tabela = ($tipo === 'aluno') ? 'aluno' : 'docentes';
$result = $conn->query("SELECT * FROM $tabela WHERE id = $id");

if (!$result || $result->num_rows === 0) {
    die("Usuário não encontrado");
}

$user = $result->fetch_assoc();

/* ===============================
   ALTERAR SENHA
================================ */
if (isset($_POST['alterar_senha'])) {

    $novaSenha = $_POST['nova_senha'];

    if (strlen($novaSenha) < 6) {
        $erro = "A nova senha deve ter no mínimo 6 caracteres.";
    } else if (password_verify($_POST['senha_atual'], $user['senha'])) {

        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $conn->query("UPDATE $tabela SET senha='$hash' WHERE id=$id");
        $msg = "Senha alterada com sucesso!";

    } else {
        $erro = "Senha atual incorreta!";
    }
}

/* ===============================
   SALVAR PERFIL
================================ */
if (isset($_POST['salvar_perfil'])) {

    if (!empty($_FILES['foto']['name'])) {

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidas)) {

            $nomeArquivo = "{$tipo}_{$id}." . $ext;
            $caminhoRelativo = "img/perfis/" . $nomeArquivo;
            $caminhoFisico = __DIR__ . "/../" . $caminhoRelativo;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoFisico)) {
                $conn->query("UPDATE $tabela SET foto='$caminhoRelativo' WHERE id=$id");
                $_SESSION['foto'] = $caminhoRelativo;
            }
        }
    }

    if ($tipo === 'aluno') {

        $nome  = $_POST['nome'];
        $email = $_POST['email'];
        $data  = $_POST['data_nascimento'];

        $conn->query("
            UPDATE aluno SET
                nome='$nome',
                email='$email',
                data_nascimento='$data'
            WHERE id=$id
        ");

        $_SESSION['nome']  = $nome;
        $_SESSION['email'] = $email;

    } else {

        $nome        = $_POST['nome_completo'];
        $email       = $_POST['email_profissional'];
        $formacao    = $_POST['formacao_academica'];
        $instituicao = $_POST['instituicao'];

        $conn->query("
            UPDATE docentes SET
                nome_completo='$nome',
                email_profissional='$email',
                formacao_academica='$formacao',
                instituicao='$instituicao'
            WHERE id=$id
        ");

        $_SESSION['nome']  = $nome;
        $_SESSION['email'] = $email;
    }

    header("Location: painel.php");
    exit;
}

/* FOTO */
$fotoAtual = !empty($user['foto'])
    ? "../" . $user['foto']
    : "../img/Logo ConnectTI.png";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Painel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">
<div class="card shadow mx-auto" style="max-width:720px">
<div class="card-body p-4 position-relative">

<a href="index.php" class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-3">✕</a>

<h3 class="mb-4">Editar Perfil</h3>

<?php if (!empty($erro)): ?>
<div class="alert alert-danger"><?= $erro ?></div>
<?php endif; ?>

<?php if (!empty($msg)): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="salvar_perfil">

<div class="text-center mb-4">
    <img src="<?= $fotoAtual ?>" class="rounded-circle mb-2"
         style="width:120px;height:120px;object-fit:cover">
    <input type="file" name="foto" class="form-control mt-2">
</div>

<?php if ($tipo === 'aluno'): ?>

<div class="mb-3">
<label>Nome</label>
<input type="text" name="nome" class="form-control" value="<?= $user['nome'] ?>">
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" value="<?= $user['email'] ?>">
</div>

<div class="mb-3">
<label>Data de nascimento</label>
<input type="date" name="data_nascimento" class="form-control" value="<?= $user['data_nascimento'] ?>">
</div>

<?php else: ?>

<div class="mb-3">
<label>Nome completo</label>
<input type="text" name="nome_completo" class="form-control" value="<?= $user['nome_completo'] ?>">
</div>

<div class="mb-3">
<label>Email profissional</label>
<input type="email" name="email_profissional" class="form-control" value="<?= $user['email_profissional'] ?>">
</div>

<div class="mb-3">
<label>Formação acadêmica</label>
<select name="formacao_academica" class="form-select" required>
<?php
$formacoes = [
"Ciência da Computação","Engenharia de Software","Sistemas de Informação",
"Análise e Desenvolvimento de Sistemas","Engenharia da Computação"
];
foreach ($formacoes as $f) {
    $selected = ($user['formacao_academica'] === $f) ? 'selected' : '';
    echo "<option value=\"$f\" $selected>$f</option>";
}
?>
</select>
</div>

<div class="mb-3">
<label>Instituição</label>
<select name="instituicao" class="form-select" required>
<?php
$inst = [
"UFPE - Universidade Federal de Pernambuco",
"UNICAP - Universidade Católica de Pernambuco",
"UPE - Universidade de Pernambuco"
];
foreach ($inst as $i) {
    $selected = ($user['instituicao'] === $i) ? 'selected' : '';
    echo "<option value=\"$i\" $selected>$i</option>";
}
?>
</select>
</div>

<?php endif; ?>

<button class="btn btn-primary w-100">Salvar alterações</button>
</form>

<hr class="my-4">

<button class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#modalSenha">
Trocar senha
</button>

</div>
</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalSenha" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Alterar senha</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="post">
<input type="hidden" name="alterar_senha">

<div class="modal-body">

<div class="mb-3">
<label>Senha atual</label>
<input type="password" name="senha_atual" class="form-control" required>
</div>

<div class="mb-3">
<label>Nova senha</label>
<input type="password" name="nova_senha" class="form-control" minlength="6" required>
<small class="text-muted">Mínimo de 6 caracteres</small>
</div>

</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
<button class="btn btn-dark">Alterar senha</button>
</div>

</form>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>