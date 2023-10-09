<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Web {
    
    static function entry(array $server) : callable {
        
        $accepting_types = array_reduce(
            explode(',', $server['HTTP_ACCEPT']), 
            function ($res, $el) { 
              list($l, $q) = array_merge(explode(';q=', $el), [1]); 
              $res[$l] = (float) $q; 
              return $res; 
            }, []);
          arsort($accepting_types);
          
        return function(callable $headers, callable $body) use ($accepting_types) {
            $headers('HTTP/2 200 OK');
            $headers('Content-Type: ' . key($accepting_types));
            $body('Hello World');
        };
    }
    
}
