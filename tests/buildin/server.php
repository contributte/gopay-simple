<?php

$type = $_GET['type'];

switch ($type) {
    case 'server':
        echo @json_encode($_SERVER);
        break;
    case 'get':
        echo @json_encode($_GET);
        break;
    case 'post':
        echo trim(file_get_contents('php://input'));
        break;
    default:
        echo @json_encode(['errors' => [0 => ['error_code' => 1, 'scope' => 'S', 'field' => 'S', 'message' => 'failed mocking']]]);
}
