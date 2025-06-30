<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
  header("refresh:2;url=index.php");
  die("Acesso Restrito");
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <!-- Barra lateral -->
  <div class="sidebar" id="sidebarMenu">
    <div>
      <img class="logo" src="../img/logo2.png" alt="Logo" style="width: 220px;">
      <hr>
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">🏠 Dashboard</a>
        </li>
        <?php
        if ($username === 'Admin') {
          echo '
          <li class="nav-item">
            <a class="nav-link" href="webcam.php">📷 Webcam</a>
          </li>';
        }
        ?>
      </ul>
    </div>
    <div>
      <form action="logout.php">
        <button class="btn logout">
          <?php echo htmlspecialchars($username); ?> Logout &nbsp;
          <i class="fas fa-sign-out-alt"></i>
        </button>
      </form>
    </div>
  </div>

  <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

  <!-- Conteúdo principal -->
  <div class="content mt-5">
    <div class="container">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="content p-4">
          <button class="btn d-lg-none mb-3" onclick="toggleSidebar()">☰ Menu</button>

          <?php
          if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['nome'])) {
            $nome = $_GET['nome'];
            $caminho = "../api/files/{$nome}/log.txt";

            if (file_exists($caminho)) {
              echo "<div class='container'>";

              // Exibir gráfico apenas se for Admin
              if ($username === 'Admin' && !in_array(strtolower($nome), ['arcondicionado', 'ledgabinetes', 'sensorpessoas'])) {
                echo "<div class='my-5'>";
                echo "<h2>Gráfico de Valores</h2>";
                echo "<canvas id='graficoSensor'></canvas>";
                echo "</div>";
              }

              // Ler o conteúdo do log
              $conteudo = file_get_contents($caminho);
              $linhas = array_filter(explode(PHP_EOL, $conteudo));

              // Arrays para o gráfico
              $horas = [];
              $valores = [];

              foreach ($linhas as $linha) {
                if (trim($linha) !== '') {
                  list($hora, $valor) = explode(';', $linha);
                  $horas[] = $hora;
                  $valores[] = (float) $valor;
                }
              }

              // Tabela de histórico
              echo "<div class='content mb-5'>";
              echo "<h2>Histórico de " . htmlspecialchars($nome) . "</h2>";
              echo "<table class='table'>";
              echo "<thead><tr><th>Data/Hora</th><th>Valor</th>";

              if (!in_array(strtolower($nome), ['arcondicionado', 'ledgabinetes', 'contadorpessoas'])) {
                echo "<th>Estado</th>";
              }

              echo "</tr></thead><tbody>";

              foreach (array_reverse($linhas) as $linha) {
                if (trim($linha) !== '') {
                  list($hora, $valor) = explode(';', $linha);
                  echo "<tr><td>{$hora}</td><td>{$valor}</td>";

                  $estado = '';
                  $cor = '';

                  if (strtolower($nome) == 'temperatura') {
                    if ($valor >= 30) {
                      $estado = "Elevado";
                      $cor = "danger";
                    } elseif ($valor <= 15) {
                      $estado = "Baixo";
                      $cor = "primary";
                    } else {
                      $estado = "Normal";
                      $cor = "success";
                    }
                    echo "<td><span class='badge rounded-pill text-bg-{$cor}'>{$estado}</span></td>";
                  }

                  if (strtolower($nome) == 'gabinetes') {
                    if ($valor > 0) {
                      $estado = "Disponíveis";
                      $cor = "success";
                    } else {
                      $estado = "Indisponíveis";
                      $cor = "danger";
                    }
                    echo "<td><span class='badge rounded-pill text-bg-{$cor}'>{$estado}</span></td>";
                  }

                  if (strtolower($nome) == 'sensorpessoas') {
                    if ($valor == 0) {
                      $estado = "Saiu";
                      $cor = "danger";
                    } elseif ($valor == 1) {
                      $estado = "Entrou";
                      $cor = "success";
                    }
                    echo "<td><span class='badge rounded-pill text-bg-{$cor}'>{$estado}</span></td>";
                  }

                  echo "</tr>";
                }
              }

              echo "</tbody></table>";
              echo "</div></div>";
            } else {
              echo "<div class='alert alert-danger'>Ficheiro log para '{$nome}' não existe.</div>";
            }
          } else {
            echo "<div class='alert alert-warning'>Parâmetro 'nome' não especificado ou método inválido.</div>";
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <?php if (
    isset($username, $horas, $valores, $nome) &&
    $username === 'Admin' &&
    !in_array(strtolower($nome), ['arcondicionado', 'ledgabinetes', 'sensorpessoas'])
  ): ?>
    <script>
      const canvas = document.getElementById('graficoSensor');
      if (canvas) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: <?php echo json_encode($horas); ?>,
            datasets: [{
              label: 'Valor do Sensor',
              data: <?php echo json_encode($valores); ?>,
              fill: true,
              borderColor: 'rgb(99, 167, 255)',
              backgroundColor: 'rgba(0, 141, 184, 0.1)',
              tension: 0.2,
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
          }
        });
      }
    </script>
  <?php endif; ?>

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
