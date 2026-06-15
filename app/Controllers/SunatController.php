<?php

namespace App\Controllers;

use App\Dows\SunatDow;

class SunatController extends BaseController
{
    private $dow;

    public function __construct()
    {
        $this->dow = new SunatDow();
    }

    public function emit($request)
    {
        return Response::json($this->dow->emit($request));
    }

    public function document($request)
    {
        return Response::json($this->dow->document($request));
    }
}
