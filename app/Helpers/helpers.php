<?php

if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $timezone = 'Asia/Ho_Chi_Minh' , $format = 'Y-m-d H:i:s')
    {
        $tz = new DateTimeZone($timezone);
        $dt = new DateTime($date, new DateTimeZone('UTC'));

        $dt->setTimezone($tz);

        return $dt->format($format);
    }
}
