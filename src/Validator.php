<?php

namespace Mustafa\Staller;

class validator
{
    public static function sanitizeInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    public function is_email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$/', $email);
    }

    public function is_url(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function is_number($value): bool
    {
        return is_numeric($value);
    }

    public function is_integer($value): bool
    {
        return is_int($value);
    }

    function is_between(string $string, int $min_length, int $max_length): bool
    {
        $length = strlen($string);
        return $length >= $min_length && $length <= $max_length;
    }

    public static function is_json($value): bool
    {
        return is_string($value) && @json_decode($value, true, 512, JSON_THROW_ON_ERROR) !== null;
    }

    public function is_date(string $date): bool
    {
        return strtotime($date) !== false;
    }

    public function is_boolean($value): bool
    {
        return is_bool($value);
    }

    public function is_alphabetic($value): bool
    {
        return is_string($value) && preg_match('/^[A-Za-z]+$/', $value);
    }

    public function is_alphanumeric($value): bool
    {
        return is_string($value) && preg_match('/^[A-Za-z0-9]+$/', $value);
    }

    public function is_positive_number($value): bool
    {
        return is_numeric($value) && $value > 0;
    }

    public function is_non_negative_number($value): bool
    {
        return is_numeric($value) && $value >= 0;
    }

    public function is_uppercase($value): bool
    {
        return is_string($value) && strtoupper($value) === $value;
    }

    public function is_lowercase($value): bool
    {
        return is_string($value) && strtolower($value) === $value;
    }

    public function is_phone_number($value): bool
    {
        return preg_match('/^[0-9+\-() ]+$/', $value);
    }

    public static function hasController(string $class): bool
    {
        return class_exists($class);
    }

    public static function hasFile(string $path): bool
    {
        return file_exists($path);
    }

    public static function hasMiddleware(string $filename): bool
    {
        return class_exists("App\\middleware\\$filename");
    }

    public static function hasView(string $filename): bool
    {
        return self::hasFile(realpath("../App/views/$filename.php"));
    }
}