<?php

class FavoriteController
{
    private $file;

    public function __construct()
    {
        $this->file = __DIR__ . '/../storage/favorites.json';
    }

    private function load()
    {
        return json_decode(file_get_contents($this->file), true);
    }

    private function save($data)
    {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function respond($data)
    {
        $format = $_GET['format'] ?? 'json';

        if ($format === 'xml') {
            header('Content-Type: application/xml');
            echo $this->arrayToXml(['favorites' => $data], '<root/>');
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function arrayToXml($data, $root)
    {
        $xml = new SimpleXMLElement($root);
        $this->fillXml($xml, $data);
        return $xml->asXML();
    }

    private function fillXml(&$xml, $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $child = $xml->addChild(is_numeric($key) ? "item" : $key);
                $this->fillXml($child, $value);
            } else {
                $xml->addChild(is_numeric($key) ? "item" : $key, htmlspecialchars($value));
            }
        }
    }

    public function index()
    {
        $data = $this->load();
        $this->respond($data);
    }


    public function store()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        if (!isset($body['code']) || !isset($body['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campos obrigatórios: code, name'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $code = strtoupper($body['code']);
        $name = $body['name'];

        $list = $this->load();

        foreach ($list as $fav) {
            if ($fav['code'] === $code) {
                http_response_code(409);
                echo json_encode(['message' => 'País já está nos favoritos'], JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        $list[] = [
            'code' => $code,
            'name' => $name,
        ];

        $this->save($list);

        http_response_code(201);
        echo json_encode(['message' => 'Favorito adicionado'], JSON_UNESCAPED_UNICODE);
    }

    public function show($code)
    {
        $code = strtoupper($code);

        $list = $this->load();

        foreach ($list as $fav) {
            if ($fav['code'] === $code) {
                $this->respond($fav);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['message' => 'Favorito não encontrado'], JSON_UNESCAPED_UNICODE);
    }


    public function delete($code)
    {
        $code = strtoupper($code);

        $list = $this->load();
        $newList = [];

        $found = false;

        foreach ($list as $fav) {
            if ($fav['code'] !== $code) {
                $newList[] = $fav;
            } else {
                $found = true;
            }
        }

        if (!$found) {
            http_response_code(404);
            echo json_encode(['message' => 'Favorito não encontrado'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $this->save($newList);

        echo json_encode(['message' => 'Favorito removido'], JSON_UNESCAPED_UNICODE);
    }
}
