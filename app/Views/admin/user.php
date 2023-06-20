<?php

use App\Models\{User};

use Tischmann\Atlantis\{Locale, Template};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <form method="post" class="m-4" autocomplete="off">
        {{csrf}}
        <div class="grid grid-cols-1 md:grid-cols-3 md:gap-4">
            <div class="flex flex-col md:col-span-2">
                <?php

                Template::echo(
                    'admin/input-field',
                    [
                        'type' => 'text',
                        'label' => Locale::get('user_login'),
                        'name' => 'login',
                        'value' => $user->login,
                        'required' => true,
                        'autocomplete' => false,
                        'id' => 'userLogin',
                    ]
                );

                Template::echo(
                    'admin/input-field',
                    [
                        'type' => 'text',
                        'label' => Locale::get('user_password'),
                        'name' => 'password',
                        'value' => '',
                        'required' => false,
                        'autocomplete' => false,
                        'id' => 'userPassword',
                    ]
                );

                Template::echo(
                    'admin/select-field',
                    [
                        'label' => Locale::get('user_role'),
                        'name' => 'role',
                        'id' => 'userRole',
                        'options' => Template::html('admin/option', [
                            'value' => '',
                            'label' => '',
                            'title' => '',
                            'selected' => !$user->role
                        ]) . Template::html('admin/option', [
                            'value' => 'guest',
                            'label' => Locale::get('user_role_guest'),
                            'title' => Locale::get('user_role_guest'),
                            'selected' => $user->role === User::ROLE_GUEST
                        ]) . Template::html('admin/option', [
                            'value' => 'user',
                            'label' => Locale::get('user_role_user'),
                            'title' => Locale::get('user_role_user'),
                            'selected' => $user->role === User::ROLE_USER
                        ]) . Template::html('admin/option', [
                            'value' => 'admin',
                            'label' => Locale::get('user_role_admin'),
                            'title' => Locale::get('user_role_admin'),
                            'selected' => $user->role === User::ROLE_ADMIN
                        ])
                    ]
                );

                Template::echo(
                    'admin/select-field',
                    [
                        'label' => Locale::get('user_status'),
                        'name' => 'status',
                        'id' => 'userStatus',
                        'options' => Template::html('admin/option', [
                            'value' => '1',
                            'label' => Locale::get('user_status_1'),
                            'title' => Locale::get('user_status_1'),
                            'selected' => $user->status
                        ]) . Template::html('admin/option', [
                            'value' => '0',
                            'label' => Locale::get('user_status_0'),
                            'title' => Locale::get('user_status_0'),
                            'selected' => !$user->status
                        ])
                    ]
                );

                Template::echo(
                    'admin/textarea-field',
                    [
                        'label' => Locale::get('user_remarks'),
                        'name' => 'remarks',
                        'id' => 'userRemarks',
                        'flex' => true,
                        'rows' => 3,
                        'value' => $user->remarks
                    ]
                );

                ?>
            </div>
            <div>
                <?php
                Template::echo(
                    'admin/load-image',
                    [
                        'value' => $user->avatar,
                        'name' => 'avatar',
                        'label' => Locale::get('user_avatar'),
                        'src' => $user->avatar_src,
                        'width' => 400,
                        'height' => 400,
                        'url' => "/upload/user/avatar/{$user->id}"
                    ]
                );
                ?>
            </div>
        </div>
        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($user->id) {
                $locale = getenv('APP_LOCALE');

                Template::echo(
                    'admin/delete-button',
                    [
                        'id' => "delete-user-{$user->id}",
                        'title' => Locale::get('warning'),
                        'message' => Locale::get('user_delete_confirm') . "?",
                        'url' => "/{$locale}/user/delete/{$user->id}",
                        'redirect' => "/{$locale}/admin/users",
                    ]
                );
            }
            ?>
            <?= Template::html('admin/cancel-button', ['href' => '/{{env=APP_LOCALE}}/admin/users']) ?>
            <?= Template::html('admin/save-button') ?>
        </div>
    </form>
</main>