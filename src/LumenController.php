<?php

namespace Huangdijia\JsonRpc;

use Huangdijia\JsonRpc\Traits\JsonRpc;
use Laravel\Lumen\Routing\Controller;

abstract class LumenController extends Controller
{
    use JsonRpc;
}
