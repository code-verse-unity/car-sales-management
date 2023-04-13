<?php

namespace App\Core\Requests;

class Request
{
    public $body = [];
    public $params = [];
    public $query = [];

    public function __construct()
    {
        $this->query = $this->sanitizeGetData($_GET);

        if (in_array($this->getMethod(), ["POST", "PUT", "PATCH", "DELETE"])) {
            // Get the raw HTTP request body
            $request_body = file_get_contents('php://input');

            // Check the content type of the request
            $content_type = $_SERVER['CONTENT_TYPE'];

            if (strpos($content_type, 'application/json') !== false) {
                // Handle JSON request body
                $data = json_decode($request_body, true);
            } elseif (strpos($content_type, 'application/xml') !== false) {
                // Handle XML request body
                $data = simplexml_load_string($request_body);
            } else {
                // Handle urlencoded request body
                $data = $this->formatRawBody($request_body);
            }

            $this->body = $data;
        }
    }

    private function sanitizeGetData($get)
    {
        $sanitized = [];

        foreach ($get as $key => $value) {
            $sanitized[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $sanitized;
    }

    private function sanitizePostData($post)
    {
        $sanitized = [];

        foreach ($post as $key => $value) {
            $sanitized[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $sanitized;
    }

    public function getMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'];
        if (false !== $pos = strpos($path, '?')) {
            $path = substr($path, 0, $pos);
        }
        $path = rawurldecode($path);
        return $path;
    }

    public function getURI()
    {
        return $_SERVER["uri"];
    }

    function formatRawBody($rawBody)
    {
        $keyValues = [];

        if (!empty($rawBody)) {
            $params = explode('&', $rawBody);

            foreach ($params as $param) {
                $param_array = explode('=', $param);
                if (count($param_array) === 2) {
                    $key = urldecode($param_array[0]);
                    $value = urldecode($param_array[1]);

                    if (array_key_exists($key, $keyValues)) {
                        $keyValues[$key][] = $value;
                    } else {
                        $keyValues[$key] = [$value];
                    }
                }
            }
        }

        return array_map(
            function ($values) {
                if (count($values) === 1) {
                    return $values[0];
                } else {
                    return $values;
                }
            },
            $keyValues
        );
    }
}