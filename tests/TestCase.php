<?php

declare(strict_types=1);

namespace Conia\Error\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected mixed $defaultLog;
    protected mixed $tempFile;
    protected mixed $logFile;

    public function setUp(): void
    {
        // capture output of error_log calls in a temporary file
        // to prevent it printed to the console.
        $this->defaultLog = ini_get('error_log');
        $this->tempFile = tmpfile();
        $this->logFile = stream_get_meta_data($this->tempFile)['uri'];
        ini_set('error_log', $this->logFile);
    }

    public function tearDown(): void
    {
        // Restore default error_log and handlers
        is_file($this->logFile) && unlink($this->logFile);
        ini_set('error_log', $this->defaultLog);
        restore_error_handler();
        restore_exception_handler();
    }

    public function throws(string $exception, string $message = null): void
    {
        $this->expectException($exception);

        if ($message) {
            $this->expectExceptionMessage($message);
        }
    }
}
