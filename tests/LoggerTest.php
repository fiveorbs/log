<?php

declare(strict_types=1);

namespace FiveOrbs\Log\Tests;

use FiveOrbs\Log\Formatter\TemplateFormatter;
use FiveOrbs\Log\Logger;
use PHPUnit\Framework\Attributes\TestDox;
use Psr\Log\InvalidArgumentException;

class LoggerTest extends TestCase
{
	#[TestDox('Write to file')]
	public function testLoggerToFile(): void
	{
		$logger = new Logger($this->logFile);

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

	#[TestDox('Write to PHP default destination')]
	public function testLoggerToPhpDefaultDestination(): void
	{
		$logger = new Logger();

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
		$logger = new Logger($this->logFile, minimumLevel: Logger::ERROR);

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

		$logger = new Logger($this->logFile, minimumLevel: Logger::ERROR);
		$logger->log(1313, 'never logged');
	}

	#[TestDox('Format message with TemplateFormatter')]
	public function testFormatMessage(): void
	{
		$logger = new Logger(logfile: $this->logFile, formatter: new TemplateFormatter());

		$logger->emergency('Template {string}', ['string' => 'Formatted']);

		$output = file_get_contents($this->logFile);

		$this->assertStringContainsString('] EMERGENCY: Template Formatted', $output);
	}

	#[TestDox('Format message with different formatters')]
	public function testFormatMessageAfterSettingFormatter(): void
	{
		$logger = new Logger(logfile: $this->logFile);

		$logger->alert('Template {string}', ['string' => 'Formatted']);

		$output = file_get_contents($this->logFile);

		$this->assertStringContainsString('] ALERT: Template {string}', $output);
		$this->assertStringNotContainsString('] ALERT: Template Formatted', $output);

		$logger->formatter(new TemplateFormatter());
		$logger->alert('Template {string}', ['string' => 'Formatted']);

		$output = file_get_contents($this->logFile);

		$this->assertStringContainsString('] ALERT: Template Formatted', $output);
	}

	#[TestDox('Format message with cloned loggers')]
	public function testFormatMessageAfterCloningLogger(): void
	{
		$logger = new Logger(logfile: $this->logFile);

		$logger->alert('Template {string}', ['string' => 'Formatted']);

		$output = file_get_contents($this->logFile);

		$this->assertStringContainsString('] ALERT: Template {string}', $output);
		$this->assertStringNotContainsString('] ALERT: Template Formatted', $output);

		$newLogger = $logger->withFormatter(new TemplateFormatter());
		$newLogger->alert('New Logger {string}', ['string' => 'Formatted']);
		$logger->alert('Old Logger {string}', ['string' => 'Formatted']);

		$output = file_get_contents($this->logFile);

		$this->assertStringContainsString('] ALERT: New Logger Formatted', $output);
		$this->assertStringContainsString('] ALERT: Old Logger {string}', $output);
	}
}
