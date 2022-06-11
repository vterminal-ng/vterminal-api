<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumeExternalService
{

    public function performRequest($method, $endpoint, $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri
        ]);

        if (isset($this->secret)) {
            $headers['Authorization'] = "Bearer $this->secret";
        }

        $reponse = $client->request($method, $endpoint, ['form_params' => $formParams, 'headers' => $headers]);

        return $reponse->getBody()->getContents();
    }
}
