<?php

use App\Models\{User};

use Tischmann\Atlantis\{Locale, Template};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <form method="post" class="px-4 my-4" autocomplete="off">
        {{csrf}}
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
            <div class="flex flex-col">
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
                <div class="mb-4">
                    <input type="hidden" value="<?= $user->avatar ?>" name="avatar" id="userAvatarInput">
                    <input type='file' id="userAvatarFile" class="hidden" aria-label="{{lang=article_image}}" accept=".jpg, .png, .jpeg, .gif, .bmp, .webp">
                    <img src="<?= $user->avatar_src ?>" id="userAvatar" width="400" height="400" alt="{{lang=user_avatar}}" class="rounded w-full object-cover border border-gray-300 cursor-pointer">
                    <button type="button" data-te-ripple-init data-te-ripple-color="light" id="imageDeleteButton" class="mt-4 w-full inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">
                        {{lang=delete_image}}
                    </button>
                </div>
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
    <script nonce="{{nonce}}">
        let csrf = `{{csrf-token}}`

        const img = document.getElementById('userAvatar')

        const file = document.getElementById('userAvatarFile')

        const input = document.getElementById('userAvatarInput')

        const imageDeleteButton = document.getElementById('imageDeleteButton')

        const errorHandler = (message) => {
            new Dialog({
                title: `{{lang=warning}}`,
                message: message,
                buttons: [{
                    text: `{{lang=yes}}`,
                    class: `bg-pink-600 text-white hover:bg-pink-500 focus:bg-pink-500 active:bg-pink-500`,
                }, ],
                onclose: () => window.location.reload()
            }).show()
        }

        const loadImage = (file, width, height) => {
            if (!file || !width || !height) return

            const formData = new FormData();

            formData.append('width', width);

            formData.append('height', height);

            formData.append('file', file, file.name);

            fetch(`/upload/user/avatar/<?= $user->id ?>`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Csrf-Token': csrf
                    },
                    body: formData
                })
                .then(response => {
                    if (response.status !== 200) {
                        response.text().then(text => {
                            return errorHandler(text)
                        })
                    }

                    response.json()
                        .then(json => {
                            if (!json?.status) {
                                return errorHandler(json?.message || 'Error')
                            }

                            input.value = json.image

                            img.src = json.location

                            csrf = json.csrf
                        })
                        .catch(error => {
                            errorHandler(error)
                        })
                }).catch(error => {
                    errorHandler(error)
                })

        }

        file.addEventListener('change', function(event) {
            loadImage(event.target.files[0],
                img.getAttribute('width'),
                img.getAttribute('height'))
        })

        img.addEventListener('click', function(event) {
            file.dispatchEvent(new MouseEvent('click'));
        })

        imageDeleteButton.addEventListener('click', () => {
            img.setAttribute('src', '/images/placeholder.svg')
            input.value = ''
        })
    </script>
</main>