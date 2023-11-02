<?php
use Amp\ByteStream;
use Amp\Http\HttpStatus;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

require __DIR__.'/bootstrap.php';

// Note any PSR-3 logger may be used, Monolog is only an example.
$logHandler = new StreamHandler(ByteStream\getStdout());
$logHandler->pushProcessor(new PsrLogMessageProcessor());
$logHandler->setFormatter(new ConsoleFormatter());

$logger = new Logger('server');
$logger->pushHandler($logHandler);

$requestHandler = new class() implements RequestHandler {
    public function handleRequest(Request $request) : Response
    {
        $response = rikmeijer\purposeplan\lib\UI\Web::entry([
            'REQUEST_METHOD' => strtoupper($request->getMethod()),
            'REQUEST_URI' => $request->getUri()->getPath(),
            'SERVER_PROTOCOL' => $request->getProtocolVersion(),
            'HTTP_ACCEPT' => $request->getHeader('Accept')
        ]);
        return $response(fn(string $status, callable $body) => new Response(
            status: substr($status, 0, 3),
            body: $body(function(string $contentType) use (&$type) { $type = $contentType; }),
            headers: ['Content-Type' => $type]
        ));
    }
};

$errorHandler = new DefaultErrorHandler();

$server = SocketHttpServer::createForDirectAccess($logger);
$server->expose(getenv('SERVE_HOST') ? getenv('SERVE_HOST') : $_ENV['SERVE_HOST']);
$server->start($requestHandler, $errorHandler);

// Serve requests until SIGINT or SIGTERM is received by the process.
Amp\trapSignal([SIGINT, SIGTERM]);

$server->stop();