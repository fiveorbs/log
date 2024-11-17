<?php

declare(strict_types=1);

namespace FiveOrbs\Log\Formatter;

use FiveOrbs\Log\Formatter;

class MessageFormatter implements Formatter
{
	public function format(string $message, ?array $context): string
	{
		return $message;
	}
}
