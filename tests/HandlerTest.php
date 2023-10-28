<?php

declare(strict_types=1);

namespace Conia\Error\Tests;

use Conia\Error\Handler;
use ErrorException;
use PHPUnit\Framework\Attributes\TestDox;

class HandlerTest extends TestCase
{
    #[TestDox("Don't handle error level 0")]
    public function testErrorHandlerI(): void
    {
        $err = new Handler($this->factory, $this->factory);

        $this->assertEquals(false, $err->handleError(0, 'Handler Test'));
    }

    public function testErrorHandlerII(): void
    {
        $this->throws(ErrorException::class, 'Handler Test');

        $err = new Handler($this->factory, $this->factory);
        $err->handleError(E_WARNING, 'Handler Test');
    }

    #[TestDox("Don't handle error level 0")]
    public function testHTTPErrorResponses(): void
    {
        $err = new Handler($this->factory, $this->factory);

        // $response = $err->getResponse(new HttpBadRequest(), $this->request());
        // $this->assertStringContainsString('<h1>400 Bad Request</h1>', (string)$response->psr()->getBody());
        //
        // $response = $err->getResponse(new HttpUnauthorized(), $this->request());
        // $this->assertStringContainsString('<h1>401 Unauthorized</h1>', (string)$response->psr()->getBody());
        //
        // $response = $err->getResponse(new HttpForbidden(), $this->request());
        // $this->assertStringContainsString('<h1>403 Forbidden</h1>', (string)$response->psr()->getBody());
        //
        // $response = $err->getResponse(new HttpNotFound(), $this->request());
        // $this->assertStringContainsString('<h1>404 Not Found</h1>', (string)$response->psr()->getBody());
        //
        // $response = $err->getResponse(new HttpMethodNotAllowed(), $this->request());
        // $this->assertStringContainsString('<h1>405 Method Not Allowed</h1>', (string)$response->psr()->getBody());
        //
        // $response = $err->getResponse(new Exception(), $this->request());
        // $this->assertStringContainsString('<h1>500 Internal Server Error</h1>', (string)$response->psr()->getBody());
    }

    public function testResponseWithTextPlain(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/plain';
        $err = new Handler($this->factory, $this->factory);

        // $response = $err->getResponse(new HttpBadRequest(), $this->request());
        // $this->assertEquals('Error: 400 Bad Request', (string)$response->psr()->getBody());
    }

    public function testResponseWithApplicationJson(): void
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $err = new Handler($this->factory, $this->factory);

        // $response = $err->getResponse(new HttpBadRequest(), $this->request());
        // $error = json_decode((string)$response->psr()->getBody());
        //
        // $this->assertEquals('400 Bad Request', $error->error);
        // $this->assertEquals('Bad Request', $error->description);
        // $this->assertStringContainsString('#0', $error->traceback);
        // $this->assertEquals(400, $error->code);
        // $this->assertEquals(null, $error->payload);
    }

    public function testResponseWithPHPExceptions(): void
    {
        $err = new Handler($this->factory, $this->factory);
        // $response = $err->getResponse(new DivisionByZeroError('Division by zero'), $this->request());
        //
        // $this->assertStringContainsString('<h1>500 Internal Server Error</h1>', (string)$response->psr()->getBody());
    }

    // public function testHandledByMiddleware(): void
    // {
    //     $app = App::create();
    //     $app->route('/', fn () => '');
    //     ob_start();
    //     $response = $app->run();
    //     ob_end_clean();
    //
    //     $this->assertStringContainsString('<title>500 Internal Server Error</title>', (string)$response->getBody());
    //     $this->assertStringContainsString('<h1>500 Internal Server Error</h1>', (string)$response->getBody());
    // }

    public function testEmitPHPExceptions(): void
    {
        $err = new Handler($this->factory, $this->factory);

        ob_start();
        $err->emitException(new DivisionByZeroError('Division by zero'));
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString('<title>500 Internal Server Error</title>', $output);
        $this->assertStringContainsString('<h1>500 Internal Server Error</h1>', $output);
    }
}
