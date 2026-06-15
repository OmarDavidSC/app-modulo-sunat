<?php

namespace App\Utilities;

class FG
{

    public static function getDefaultErrorMessage()
    {
        return "Este servicio no se encuentra disponible en estos instantes. Inténtelo más tarde.";
    }

    public static function responseDefault()
    {
        return ['success' => false, 'data' => null, 'message' => self::getDefaultErrorMessage(), 'status' => 200, 'errors' => []];
    }

    public static function responseJSONDefault()
    {
        return json_decode(json_encode(self::responseDefault()));
    }

    public static function isEmail($str = "")
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public static function response(
        bool $success = false,
        mixed $data = null,
        string $message = '',
        int $status = 200,
        array $errors = []
    ) {
        return [
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'status' => $status,
            'errors' => $errors
        ];
    }

    public static function rand_string($lenght = 10, $characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789")
    {
        $string = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $lenght; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        return $string;
    }

    function getMinuts($fecha1, $fecha2)
    {
        $fecha1 = str_replace('/', '-', $fecha1);
        $fecha2 = str_replace('/', '-', $fecha2);
        $fecha1 = strtotime($fecha1);
        $fecha2 = strtotime($fecha2);
        return round(($fecha2 - $fecha1) / 60);
    }

    public static function getRealIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getDateHour($format = "Y-m-d H:i:s")
    {
        date_default_timezone_set('America/Lima');
        $fecha = date($format);
        return $fecha;
    }

    public static function getHour($format = "H:i:s")
    {
        date_default_timezone_set('America/Lima');
        $fecha = date($format);
        return $fecha;
    }

    public static function getDateString($date)
    {
        $arrayFN = ['Error', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $mes = (int) substr($date, 5, 2);
        $anio = (int) substr($date, 0, 4);
        $mes = isset($arrayFN[$mes]) ? $arrayFN[$mes] : 'No encontrado';
        $salida = $mes . ' ' . $anio;
        return $salida;
    }

    public static function getSizeRound($size, $unit = 'B', $precision = 2)
    {
        switch (strtoupper($unit)) {
            case 'KB':
                $size = round($size / 1024, 4);
                break;
            case 'MB':
                $size = round($size / 1024 / 1024, 4);
                break;
            case 'GB':
                $size = round($size / 1024 / 1024 / 1024, 4);
                break;
        }
        return round($size, $precision);
    }

    public static function getRound($valor)
    {
        return round($valor, 2);
    }

    public static function fixSpecialCharecters($str, $except = "", $replace = "")
    {
        $replaced = str_replace(
            ["á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ"],
            ["a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n"],
            $str
        );
        return preg_replace('/[^A-Za-z0-9\\' . $except . ']/', $replace, $replaced);
    }

    public static function slugify($str, $pattern = '-')
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', $pattern, ''), str_replace($a, $b, $str)));
    }

    public static function getDomain()
    {
        return str_replace("www.", "", $_SERVER['HTTP_HOST']);
    }

    public static function getFechaHora_text()
    {
        date_default_timezone_set('America/Lima');
        $fecha = date("d F, Y");
        return $fecha;
    }

    public static function getYear()
    {
        date_default_timezone_set('America/Lima');
        $fecha = date("Y");
        return $fecha;
    }

    public static function step($message)
    {
        return ['datetime' => FG::getDateHour(), 'message' => $message];
    }

    public static function removePortFromUrl($url)
    {
        $parsedUrl = parse_url($url);

        // Si la URL no tiene un puerto, simplemente retorna la URL original
        if (!isset($parsedUrl['port'])) {
            return $url;
        }

        // Reconstruir la URL sin el puerto
        $cleanUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        // Añadir el path si existe
        if (isset($parsedUrl['path'])) {
            $cleanUrl .= $parsedUrl['path'];
        }

        // Añadir el query string si existe
        if (isset($parsedUrl['query'])) {
            $cleanUrl .= '?' . $parsedUrl['query'];
        }

        // Añadir el fragmento si existe
        if (isset($parsedUrl['fragment'])) {
            $cleanUrl .= '#' . $parsedUrl['fragment'];
        }

        return $cleanUrl;
    }

    public static function uniqid()
    {
        return strtolower(md5(uniqid(time())));
    }

    public static function keyTypeLabel($type)
    {
        switch ($type) {
            case 'radio':
                return 'Selectiva';
            case 'checkbox':
                return 'Multiselectiva';
            case 'libre':
                return 'Abierta';
            default:
                return 'Personalizada';
        }
    }

    public static function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public static function convertDatetimeIsoToDateHour($recordingStart)
    {
        // $recordingStart = "2025-01-07T20:21:20Z";
        date_default_timezone_set('America/Lima');
        $dateTime = new \DateTime($recordingStart);
        $dateTime->setTimezone(new \DateTimeZone('UTC')); // Asegura que la zona horaria es UTC
        return $dateTime->format('Y-m-d H:i:s');
    }
}
