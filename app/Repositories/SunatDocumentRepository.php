<?php

namespace App\Repositories;

class SunatDocumentRepository
{
    private $url;

    public function __construct()
    {
        $this->url = 'https://back.apisunat.com';
    }

    public function sendBill($payload)
    {
        return $this->request(
            '/personas/v1/sendBill',
            'POST',
            $payload
        );
    }

    public function getDocumentById($document_id)
    {
        return $this->request(
            '/documents/' . $document_id . '/getById',
            'GET'
        );
    }

    public function getLastCorrelative($persona_id, $persona_token, $type, $serie)
    {
        return $this->request(
            '/personas/lastDocument',
            'POST',
            [
                'personaId' => $persona_id,
                'personaToken' => $persona_token,
                'type' => $type,
                'serie' => $serie
            ]
        );
    }

    private function request($endpoint, $method = 'POST',  $payload = [])
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            throw new \Exception($error);
        }

        return [
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }
}
