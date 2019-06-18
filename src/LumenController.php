<?php

namespace Huangdijia\JsonRpc;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Huangdijia\JsonRpc\Traits\JsonRpc;

abstract class LumenController extends BaseController
{
    use JsonRpc;
}
