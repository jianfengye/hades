<?php

namespace Hades\Http\Response;

use Hades\Http\Response;

class HtmlResponse extends Response
{
    public static function make($template, $data = [])
    {
        $response = new JsonResponse();

        $response->setContentType('text/html; charset=UTF-8')
            ->setStatusCode(200);

        ob_start();

        extract($data);
        $file = HADES_ROOT . '/views/' . $template . '.php';

        try {
            include $file;
        } catch (\Exception $e) {

        }

        $body = ob_get_contents();
        $response->setBody($body);
        ob_end_clean();
        return $response;
    }
}
