<?php
// Ajuste o caminho conforme o seu webroot
$dest = __DIR__ . '/files/camera/webcam.jpg';


if (isset($_FILES['webcam']) && $_FILES['webcam']['error'] === UPLOAD_ERR_OK) {
    move_uploaded_file($_FILES['webcam']['tmp_name'], $dest);
    http_response_code(200);
    echo 'OK';
} else {
    http_response_code(400);
    echo 'Erro no upload';
}
?>