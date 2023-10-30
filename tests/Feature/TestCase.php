<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Feature;


abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private $result;
    private $curl;
    
    public function open(string $path) {
        $this->curl = curl_init('http://' . $_ENV['SERVE_HOST'] . $path);
        curl_setopt_array($this->curl, [
            CURLOPT_HTTPHEADER => [
                'Accept: text/html'
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);
        $this->result = curl_exec($this->curl);
        $this->assertNotFalse($this->result, curl_error($this->curl));
        $this->assertStringNotContainsString('Warning', $this->result);
        $this->assertStringNotContainsString('Error', $this->result);
    }
    
    public function assertResponseCode(string $expectedCode) {
        $this->assertEquals($expectedCode, curl_getinfo($this->curl,  CURLINFO_RESPONSE_CODE));
    }
    
    public function assertBodyEquals(string $expectedBody) {
        $this->assertEquals($expectedBody, $this->result);
    }
    public function assertBodyContains(string $needle) {
        $this->assertTrue(str_contains($this->result, $needle), "'$needle' not in '$this->result'");
    }
}
