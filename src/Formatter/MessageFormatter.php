<?php

declare(strict_types=1);

namespace Conia\Log\Formatter;

use Conia\Log\Formatter;

class MessageFormatter implements Formatter
{
    public function format(string $message, ?array $context): string
    {
        return $message;
    }
}
