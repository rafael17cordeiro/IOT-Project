<?php
// Inicia a sessão para armazenar dados do utilziador logado
session_start();

// Define o caminho do arquivo que armazena os utilizador e senhas
$users_file = "../users/users.txt";

// Verifica se a requisição é do tipo POST (envio do formulário)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os valores do formulário, com fallback para string vazia se não existirem
    $input_username = $_POST['username'] ?? ''; //"Rafael" ou "Guilherme"
    $input_password = $_POST['password'] ?? ''; // 123

    // Verifica se o arquivo de utilizadores existe
    if (file_exists($users_file)) {
        // Lê todas as linhas do arquivo, ignorando linhas vazias
        $lines = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Percorre cada linha do arquivo
        foreach ($lines as $line) {
            // Divide a linha em username e password_hash usando o : como separador
            list($username, $password_hash) = explode(":", $line, 2);

            // Verifica se o username coincide e se a senha está correta
            if ($input_username === $username && password_verify($input_password, $password_hash)) {
                // Se as credenciais estiverem corretas:
                // 1. Armazena o username na sessão
                $_SESSION['username'] = $username;
                // 2. Redireciona para a dashboard
                header('Location: dashboard.php');
                exit; // Termina a execução do script
            }
        }
    }

    // Redireciona de volta para a página de login com um parâmetro de erro
    header('Location: index.php?login=incorreto');
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <!-- Configuração de viewport para responsividade -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <!-- Container principal fluid (ocupa 100% da largura) -->
    <div class="container-fluid">
        <div class="row">
            <!-- Coluna do formulário de login (ocupa 6 colunas em telas médias/grandes) -->
            <div class="col-md-6 login-side">
                <!-- Formulário de login com método POST -->
                <form class="login-form" method="POST">
                    <h2 class="mb-4 text-center">Bem-Vindo</h2>

                    <!-- Campo de entrada para o nome de usuário -->
                    <div class="mb-3">
                        <label for="exampleInputUser" class="form-label">Nome</label>
                        <input required type="text" class="form-control" id="exampleInputUser"
                            placeholder="Digite o seu nome" name="username">
                    </div>

                    <!-- Campo de entrada para a senha -->
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input required type="password" class="form-control" id="senha"
                            placeholder="Digite a sua password" name="password">
                    </div>

                    <!-- Exibe mensagem de erro se as credenciais estiverem incorretas -->
                    <?php if (isset($_GET['login']) && $_GET['login'] === 'incorreto'): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            Nome de Utilizador ou password incorretos!
                        </div>
                    <?php endif; ?>

                    <!-- Botão de submissão do formulário -->
                    <button type="submit" class="btn btn-primary-costum w-100">Entrar</button>
                </form>
            </div>

            <!-- Coluna da imagem (oculta em telas pequenas) -->
            <div class="col-md-6 image-side d-none d-md-block"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>