<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\HTTP;
use \rikmeijer\purposeplan\lib\HTTP\Message;

final readonly class Response {
    
    public function __construct(
            public string $status,
            public Message $message
    ) {}
    
    public static function send(self $response, callable $headers, callable $body) {
        $headers($response->status);
        Message::send($response->message, $headers, $body);
    }
    
}
