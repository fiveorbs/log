<?php

declare(strict_types=1);

namespace Conia\Error\Tests;

use Conia\Error\Logger;
use DateTime;
use Exception;
use PHPUnit\Framework\Attributes\TestDox;
use Psr\Log\InvalidArgumentException;
use stdClass;

class LoggerTest extends TestCase
{
    #[TestDox('Write to file')]
    public function testLoggerToFile(): void
    {
        $logger = new Logger(logfile: $this->logFile);

        $logger->debug('Scott');
        $logger->info('Steve');
        $logger->notice('James');
        $logger->warning('Chuck');
        $logger->error('Bobby');
        $logger->critical('Chris');
        $logger->alert('Kelly');
        $logger->emergency('Terry');

        $output = file_get_contents($this->logFile);

        $this->assertStringContainsString('] DEBUG: Scott', $output);
        $this->assertStringContainsString('] INFO: Steve', $output);
        $this->assertStringContainsString('] NOTICE: James', $output);
        $this->assertStringContainsString('] WARNING: Chuck', $output);
        $this->assertStringContainsString('] ERROR: Bobby', $output);
        $this->assertStringContainsString('] CRITICAL: Chris', $output);
        $this->assertStringContainsString('] ALERT: Kelly', $output);
        $this->assertStringContainsString('] EMERGENCY: Terry', $output);
    }

    #[TestDox('Write to PHP SAPI')]
    public function testLoggerToPhpSapi(): void
    {
        $logger = new Logger(logfile: $this->logFile);

        $logger->debug('Scott');
        $logger->info('Steve');
        $logger->warning('Chuck');
        $logger->error('Bobby');
        $logger->alert('Kelly');

        $output = file_get_contents($this->logFile);

        $this->assertStringContainsString('] DEBUG: Scott', $output);
        $this->assertStringContainsString('] INFO: Steve', $output);
        $this->assertStringContainsString('] WARNING: Chuck', $output);
        $this->assertStringContainsString('] ERROR: Bobby', $output);
        $this->assertStringContainsString('] ALERT: Kelly', $output);
    }

    #[TestDox('Respect higher debug level')]
    public function testLoggerWithHigherDebugLevel(): void
    {
        $logger = new Logger(Logger::ERROR, $this->logFile);

        $logger->debug('Scott');
        $logger->info('Steve');
        $logger->notice('James');
        $logger->warning('Chuck');
        $logger->error('Bobby');
        $logger->critical('Chris');
        $logger->alert('Kelly');
        $logger->emergency('Terry');

        $output = file_get_contents($this->logFile);

        $this->assertStringNotContainsString('] DEBUG: Scott', $output);
        $this->assertStringNotContainsString('] INFO: Steve', $output);
        $this->assertStringNotContainsString('] NOTICE: James', $output);
        $this->assertStringNotContainsString('] WARNING: Chuck', $output);
        $this->assertStringContainsString('] ERROR: Bobby', $output);
        $this->assertStringContainsString('] CRITICAL: Chris', $output);
        $this->assertStringContainsString('] ALERT: Kelly', $output);
        $this->assertStringContainsString('] EMERGENCY: Terry', $output);
    }

    #[TestDox('Fail with PSR-3 error on unknown log level ')]
    public function testLoggerWithWrongLogLevel(): void
    {
        $this->throws(InvalidArgumentException::class, 'Unknown log level');

        $logger = new Logger(Logger::ERROR, $this->logFile);
        $logger->log(1313, 'never logged');
    }

    #[TestDox('Iterpolate context values into message template')]
    public function testMessageInterpolation(): void
    {
        $logger = new Logger(logfile: $this->logFile);

        $logger->warning(
            'String: {string}, Integer: {integer}, ' .
                'DateTime: {datetime}, Array: {array}' .
                'Float: {float}, Object: {object} ' .
                'Other: {other}, Null: {null}',
            [
                'string' => 'Scream Bloody Gore',
                'integer' => 13,
                'float' => 73.23,
                'datetime' => new DateTime('1987-05-25T13:31:23'),
                'array' => [13, 23, 71],
                'object' => new stdClass(),
                'other' => stream_context_create(),
                'null' => null,
                'exception' => new Exception('The test exception'),
            ]
        );

        $output = file_get_contents($this->logFile);

        $this->assertStringContainsString('String: Scream Bloody Gore', $output);
        $this->assertStringContainsString('Integer: 13', $output);
        $this->assertStringContainsString('Float: 73.23', $output);
        $this->assertStringContainsString('DateTime: 1987-05-25 13:31:23', $output);
        $this->assertStringContainsString('Array: [Array [13,23,71]]', $output);
        $this->assertStringContainsString('Object: [Instance of stdClass]', $output);
        $this->assertStringContainsString('Other: [resource (stream-context)]', $output);
        $this->assertStringContainsString('Null: [null]', $output);
        $this->assertStringContainsString('Exception Message: The test exception', $output);
    }
}
