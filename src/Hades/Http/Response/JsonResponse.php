<?php

namespace Hades\Http\Response;

use Hades\Http\Response;

class JsonResponse extends Response
{
    public static function make($data = [], $code = 200)
    {
        $response = new JsonResponse();

        $response->setContentType('application/json')
            ->setStatusCode($code)
            ->setBody(json_encode($data));

        $response->setRawData($data);

        return $response;
    }
}
