Conia Log
=========

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/coniadev/log.svg)](https://scrutinizer-ci.com/g/coniadev/log/code-structure)
[![Psalm coverage](https://shepherd.dev/github/coniadev/log/coverage.svg?)](https://shepherd.dev/github/coniadev/log)
[![Psalm level](https://shepherd.dev/github/coniadev/log/level.svg?)](https://conia.dev/log)
[![Quality Score](https://img.shields.io/scrutinizer/g/coniadev/log.svg)](https://scrutinizer-ci.com/g/coniadev/log)

A simple PSR-3 logger using PHP's `error_log` function.

## Testing

During testing PHP's `error_log` ini setting is set to a temporary file. To print the output
to the console prepend the PHPUnit cli command with a specific env variable like the following:

    ECHO_LOG=1 phpunit
