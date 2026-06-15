<?php

namespace App\Dows;

use Illuminate\Database\Capsule\Manager as DB;
use App\Utilities\FG;

class HomeDow
{

    public function index($request)
    {
        $response = FG::responseDefault();
        try {

            $general = [];

            $response['success'] = true;
            $response['data']    = null;
            $response['message'] = 'successfully';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
