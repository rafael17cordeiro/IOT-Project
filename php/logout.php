<?php
session_start();   // Inicia uma sessão ou retoma uma sessão existente. 
                  // O PHP cria ou recupera uma sessão para armazenar e acessar variáveis de sessão.

session_unset();   // Remove todas as variáveis de sessão armazenadas atualmente. 
                   // Ou seja limpa todos os dados que foram guardados na sessão (como nome de utilizador, etc.).

session_destroy();  // Destroi a sessão atual. 
                    // Isso significa que a sessão será completamente destruída e as variáveis de sessão não estarão mais disponíveis.
                    
header("refresh:0;url=index.php");  // Faz uma redireção imediata para a página "index.php".
                                    // O "0" no parâmetro de tempo significa que o redirecionamento ocorre imediatamente (sem delay).
                                    
?>
