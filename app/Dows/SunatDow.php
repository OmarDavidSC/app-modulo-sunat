<?php

namespace App\Dows;

use App\Services\SunatService;
use App\Utilities\FG;

class SunatDow
{
    public function emit($request)
    {
        $response = FG::responseDefault();
        try {
            $input = $request['body'] ?? [];

            $ruc = $input['empresa']['ruc'];
            $tipo_documento = $input['comprobante']['tipo_documento'];
            $items = $input['items'];

            if (empty($ruc)) {
                $response['success'] = false;
                $response['message'] = 'El Ruc es requerido.';
                return $response;
            }


            if (empty($tipo_documento)) {
                $response['success'] = false;
                $response['message'] = 'El Tipo Documento es requerido.';
                return $response;
            }

            if (empty($items)) {
                $response['success'] = false;
                $response['message'] = 'Los items son requeridos.';
                return $response;
            }

            $service = new SunatService();
            $result = $service->emitDocument($input);

            $sunatResponse = $result['response'] ?? [];

            if (isset($sunatResponse['status']) && $sunatResponse['status'] === 'ERROR') {
                $response['success'] = false;
                $response['data'] = null;
                $response['message'] = $sunatResponse['error']['message'] ?? 'Error al emitir documento en SUNAT';
                return $response;
            }

            $response['success'] = true;
            $response['data'] = $sunatResponse;
            $response['message'] = 'Documento emitido correctamente';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function document($request)
    {
        $response = FG::responseDefault();
        try {
            $input = $request['body'] ?? [];
            $document_id = $request['attributes']['document_id'] ?? null;

            if (empty($document_id)) {
                $response['success'] = false;
                $response['message'] = "El Id del documento es obligatorio.";
                return $response;
            }

            $service = new SunatService();
            $data = $service->getDocumentById($document_id);

            if ($data['http_code'] !== 200) {
                $response['success'] = false;
                $response['message'] = 'Error al obtener documento';
                $response['data'] = $data['response'];
                return $response;
            }

            $response['success'] = true;
            $response['data'] = $data['response'];
            $response['message'] = 'Detalle del documento obtenido correctamente.';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
