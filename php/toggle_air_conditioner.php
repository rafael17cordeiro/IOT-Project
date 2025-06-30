<?php
header('Content-Type: text/html; charset=utf-8');

// Verifica se já foi processado (evita duplicação)
session_start();
if (isset($_SESSION['processing'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
$_SESSION['processing'] = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $arquivo_estado = __DIR__ . '/../../api/files/arcondicionado/valor.txt';
    
    if (!file_exists($arquivo_estado)) {
        file_put_contents($arquivo_estado, 'Desligado');
    }
    
    $estado_atual = trim(file_get_contents($arquivo_estado));
    $novo_estado = (strtolower($estado_atual) === 'ligado') ? 'Desligado' : 'Ligado';
    
    // Atualiza primeiro o arquivo local
    file_put_contents($arquivo_estado, $novo_estado);
    
    $dados = [
        'nome' => 'arcondicionado',
        'valor' => $novo_estado,
        'hora' => date('Y-m-d H:i:s')
    ];
    
    $contexto = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($dados)
        ]
    ]);
    
    try {
        $resultado = file_get_contents(
            'https://iot.dei.estg.ipleiria.pt/ti/ti085/api/api.php',
            false,
            $contexto
        );
        
        // Limpa a flag de processamento
        unset($_SESSION['processing']);
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
        
    } catch (Exception $e) {
        unset($_SESSION['processing']);
        die("Erro: " . $e->getMessage());
    }
} else {
    http_response_code(405);
    echo "Método não permitido";
}
?>