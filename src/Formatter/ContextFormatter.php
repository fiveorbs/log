<?php

declare(strict_types=1);

namespace FiveOrbs\Log\Formatter;

use FiveOrbs\Log\Formatter;

/** @psalm-api */
class ContextFormatter implements Formatter
{
	use PreparesValue;

	public function __construct(protected readonly bool $includeTraceback = true) {}

	public function format(string $message, ?array $context): string
	{
		if ($context) {
			return $message . ":\n" . $this->transform($context);
		}

		return $message;
	}

	protected function transform(array $context): string
	{
		$result = '';

		/**
		 * @psalm-suppress MixedAssignment
		 *
		 * $value types are exhaustively checked
		 */
		foreach ($context as $key => $value) {
			$result .= "  [{$key}] => " . $this->prepare($value, $this->includeTraceback, '      ') . "\n";
		}

		return $result;
	}
}
