<?php

namespace Integreat\Gemeindeverzeichnis;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        $handle = STDOUT;

        if (!empty($context)) {
            $message .= ' ' . json_encode($context);
        }

        $errors = [LogLevel::WARNING,LogLevel::ERROR, LogLevel::CRITICAL, LogLevel::ALERT, LogLevel::EMERGENCY];
        if (in_array($level, $errors)) {
            $handle = STDERR;
        }

        fwrite($handle, "[".strtoupper($level)."] " . $message . "\n");
    }
}
