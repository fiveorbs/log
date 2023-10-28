<?php

declare(strict_types=1);

namespace Conia\Error\Tests;

use Conia\Error\Formatter\TemplateFormatter;
use DateTime;
use ErrorException;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use stdClass;

class FormatterTest extends TestCase
{
    #[TestDox('Iterpolate context values into message template')]
    public function testTemplateFormatter(): void
    {
        $template = 'String: {string}, Integer: {integer}, ' .
            'DateTime: {datetime}, Array: {array}' .
            'Float: {float}, Object: {object} ' .
            'Other: {other}, Null: {null}, {exception}';
        $context = [
            'string' => 'Scream Bloody Gore',
            'integer' => 13,
            'float' => 73.23,
            'datetime' => new DateTime('1987-05-25T13:31:23'),
            'array' => [13, 23, 71],
            'object' => new stdClass(),
            'other' => stream_context_create(),
            'null' => null,
            'exception' => new ErrorException('The test exception'),
        ];

        $formatter = new TemplateFormatter();
        $output = $formatter->format($template, $context);

        $this->assertStringContainsString('String: Scream Bloody Gore', $output);
        $this->assertStringContainsString('Integer: 13', $output);
        $this->assertStringContainsString('Float: 73.23', $output);
        $this->assertStringContainsString('DateTime: 1987-05-25 13:31:23', $output);
        $this->assertStringContainsString('Array: [Array [13,23,71]]', $output);
        $this->assertStringContainsString('Object: [Instance of stdClass]', $output);
        $this->assertStringContainsString('Other: [resource (stream-context)]', $output);
        $this->assertStringContainsString('Null: [null]', $output);
        $this->assertStringContainsString('ErrorException: The test exception', $output);
        // Check if traceback exists
        $this->assertStringContainsString('#0', $output);
        $this->assertStringContainsString('FormatterTest->testTemplateFormatter', $output);

        $formatter = new TemplateFormatter(includeTraceback: false);
        $output = $formatter->format($template, $context);

        $this->assertStringContainsString('String: Scream Bloody Gore', $output);
        $this->assertStringNotContainsString('#0', $output);
        $this->assertStringNotContainsString('FormatterTest->testTemplateFormatter', $output);
    }
}
