<?php

    if (isset($_POST['source'])) {
        file_put_contents('sandbox.php', trim($_POST['source']));
    }

    header('Content-Type: application/json');

    echo json_encode(['status'=>'ok']);
