<?php

declare(strict_types=1);

namespace Conia\Error;

use Throwable;

class RendererEntry
{
    public function __construct(
        public readonly array $exceptions,
        public readonly Renderer $renderer
    ) {
    }

    public function matches(Throwable $exception): bool
    {
        foreach ($this->exceptions as $exceptionEntry) {
            if ($exception::class === $exceptionEntry) {
                return true;
            }

            if (is_subclass_of($exception::class, $exceptionEntry)) {
                return true;
            }
        }

        return false;
    }
}
