Conia Error
===========

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/coniadev/error.svg)](https://scrutinizer-ci.com/g/coniadev/error/code-structure)
[![Psalm coverage](https://shepherd.dev/github/coniadev/error/coverage.svg?)](https://shepherd.dev/github/coniadev/error)
[![Psalm level](https://shepherd.dev/github/coniadev/error/level.svg?)](https://conia.dev/error)
[![Quality Score](https://img.shields.io/scrutinizer/g/coniadev/error.svg)](https://scrutinizer-ci.com/g/coniadev/error)

A Error handling PSR-15 middleware and a PSR-3 Logger.

## Testing

During testing PHP's `error_log` ini setting is set to a temporary file. To print the output
to the console prepend the PHPUnit cli command with a specific env variable like the following:

    ECHO_LOG=1 phpunit
