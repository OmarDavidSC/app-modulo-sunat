<?php

namespace App\Services;

use App\Repositories\SunatPayloadRepository;
use App\Repositories\SunatDocumentRepository;

class SunatService
{
    private $payloadRepository;
    private $documentRepository;

    public function __construct()
    {
        $this->payloadRepository = new SunatPayloadRepository();
        $this->documentRepository = new SunatDocumentRepository();
    }

    public function emitDocument($data)
    {
        $empresa = $data['empresa'];
        $comp = $data['comprobante'];

        $persona_id  = $empresa['persona_id'];
        $persona_token  = $empresa['persona_token'];
        $type = $comp['tipo_documento'];
        $serie = $comp['serie'];

        //ultimo correlativo
        $last_correlative = $this->documentRepository->getLastCorrelative($persona_id, $persona_token, $type, $serie);

        //obtener el correlativo sugerido por sunat
        $response  = $last_correlative['response'];
        if(!isset($response['suggestedNumber'])) {
            throw new \Exception('No se pudo obtener el correlativo');
        }

        $data['comprobante']['correlativo'] = $response['suggestedNumber'];

        $payload = $this->payloadRepository->build($data);
        return $this->documentRepository->sendBill($payload);
    }

    public function getDocumentById($document_id){
        return $this->documentRepository->getDocumentById($document_id);
    }
}
