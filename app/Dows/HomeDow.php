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

            $users = [];

            $response['success'] = true;
            $response['data']    = compact('users');
            $response['message'] = 'Se guardo correctamente';
        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
