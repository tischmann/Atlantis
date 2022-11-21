<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\{User};

use Tischmann\Atlantis\Exceptions\NotFoundException;

/**
 * Авторизация пользователя
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Auth
{
    protected static User $user;

    private function __construct()
    {
    }

    public static function user(): User
    {
        static::$user ??= static::authorize();

        return static::$user;
    }

    public static function authorize(): User
    {
        try {
            static::$user = User::find(Session::get('user_id') ?? 0);
        } catch (NotFoundException $e) {
            static::$user = new User();
        }

        return static::$user;
    }
}
