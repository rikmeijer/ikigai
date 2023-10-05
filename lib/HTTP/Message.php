<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\HTTP;

final readonly class Message {
    
    public function __construct(
            public array $headers,
            public ?string $body = null
    ) {}
    
    public static function fromRequest() : self {
        $headers = array_filter($_SERVER, fn(string $key) => str_starts_with($key, 'HTTP_'), ARRAY_FILTER_USE_KEY);
        $headers = array_map(fn(string $name, string $value) => ucwords(str_replace('_', '-', strtolower(substr($name, 5))), '-') . ': ' . $value, array_keys($headers), $headers);
        return new self(array_values($headers));
    }
    
}
