<?php

// ANZGO-3899 Added by Shane Camus 10/19/18
// ANZGO-3895 POC by John Renzo Sunico 10/19/18

class FileLogger
{
    static $identifier;

    public static function log($error)
    {
        $log = static::format($error);
        error_log($log);
    }

    private static function format($error)
    {
        $format = "%s %s";
        $logIdentifier = static::getIdentifier();
        $stringError = is_string($error) ? $error : json_encode($error);

        return sprintf($format, $logIdentifier, $stringError);
    }

    public static function getIdentifier()
    {
        if (static::$identifier) {
            return static::$identifier;
        }

        return static::$identifier = uniqid();
    }

    public static function getDateString()
    {
        return (new DateTime())->format('Y-m-d h:i:s z');
    }
}
