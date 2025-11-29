<?php

class CountryLayerService
{
    private $key = "901269b3cf1fabac2ed00aedc4a5f6ab";

    public function getAllCountries()
    {
        $url = "https://api.countrylayer.com/v2/all?access_key={$this->key}";
        $data = file_get_contents($url);
        return json_decode($data, true);
    }

    public function getCountry($code)
    {
        $url = "https://api.countrylayer.com/v2/alpha/{$code}?access_key={$this->key}";
        $data = @file_get_contents($url);

        if ($data === false) return null; // retorna 404 no controller

        return json_decode($data, true);
    }
}
