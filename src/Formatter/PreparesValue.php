<?php

declare(strict_types=1);

namespace Conia\Error\Formatter;

use DateTimeInterface;
use Stringable;
use Throwable;

trait PreparesValue
{
    public function prepare(mixed $value): string
    {
        return match (true) {
            // Exceptions must be first as they are Stringable
            is_subclass_of($value, Throwable::class) => $this->getExceptionMessage($value),
            (is_scalar($value) || (is_object($value) && ($value instanceof Stringable))) => (string)$value,
            $value instanceof DateTimeInterface => $value->format('Y-m-d H:i:s T'),
            is_object($value) => '[Instance of ' . $value::class . ']',
            is_array($value) => '[Array ' . json_encode($value, JSON_UNESCAPED_SLASHES) . ']',
            is_null($value) => '[null]',
            default => '[' . get_debug_type($value) . ']',
        };
    }

    protected function getExceptionMessage(Throwable $exception): string
    {
        $message = $exception::class . ': ' . $exception->getMessage();

        if ($this->includeTraceback) {
            $message .= "\n" . $exception->getTraceAsString() . "\n";
        }

        return $message;
    }
}
