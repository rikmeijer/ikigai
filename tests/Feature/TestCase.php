<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Feature;


abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private $result;
    private $curl;
    
    public function open(string $path) {
        $this->curl = curl_init('http://localhost:8000' . $path);
        curl_setopt_array($this->curl, [
            CURLOPT_HTTPHEADER => [
                'Accept: text/html'
            ]
        ]);
        $this->result = curl_exec($this->curl);
    }
    
    public function assertResponseCode(string $expectedCode) {
        $this->assertEquals($expectedCode, curl_getinfo($this->curl, CURLINFO_HTTP_CODE));
    }
    
    public function assertBodyEquals(string $expectedBody) {
        $this->assertEquals($expectedBody, $this->result);
    }
}
