<?php
// Inclui o arquivo que contém a lógica para recuperar os dados dos sensores e atuadores
require_once 'dashboard_controller.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <!-- Bootstrap CSS para estilos rápidos e responsivos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Estilo personalizado -->
    <link rel="stylesheet" href="../css/style.css">
    <!-- Atualiza a página automaticamente a cada 5 segundos -->
    <meta http-equiv="refresh" content="5">
</head>

<body>
    <!-- Menu lateral -->
    <div class="sidebar" id="sidebarMenu">
        <div>
            <!-- Logotipo -->
            <img class="logo" src="../img/logo2.png" alt="Logo" style="width: 220px;">
            <hr>
            <ul class="nav flex-column">
                <!-- Link para a página atual -->
                <li class="nav-item">
                    <a class="nav-link " href="dashboard.php">🏠 Dashboard</a>
                </li>
                <?php
                session_start(); // Certifique-se de que a sessão foi iniciada
                if (isset($_SESSION['username']) && $_SESSION['username'] === 'Admin') {
                    echo '
      <li class="nav-item">
        <a class="nav-link active" href="webcam.php">📷 Webcam</a>
      </li>';
                }
                ?>
            </ul>
        </div>

        <div>
            <!-- Botão de logout -->
            <form action="logout.php">
                <button class="btn logout">
                    <!-- Nome do utilizador a ser exibido com proteção XSS -->
                    <?php echo htmlspecialchars($username); ?> Logout &nbsp;
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <div class="content mt-5">
        <div class="container">
    <!-- Botão para mostrar o menu em dispositivos móveis -->
    <button class="btn d-lg-none" onclick="toggleSidebar()">☰ Menu</button>
    
    <!-- Card para exibir a imagem -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-camera me-2"></i>Visualização da Webcam
            </h5>
        </div>
        
        <div class="card-body text-center">
            <?php
    $imageUrl = 'https://iot.dei.estg.ipleiria.pt/ti/ti085/api/files/camera/webcam.jpg';
    $timestamp = time(); // Para evitar cache
    
    // Verifica se a imagem existe remotamente (opcional)
    $headers = @get_headers($imageUrl);
    $imageExists = $headers && strpos($headers[0], '200');
    
    if ($imageExists) {
        echo "<img src='{$imageUrl}?ts={$timestamp}' alt='Imagem da Webcam' class='img-fluid' style='max-width:100%; height:auto;'>";
    } else {
        echo "<div class='alert alert-warning'>A webcam está temporariamente indisponível</div>";
    }
    ?>
        </div>
        <div class="card-footer text-muted">
            Última atualização: <span id="lastUpdate"><?php echo date('H:i:s'); ?></span>
        </div>
    </div>
</div>
    </div>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Função para alternar exibição da sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>
</body>

</html>