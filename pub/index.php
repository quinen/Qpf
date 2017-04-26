<?php

namespace Qpf\Http;

require "../inc/boot.php";

(new Dispatcher(new Request(),new Response()))->dispatch();
