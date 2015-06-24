<?php

namespace Hades\Http\Response;

use Hades\Http\Response;

class JsonResponse extends Response
{

    public function make($data, $code = 200)
    {
        $this->setContentType('application/json')
            ->setStatusCode($code)
            ->setBody(json_encode($data));
        return $this;
    }
}