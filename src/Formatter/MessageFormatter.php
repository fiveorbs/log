<?php

declare(strict_types=1);

namespace Conia\Error\Formatter;

use Conia\Error\Formatter;

class MessageFormatter implements Formatter
{
    public function format(string $message, ?array $context): string
    {
        return $message;
    }
}
