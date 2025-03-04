<?php

namespace Noodleware\Chattera\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class MessageSanitizerService
{
    public static function sanitize(string $message): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', '/tmp');

        return (new HTMLPurifier($config))->purify($message);
    }
}
