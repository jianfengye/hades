<?php

namespace Hades\Http\Response;

use Hades\Http\Response;

class RedirectResponse extends Response
{
    public static function make($url)
    {
        $response = new RedirectResponse();

        $response->addHeader('Location', $url)
            ->setStatusCode(301);

        $response->setRawData = $url;
        
        return $response;
    }
}
