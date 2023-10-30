<?php

declare(strict_types=1);

namespace Conia\Error;

interface Formatter
{
    public function format(string $message, ?array $context): string;
}
