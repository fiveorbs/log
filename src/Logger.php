<?php

declare(strict_types=1);

namespace Conia\Log;

use Conia\Log\Formatter\MessageFormatter;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface as PsrLogger;
use Stringable;

/** @psalm-api */
class Logger implements PsrLogger
{
    public const DEBUG = 100;
    public const INFO = 200;
    public const NOTICE = 300;
    public const WARNING = 400;
    public const ERROR = 500;
    public const CRITICAL = 600;
    public const ALERT = 700;
    public const EMERGENCY = 800;

    /** @psalm-var array<int, non-empty-string> */
    protected array $levelLabels;

    public function __construct(
        protected ?string $logfile = null,
        protected ?Formatter $formatter = null,
        protected int $minimumLevel = self::DEBUG,
    ) {
        if (!$formatter) {
            $this->formatter = new MessageFormatter();
        }

        $this->levelLabels = [
            self::DEBUG => 'DEBUG',
            self::INFO => 'INFO',
            self::NOTICE => 'NOTICE',
            self::WARNING => 'WARNING',
            self::ERROR => 'ERROR',
            self::CRITICAL => 'CRITICAL',
            self::ALERT => 'ALERT',
            self::EMERGENCY => 'EMERGENCY',
        ];
    }

    public function formatter(Formatter $formatter): void
    {
        $this->formatter = $formatter;
    }

    public function withFormatter(Formatter $formatter): self
    {
        $new = clone $this;
        $new->formatter($formatter);

        return $new;
    }

    public function log(
        mixed $level,
        string|Stringable $message,
        array $context = [],
    ): void {
        $message = (string)$message;
        assert(is_int($level) || is_numeric($level));
        $level = (int)$level;

        if ($level < $this->minimumLevel) {
            return;
        }

        if (isset($this->levelLabels[$level])) {
            $levelLabel = $this->levelLabels[$level];
        } else {
            throw new InvalidArgumentException('Unknown log level: ' . (string)$level);
        }

        assert(!is_null($this->formatter));
        $message = $this->formatter->format(str_replace("\0", '', $message), $context);
        $time = date('Y-m-d H:i:s D T');
        $line = "[{$time}] {$levelLabel}: {$message}";

        if (is_string($this->logfile)) {
            error_log($line, 3, $this->logfile);
        } else {
            error_log($line);
        }
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log(self::DEBUG, $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log(self::INFO, $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log(self::NOTICE, $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log(self::WARNING, $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log(self::ERROR, $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log(self::ALERT, $message, $context);
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log(self::EMERGENCY, $message, $context);
    }
}
