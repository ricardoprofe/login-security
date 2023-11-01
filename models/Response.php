<?php
declare(strict_types=1);
class Response
{
    public static function result(int $code, array $response): bool|string{
        header("Content-type:application/json; charset=utf-8'");
        http_response_code($code);
        return json_encode($response);
    }
}