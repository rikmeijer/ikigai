<?php

class IndexTest extends \rikmeijer\purposeplan\Tests\Feature\TestCase {
    
    public function testIndexPageShowsHelloWorld() {
        $this->open('/');
        
        $this->assertResponseCode('200');
        $this->assertBodyEquals('<!DOCTYPE html>Hello World</html>');
    }
    
}
