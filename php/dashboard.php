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
          <a class="nav-link active" href="#">🏠 Dashboard</a>
        </li>
        <?php
  session_start(); // Certifique-se de que a sessão foi iniciada
  if (isset($_SESSION['username']) && $_SESSION['username'] === 'Admin') {
      echo '
      <li class="nav-item">
        <a class="nav-link" href="webcam.php">📷 Webcam</a>
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

      <div class="row d-flex justify-content-center align-items-center">
        <div class="mb-3">
          <h1>Sensores</h1>
        </div>
        <!-- Card Temperatura -->
        <div class="col-sm-4 mb-5">
          <div class="card">
            <div class="card-body">
              <img src="../img/high-temperature.png" alt="Temperatura" style="width: 128px;">
            </div>
            <div class="card-footer p-4">
              <div class="card-title sensor text-start">
                <h5>
                  <strong><?php echo htmlspecialchars($nome_temperatura . ": " . $valor_temperatura . "°C"); ?></strong>
                </h5>
              </div>
              <p class="card-text text-start">
                <strong>Atualização:</strong> <?php echo htmlspecialchars($hora_temperatura); ?>
                <a href="historico.php?nome=Temperatura">Histórico</a>
              </p>
            </div>
          </div>
        </div>
        <!-- Card Sensor de pessoas -->
        <div class="col-sm-4 mb-5">
          <div class="card">
            <div class="card-body">
              <img src="../img/motion-sensor.png" alt="Sensor Pessoas" style="width: 128px;">
            </div>
            <div class="card-footer p-4">
              <div class="card-title sensor text-start">
                <h5><strong><?php echo htmlspecialchars($nome_sensorPessoas); ?></strong></h5>
              </div>
              <p class="card-text text-start">
                <strong>Atualização:</strong> <?php echo htmlspecialchars($hora_sensorPessoas); ?>
                <a href="historico.php?nome=SensorPessoas">Histórico</a>
              </p>
            </div>
          </div>
        </div>
        <!-- Card gabinetes -->
        <div class="col-sm-4 mb-5">
          <div class="card">
            <div class="card-body">
              <img src="../img/meeting.png" alt="Gabinetes" style="width: 128px;">
            </div>
            <div class="card-footer p-4">
              <div class="card-title sensor text-start">
                <h5><strong><?php echo htmlspecialchars($nome_gabinetes . ": " . $valor_gabinetes); ?></strong></h5>
              </div>
              <p class="card-text text-start">
                <strong>Atualização:</strong> <?php echo htmlspecialchars($hora_gabinetes); ?>
                <a href="historico.php?nome=Gabinetes">Histórico</a>
              </p>
              
            </div>
          </div>
        </div>
      </div>
      <!-- Seção dos atuadores -->
      <div class="row d-flex justify-content-center align-items-center">
        <div class="mb-3">
          <h1>Atuadores</h1>
        </div>

        <!-- Card do ar-condicionado -->
        <div class="col-sm-4 mb-5">
    <div class="card">
        <div class="card-body">
            <?php
            // Escolhe a imagem com base no estado do ar-condicionado e temperatura
            if ($valor_arcondicionado == 'Ligado' && $valor_temperatura >= 30) {
                echo '<img src="../img/air-conditioner-cold.png" alt="Ar Condicionado Ligado (Frio)" style="width: 128px;">';
            } else if ($valor_arcondicionado == 'Ligado' && $valor_temperatura <= 15) {
                echo '<img src="../img/air-conditioner-hot.png" alt="Ar Condicionado Ligado (Quente)" style="width: 128px;">';
            } else {
                echo '<img src="../img/air-conditioner-off.png" alt="Ar Condicionado Desligado" style="width: 128px;">';
            }
            ?>
        </div>

        <div class="card-footer p-4">
            <div class="card-title sensor text-start">
                <h5>
                    <strong><?php echo htmlspecialchars($nome_arcondicionado . ": " . $valor_arcondicionado); ?></strong>
                </h5>
            </div>
            <p class="card-text text-start">
                <strong>Atualização:</strong> <?php echo htmlspecialchars($hora_arcondicionado); ?>
                <a href="historico.php?nome=arcondicionado">Histórico</a>
            </p>
  
        </div>
    </div>
</div>

        <!-- Card do contador de pessoas -->
        <div class="col-sm-4 mb-5">
      <div class="card">
        <div class="card-body">
            <?php
            if ($valor_contadorPessoas == 0) {
                // Sala vazia [Image of an empty room icon]
                echo '<img src="../img/studyroom.png" alt="Sala Vazia" style="width: 128px;">';
            } else {
                // Sala ocupada 
                echo '<img src="../img/training.png" alt="Sala Ocupada" style="width: 128px;">';
            }
            ?>
        </div>
        <div class="card-footer p-4">
            <div class="card-title sensor text-start">
                <h5>
                    <strong><?php echo htmlspecialchars($nome_contadorPessoas . ": " . $valor_contadorPessoas); ?></strong>
                </h5>
            </div>
            <p class="card-text text-start">
                <strong>Atualização:</strong> <?php echo htmlspecialchars($hora_contadorPessoas); ?>
                <a href="historico.php?nome=ContadorPessoas">Histórico</a>
            </p>
            <div class="d-flex justify-content-between mt-2">
                <form action="update_person_count.php" method="post" class="w-48">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="btn btn-success w-100">+</button>
                </form>
                <form action="update_person_count.php" method="post" class="w-48">
                    <input type="hidden" name="action" value="remove">
                    <button type="submit" class="btn btn-danger w-100" <?php echo ($valor_contadorPessoas <= 0) ? 'disabled' : ''; ?>>-</button>
                </form>
            </div>
        </div>
    </div>
</div>

        <!-- Card para o estado dos gabinetes (LED) -->
        <div class="col-sm-4 mb-5">
          <div class="card">
            <div class="card-body">
              <?php
              if ($valor_ledGabinetes == 'Indisponivel') {
                echo '<img src="../img/closed.png" alt="Indisponível" style="width: 128px;">';
              } else {
                echo '<img src="../img/available.png" alt="Disponível" style="width: 128px;">';
              }
              ?>
            </div>
            <div class="card-footer p-4">
              <div class="card-title sensor text-start">
                <h5><strong>
                    <?php
                    // Mostra o nome do sensor e se está disponível ou não
                    echo htmlspecialchars($nome_ledGabinetes . ": ");
                    if ($valor_gabinetes == 0) {
                      echo 'Indisponiveis';
                    } else {
                      echo 'Disponiveis';
                    }
                    ?>
                  </strong></h5>
              </div>
              <p class="card-text text-start">
                <strong>Atualização:</strong> <?php echo htmlspecialchars($hora_ledGabinetes); ?>
                <a href="historico.php?nome=LedGabinetes">Histórico</a>
              </p>
              
                  
            </div>
              
          </div>
        </div>
      </div>
    </div>

    <div class="container mb-5">
      <div class="card">
        <div class="card-header">
          <h2>Tabela de Sensores</h2>
        </div>
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th class="bg-light" scope="col">Sensor</th>
                <th class="bg-light" scope="col">Valor</th>
                <th class="bg-light" scope="col">Data Atualização</th>
                <th class="bg-light" scope="col">Estado Alertas</th>
              </tr>
            </thead>
            <tbody>
              <!-- Linha do sensor de temperatura -->
              <tr>
                <td class="bg-light"><?php echo htmlspecialchars($nome_temperatura); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($valor_temperatura); ?> Cº</td>
                <td class="bg-light"><?php echo htmlspecialchars($hora_temperatura); ?></td>
                <td class="bg-light">
                  <?php
                  // Define alerta com base no valor da temperatura
                  if ($valor_temperatura > 30 ) {
                    echo "<span class='badge rounded-pill text-bg-danger'>Elevado</span>";
                  } elseif ($valor_temperatura < 15) {
                    echo "<span class='badge rounded-pill text-bg-warning'>Baixo</span>";
                  } else {
                    echo "<span class='badge rounded-pill text-bg-primary'>Normal</span>";
                  }
                  ?>
                </td>
              </tr>
              <!-- Linha do sensor de pessoas (entrada/saída) -->
              <tr>
                <td class="bg-light"><?php echo htmlspecialchars($nome_sensorPessoas); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($valor_sensorPessoas); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($hora_sensorPessoas); ?></td>
                <td class="bg-light">
                  <?php
                  // Mostra o estado baseado se entrou ou saiu alguém
                  if ($valor_sensorPessoas == 0) {
                    echo "<span class='badge rounded-pill text-bg-danger'>Saio</span>";
                  } elseif ($valor_sensorPessoas == 1) {
                    echo "<span class='badge rounded-pill text-bg-success'>Entrou</span>";
                  }
                  ?>
                </td>
              </tr>
              <!-- Linha do sensor de gabinetes (quantidade de gabinetes livres) -->
              <tr>
                <td class="bg-light"><?php echo htmlspecialchars($nome_gabinetes); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($valor_gabinetes); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($hora_gabinetes); ?></td>
                <td class="bg-light">
                  <?php
                  // Mostra alerta com base no número de gabinetes livres
                  if ($valor_gabinetes == 0) {
                    echo "<span class='badge rounded-pill text-bg-danger'>Livres</span>";
                  } elseif ($valor_gabinetes < 5) {
                    echo "<span class='badge rounded-pill text-bg-warning'>Livres</span>";
                  } else {
                    echo "<span class='badge rounded-pill text-bg-success'>livres</span>";
                  }
                  ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="card">
        <div class="card-header">
          <h2>Tabela de Atuadores</h2>
        </div>
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th class="bg-light" scope="col">Atuador</th>
                <th class="bg-light" scope="col">Data Atualização</th>
                <th class="bg-light" scope="col">Estado</th>
              </tr>
            </thead>
            <tbody>
              <!-- Linha do ar condicionado -->
              <tr>
                <td class="bg-light"><?php echo htmlspecialchars($nome_arcondicionado); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($hora_arcondicionado); ?></td>
                <td class="bg-light">
                  <?php
                  if ($valor_arcondicionado == "Ligado") {
                    // Se estiver ligado, badge verde (success)
                    echo "<span class='badge rounded-pill text-bg-success'>$valor_arcondicionado</span>";
                  } else {
                    // Se estiver desligado, badge cinza (secondary)
                    echo "<span class='badge rounded-pill text-bg-secondary'>$valor_arcondicionado</span>";
                  }
                  ?>
                </td>
              </tr>
              <!-- Linha do contadorPessoas -->
              <tr>
                <td class="bg-light"><?php echo htmlspecialchars($nome_contadorPessoas); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($hora_contadorPessoas); ?></td>
                <td class="bg-light">
                  <?php
                  if ($valor_contadorPessoas == 0) {
                    // Se ninguém estiver presente, badge vermelha
                    echo "<span class='badge rounded-pill text-bg-danger'>$valor_contadorPessoas</span>";
                  } else {
                    // Se houver pessoas, badge verde
                    echo "<span class='badge rounded-pill text-bg-success'>$valor_contadorPessoas</span>";
                  }
                  ?>
                </td>
              </tr>
              <!-- Linha da iluminação (LED dos gabinetes) -->
              <tr>
                <td class="bg-light"><?php echo htmlspecialchars($nome_ledGabinetes); ?></td>
                <td class="bg-light"><?php echo htmlspecialchars($hora_ledGabinetes); ?></td>
                <td class="bg-light">
                  <?php
                  if ($valor_ledGabinetes == 'Indisponivel') {
                    // Se os gabinetes não estiverem disponíveis, badge vermelha
                    echo "<span class='badge rounded-pill text-bg-danger'>Indisponiveis</span>";
                  } else {
                    // Se estiverem disponíveis, badge verde
                    echo "<span class='badge rounded-pill text-bg-success'>Disponiveis</span>";
                  }
                  ?>
                </td>
              </tr>
            </tbody>
          </table>
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