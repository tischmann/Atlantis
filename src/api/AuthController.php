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
class AuthController extends Controller
{
    function index()
    {
        $request = new Request();

        $request->validate(['login' => '', 'password' => '']);

        $api = new Api();

        if (!$request->login || !$request->password) {
            $api->response(401, 2, App::$lang->get('no_credentials'));
        }

        $user = new User();

        foreach ($user->fetchTableRowByLogin($request->login) as $row) {
            if (!$user::checkHash($request->password, $row->password)) {
                $api->response(401, 2, App::$lang->get('bad_password'));
            }

            $user->init($row);

            $data = (object) [
                'user' => (object) [
                    'id' => $user->id,
                    'role' => $user->role,
                    'name' => $user->name,
                    'phone' => $user->phoneCode . $user->phoneLocal,
                ],
            ];

            $api->issue($data);
        }

        $api->response(401, 2, App::$lang->get('bad_login'));
    }
}
