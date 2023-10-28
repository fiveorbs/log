Conia Error
===========

A Error handling PSR-15 middleware.

## Testing

During testing PHP's `error_log` ini setting is set to a temporary file. To print the output
to the console prepend the PHPUnit cli command with a specific env variable like the following:

    ECHO_LOG=1 phpunit
