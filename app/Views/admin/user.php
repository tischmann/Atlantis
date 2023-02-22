<?php

use App\Models\User;

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <form method="post" class="px-4 my-4" autocomplete="off">
        {{csrf}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="flex flex-col">
                <div class="relative mb-4" data-te-input-wrapper-init>
                    <input type="text" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="userLogin" placeholder="{{lang=user_login}}" name="login" value="<?= $user->login ?>" autocomplete="off" required />
                    <label for="userLogin" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=user_login}}</label>
                </div>
                <div class="relative mb-4" data-te-input-wrapper-init>
                    <input type="text" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="userPassword" placeholder="{{lang=user_password}}" name="password" value="" autocomplete="off" />
                    <label for="userPassword" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=user_password}}</label>
                </div>
                <div class="mb-4">
                    <select data-te-select-init name="role" id="userRole" aria-label="{{lang=user_role}}">
                        <option value="" <?= !$user->role ? 'selected' : '' ?>></option>
                        <option value="guest" <?= $user->role === User::ROLE_GUEST ? 'selected' : '' ?>>{{lang=user_role_guest}}</option>
                        <option value="user" <?= $user->role === User::ROLE_USER ? 'selected' : '' ?>>{{lang=user_role_user}}</option>
                        <option value="admin" <?= $user->role === User::ROLE_ADMIN ? 'selected' : '' ?>>{{lang=user_role_admin}}</option>
                    </select>
                    <label for="userRole" data-te-select-label-ref>{{lang=user_role}}</label>
                </div>
                <div class="mb-4">
                    <select data-te-select-init name="status" id="userStatus" aria-label="{{lang=user_status}}">
                        <option value="1" <?= $user->status ? 'selected' : '' ?>>{{lang=user_status_1}}</option>
                        <option value="0" <?= !$user->status ? 'selected' : '' ?>>{{lang=user_status_0}}</option>
                    </select>
                    <label for="userStatus" data-te-select-label-ref>{{lang=user_status}}</label>
                </div>
                <div class="relative flex flex-grow" data-te-input-wrapper-init>
                    <textarea class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0 flex-grow" placeholder="{{lang=user_remarks}}" id="userRemarksInput" rows="2" name="remarks"><?= $user->remarks ?></textarea>
                    <label for="userRemarksInput" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=user_remarks}}</label>
                </div>
            </div>
            <div>
                <input type="hidden" value="<?= $user->avatar ?>" name="avatar" id="userAvatarInput">
                <input type='file' id="userAvatarFile" class="hidden" aria-label="{{lang=article_image}}">
                <img src="/images/avatars/<?= $user->avatar ?>" id="userAvatar" width="400" height="400" alt="{{lang=user_avatar}}" class="rounded w-full object-cover border border-gray-300 cursor-pointer">
                <button type="button" data-te-ripple-init data-te-ripple-color="light" id="imageDeleteButton" class="mt-4 w-full hidden flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">
                    {{lang=delete_image}}
                </button>
                <script nonce="{{nonce}}">
                    document.getElementById(`userAvatar`).addEventListener('error', function(e) {
                        e.target.src = '/images/placeholder.svg';
                    }, true)
                </script>
            </div>
        </div>
        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($user->id) {
                echo <<<HTML
                <button type="button" id="deleteUserButton" aria-label="{{lang=delete}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=delete}}</button>
                <script src="/js/dialog.js" nonce="{{nonce}}"></script>
                <script nonce="{{nonce}}">
                    const dialog = new Dialog({
                        title: `{{lang=warning}}!`,
                        message: `{{lang=user_delete_confirm}}?`,
                        buttons: [
                            {
                                text: `{{lang=no}}`,
                                class: `bg-sky-600 text-white hover:bg-sky-500 focus:bg-sky-500 active:bg-sky-500`,
                                callback: () => {}
                            },
                            {
                                text: `{{lang=yes}}`,
                                class: `bg-pink-600 text-white hover:bg-pink-500 focus:bg-pink-500 active:bg-pink-500`,
                                callback: () => {
                                    fetch(`/user/delete/{$user->id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-Requested-With': `XMLHttpRequest`,
                                            'X-Csrf-Token': `{{csrf-token}}`,
                                            'Accept': 'application/json',                    
                                        },
                                    }).then(response => response.json().then(data => {
                                        if (data?.status) {
                                            window.location.href = `/{{env=APP_LOCALE}}/admin/users`
                                        } else {
                                            alert(data.message)
                                            console.error(data.message)
                                        }
                                    }).catch(error => {
                                        alert(error)
                                        console.error(error)
                                    })).catch(error => {
                                        alert(error)
                                        console.error(error)
                                    })
                                }
                            },
                        ]
                    })

                    document.getElementById('deleteUserButton')
                        .addEventListener('click', () => dialog.show())
                </script>
                HTML;
            }
            ?>
            <a href="/{{env=APP_LOCALE}}/admin/users" aria-label="{{lang=cancel}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-700 hover:shadow-lg focus:bg-gray-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-800 active:shadow-lg transition duration-150 ease-in-out text-center">{{lang=cancel}}</a>
            <button type="submit" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-sky-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-700 active:shadow-lg transition duration-150 ease-in-out">{{lang=save}}</button>
        </div>
    </form>
    <script nonce="{{nonce}}">
        let csrf = `{{csrf-token}}`

        const img = document.getElementById('userAvatar')

        const file = document.getElementById('userAvatarFile')

        const input = document.getElementById('userAvatarInput')

        const imageDeleteButton = document.getElementById('imageDeleteButton')

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
                .then(response => response.json()
                    .then(json => {
                        if (!json?.status) {
                            console.error(json)
                            return alert(json?.message || 'Error')
                        }

                        input.value = json.image

                        img.src = json.location

                        csrf = json.csrf

                        imageDeleteButton.classList.remove('hidden')

                        imageDeleteButton.classList.add('inline-block')
                    })
                    .catch(error => {
                        alert(error)
                        console.error('Error:', error)
                    })
                ).catch(error => {
                    alert(error)
                    console.error('Error:', error)
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
            imageDeleteButton.classList.add('hidden')
            imageDeleteButton.classList.remove('inline-block')
        })
    </script>
</main>