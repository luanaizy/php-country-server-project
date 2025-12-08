<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require_once __DIR__ . '/tools/proto/vendor/autoload.php';
require_once __DIR__ . '/controllers/CountryController.php';
require_once __DIR__ . '/controllers/FavoriteController.php';


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header('Access-Control-Allow-Origin: *');

if ($uri === "/api/countries" && $method === "GET") {
    (new CountryController())->index();
    exit;
}

if (preg_match('#^/api/countries/([A-Za-z]{2})$#', $uri, $m) && $method === "GET") {
    (new CountryController())->show($m[1]);
    exit;
}

if ($uri === "/api/favorites" && $method === "GET") {
    (new FavoriteController())->index();
    exit;
}

if ($uri === "/api/favorites" && $method === "POST") {
    (new FavoriteController())->store();
    exit;
}

if (preg_match("#^/api/favorites/([A-Za-z]{2})$#", $uri, $m) && $method === "GET") {
    (new FavoriteController())->show($m[1]);
    exit;
}

if (preg_match('#^/api/favorites/([A-Za-z]{2})$#', $uri, $m) && $method === "DELETE") {
    (new FavoriteController())->delete($m[1]);
    exit;
}

if ($uri === "/api/countries-proto" && $method === "GET") {
    (new CountryController())->proto();
    exit;
}

http_response_code(404);
echo json_encode(["error" => "Rota inexistente"], JSON_UNESCAPED_UNICODE);