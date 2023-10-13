<?php

namespace Mustafa\Staller;

class XSS_Protection
{
    public static function inputs(): array
    {
        $inputs = [
            'post' => INPUT_POST,
            'get' => INPUT_GET,
            'cookie' => INPUT_COOKIE,
        ];

        $data = [];

        foreach ($inputs as $key => $inputType) {
            $inputData = filter_input_array($inputType, FILTER_SANITIZE_SPECIAL_CHARS);

            if ($inputData !== null) {
                $data[$key] = array_map([self::class, 'sanitize'], $inputData);
            }
        }

        $json_params = file_get_contents("php://input");
        if (strlen($json_params) > 0 && validator::is_json($json_params)) {
            $JSON = json_decode($json_params, true);
            $data['json'] = array_map([self::class, 'sanitize'], $JSON);
        }

        return $data;
    }

    private static function sanitize($value)
    {
        return validator::sanitizeInput($value);
    }
}
