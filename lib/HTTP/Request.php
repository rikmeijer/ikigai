<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\HTTP;
use \rikmeijer\purposeplan\lib\HTTP\Message;

final readonly class Request {
    
    public function __construct(
            public string $method,
            public string $path,
            public string $protocol,
            public Message $message
    ) {}
    
    public static function fromCurrent() : self {
        $headers = array_filter($_SERVER, fn(string $key) => str_starts_with($key, 'HTTP_'), ARRAY_FILTER_USE_KEY);
        $headers = array_map(fn(string $name, string $value) => ucwords(str_replace('_', '-', strtolower(substr($name, 5))), '-') . ': ' . $value, array_keys($headers), $headers);
        
        return new self($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['SERVER_PROTOCOL'], new Message($headers));
    }
    
}
