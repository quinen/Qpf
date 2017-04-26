<?php

namespace Qpf\Http;

use Qpf\Core\Object;

class Response extends Object {
    public function __construct(){
        $this->log("response");
    }
}