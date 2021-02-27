<?php

namespace Atlantis\Controllers\Api\V1;

use Atlantis\Api;
use Atlantis\App;
use Atlantis\Controller;
use Atlantis\Request;
use Atlantis\Models\User;

/**
 * API version 1.0
 */
class UserController extends Controller
{
    function index()
    {
        $request = new Request();

        $api = new Api();
        $api->auth();

        $user = $api->decoded->data->user;

        $id = $request->id ?? $user->id;

        if ($user->role != 1 && $id != $user->id) {
            $api->accessDenied();
        }

        $user = new User();
        $user->init($id);

        $api->response(200, 1, App::$lang->get('success'), ["user" => $user]);
    }
}
