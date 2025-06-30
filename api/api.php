<?php
// Define o tipo de conteúdo e charset como HTML UTF-8
header('Content-Type: text/html; charset=utf-8');
// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "O método HTTP utilizado foi: " . $_SERVER['REQUEST_METHOD'] . "<br>";
    // Verifica se os parâmetros obrigatórios foram enviados
    if (isset($_POST['nome'], $_POST['valor'], $_POST['hora'])) {
        echo $_POST['nome'];

        // Grava dados do sensor
        file_put_contents("files/{$_POST['nome']}/nome.txt", $_POST['nome']);
        file_put_contents("files/{$_POST['nome']}/valor.txt", $_POST['valor']);
        file_put_contents("files/{$_POST['nome']}/hora.txt", $_POST['hora']);
        // Grava a leitura no log (acrescenta ao final)
        $linha = $_POST['hora'] . ";" . $_POST['valor'] . PHP_EOL;
        file_put_contents("files/{$_POST['nome']}/log.txt", $linha, FILE_APPEND);
        echo "Dados gravados com sucesso!";
        // Variáveis para identificar o atuador associado e o valor que ele deve assumir
        $atuador = "";
        $valor = "";
    
        
        // Lógica para o sensor de Temperatura
        if ($_POST['nome'] == "Temperatura") {
            $atuador = "arcondicionado";
            $valor = $_POST['valor'] <= 15 || $_POST['valor'] >= 25 ? "Ligado" : "Desligado";
        // Lógica para o sensor de Gabinetes
        } else if ($_POST['nome'] == "Gabinetes") {
            $atuador = "ContadorPessoas";

            // Lê o contador atual, se existir
            $contadorAtual = 0;
            if (file_exists("files/$atuador/valor.txt")) {
                $contadorAtual = (int) file_get_contents("files/$atuador/valor.txt");
            }
            // Atualiza o contador de acordo com o valor recebido (1 = entrou, 0 = saiu)
            if ($_POST['valor'] == 1) {
                $contadorAtual += 1;
            } else if ($_POST['valor'] == 0) {
                $contadorAtual = max(0, $contadorAtual - 1); // evita negativo
            }

            $valor = $contadorAtual;
            $linha2 = $_POST['hora'] . ";" . $contadorAtual . PHP_EOL;

            // Grava novo valor do atuador
            file_put_contents("files/$atuador/valor.txt", $contadorAtual);
            file_put_contents("files/$atuador/hora.txt", $_POST['hora']);
            file_put_contents("files/$atuador/log.txt", $linha2, FILE_APPEND);

            // Pula a gravação abaixo pois já foi feita
            return;
        // Lógica para o Sensor de Pessoas (contador de entrada/saída)
        } else if ($_POST['nome'] == "SensorPessoas") {
            $atuador = "ContadorPessoas";

            // Lê o contador atual, se existir
            $contadorAtual = 0;
            if (file_exists("files/$atuador/valor.txt")) {
                $contadorAtual = (int) file_get_contents("files/$atuador/valor.txt");
            }
            // Atualiza o contador de acordo com o valor recebido (1 = entrou, 0 = saiu)
            if ($_POST['valor'] == 1) {
                $contadorAtual += 1;
            } else if ($_POST['valor'] == 0) {
                $contadorAtual = max(0, $contadorAtual - 1); // evita negativo
            }

            $valor = $contadorAtual;
            $linha2 = $_POST['hora'] . ";" . $contadorAtual . PHP_EOL;

            // Grava novo valor do atuador
            file_put_contents("files/$atuador/valor.txt", $contadorAtual);
            file_put_contents("files/$atuador/hora.txt", $_POST['hora']);
            file_put_contents("files/$atuador/log.txt", $linha2, FILE_APPEND);

            // Pula a gravação abaixo pois já foi feita
            return;
        }

        // Para outros atuadores (Temperatura, Gabinetes), grava log normalmente
        $linha2 = $_POST['hora'] . ";" . $valor . PHP_EOL;
        file_put_contents("files/$atuador/valor.txt", $valor);
        file_put_contents("files/$atuador/hora.txt", $_POST['hora']);
        file_put_contents("files/$atuador/log.txt", $linha2, FILE_APPEND);

    } else {
        // Se faltarem parâmetros obrigatórios
        echo "Erro: Parâmetros ausentes. Certifique-se de enviar 'nome', 'valor' e 'hora'.";
        http_response_code(400);
    }

} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Verifica se o parâmetro 'nome' foi passado
    if (isset($_GET['nome'])) {
        // Se existir o ficheiro com o valor atual, retorna
        if (file_exists("files/{$_GET['nome']}/valor.txt")) {
            $valor = file_get_contents("files/{$_GET['nome']}/valor.txt");
            echo $valor;
        } else {
            http_response_code(400); // Ficheiro não encontrado
        }
    } else {
        http_response_code(400); // Parâmetro 'nome' ausente
    }

} else {
    http_response_code(403); // Método não permitido
}
?>