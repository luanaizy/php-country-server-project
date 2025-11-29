<?php

require_once __DIR__ . '/../services/CountryLayerService.php';

class CountryController
{
    public function index()
    {
        $service = new CountryLayerService();
        $data = $service->getAllCountries();

        $this->respond($data);
    }

    public function show($code)
    {
        $service = new CountryLayerService();
        $data = $service->getCountry($code);

        if ($data === null) {
            http_response_code(404);
            echo json_encode(["message" => "País não encontrado"], JSON_UNESCAPED_UNICODE);
            return;
        }

        $this->respond($data);
    }

    public function proto()
    {

        require_once __DIR__ . '/../tools/proto/vendor/autoload.php';
        require_once __DIR__ . '/../tools/proto/GPBMetadata/Api/Proto/Countries.php';
        require_once __DIR__ . '/../tools/proto/Proto/Country.php';
        require_once __DIR__ . '/../tools/proto/Proto/CountryList.php';

        $service = new CountryLayerService();
        $countries = $service->getAllCountries();

        $list = new \Proto\CountryList();

        foreach ($countries as $c) {

            $country = new \Proto\Country();
            $country->setName($c['name'] ?? '');
            $country->setAlpha2Code($c['alpha2Code'] ?? '');
            $country->setRegion($c['region'] ?? '');

            $list->getCountries()[] = $country;
        }

        header('Content-Type: application/x-protobuf');
        echo $list->serializeToString();
    }


    private function respond($data)
    {
        $format = $_GET['format'] ?? 'json';

        if ($format === "xml") {
            header('Content-Type: application/xml');
            echo $this->arrayToXml($data, "<root/>");
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
}
