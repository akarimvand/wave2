<?php
class CSRF
{
    public static function generate()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validate($token)
    {
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function field()
    {
        return '<input type="hidden" name="_token" value="' . e(self::generate()) . '">';
    }
}

function csrf_field()
{
    return CSRF::field();
}

function csrf_token()
{
    return CSRF::generate();
}