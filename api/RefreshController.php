<?php

namespace Atlantis\Controllers\Api\V1;

use Atlantis\Api;
use Atlantis\Controller;

/**
 * API version 1.0
 */
class RefreshController extends Controller
{
    function index()
    {
        $api = new Api();
        $api->auth()->refresh();
    }
}
