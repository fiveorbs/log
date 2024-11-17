<?php

declare(strict_types=1);

namespace FiveOrbs\Log\Formatter;

use FiveOrbs\Log\Formatter;

/** @psalm-api */
class TemplateFormatter implements Formatter
{
	use PreparesValue;

	public function __construct(protected readonly bool $includeTraceback = true) {}

	public function format(string $message, ?array $context): string
	{
		if ($context) {
			return $this->interpolate($message, $context);
		}

		return $message;
	}

	protected function interpolate(string $template, array $context): string
	{
		$substitutes = [];

		/**
		 * @psalm-suppress MixedAssignment
		 *
		 * $value types are exhaustively checked
		 */
		foreach ($context as $key => $value) {
			$placeholder = '{' . $key . '}';

			if (strpos($template, $placeholder) === false) {
				continue;
			}

			$substitutes[$placeholder] = $this->prepare($value, $this->includeTraceback);
		}

		$message = strtr($template, $substitutes);

		return $message;
	}
}
