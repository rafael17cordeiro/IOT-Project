<?php
// update_person_count.php
header('Content-Type: text/html; charset=utf-8');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se a ação foi especificada
    if (!isset($_POST['action'])) {
        http_response_code(400);
        die("Ação não especificada");
    }

    // Define o valor baseado na ação (1 para adicionar, 0 para remover)
    $valor = ($_POST['action'] == 'add') ? 1 : 0;
    
    // Hora atual
    $hora = date('Y-m-d H:i:s');
    
    // Dados para enviar à API
    $dados = [
        'nome' => 'Gabinetes',
        'valor' => $valor,
        'hora' => $hora
    ];
    
    // Configuração para fazer o POST
    $url = 'https://iot.dei.estg.ipleiria.pt/ti/ti085/api/api.php';
    
    $opcoes = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($dados),
        ],
    ];
    
    $contexto = stream_context_create($opcoes);
    
    try {
        $resultado = file_get_contents($url, false, $contexto);
        
        if ($resultado === FALSE) {
            throw new Exception("Falha na comunicação com a API");
        }
        
        // Redireciona de volta para a página principal
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
        
    } catch (Exception $e) {
        die("Erro: " . $e->getMessage());
    }
} else {
    http_response_code(403);
    echo "Método não permitido";
}
?>