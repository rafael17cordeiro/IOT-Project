<?php
// Iniciar a sessão para controlo de acesso
session_start();
// Verificar se o utilizador tem sessão iniciada
if (!isset($_SESSION['username'])) {
  // Redirecionar para a página de login após 2 segundos se não estiver autenticado
  header("refresh:2;url=index.php");
  die("Acesso Restrito");
}
// Obter o nome de utilizador da sessão
$username = $_SESSION['username'];

// -------------------------------------------------------------------
// LEITURA DOS DADOS DOS SENSORES E ATUADORES
// -------------------------------------------------------------------


// Variaveis para a Temperatura
$valor_temperatura = file_get_contents("../api/files/Temperatura/valor.txt");
$hora_temperatura = file_get_contents("../api/files/Temperatura/hora.txt");
$nome_temperatura = file_get_contents("../api/files/Temperatura/nome.txt");

// Variaveis para o SensorPessoas
$valor_sensorPessoas = file_get_contents("../api/files/SensorPessoas/valor.txt");
$hora_sensorPessoas = file_get_contents("../api/files/SensorPessoas/hora.txt");
$nome_sensorPessoas = file_get_contents("../api/files/SensorPessoas/nome.txt");

// Variaveis para o Gabinetes
$valor_gabinetes = file_get_contents("../api/files/Gabinetes/valor.txt");
$hora_gabinetes = file_get_contents("../api/files/Gabinetes/hora.txt");
$nome_gabinetes = file_get_contents("../api/files/Gabinetes/nome.txt");

// Variaveis para o arcondicionado
$valor_arcondicionado = file_get_contents("../api/files/arcondicionado/valor.txt");
$hora_arcondicionado = file_get_contents("../api/files/arcondicionado/hora.txt");
$nome_arcondicionado = file_get_contents("../api/files/arcondicionado/nome.txt");

// Variaveis para o ledgabinetes
$valor_ledGabinetes = file_get_contents("../api/files/LedGabinetes/valor.txt");
$hora_ledGabinetes = file_get_contents("../api/files/LedGabinetes/hora.txt");
$nome_ledGabinetes = file_get_contents("../api/files/LedGabinetes/nome.txt");

// Variaveis para o Pessoas
$valor_contadorPessoas = file_get_contents("../api/files/ContadorPessoas/valor.txt");
$hora_contadorPessoas = file_get_contents("../api/files/ContadorPessoas/hora.txt");
$nome_contadorPessoas = file_get_contents("../api/files/ContadorPessoas/nome.txt");
?>