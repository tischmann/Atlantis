<?php

use App\Models\{User};

use Tischmann\Atlantis\{Template};

assert($user instanceof User);
?>

<main class="md:container mx-4 md:mx-auto">
    <form autocomplete="new-password" id="user-form" data-id="<?= $user?->id ?>">
        {{csrf}}
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'fields/input_field',
                args: [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => get_str('user_name'),
                    'value' => $user->name,
                    'required' => true,
                    'autocomplete' => 'new-password'
                ]
            );
            ?>
        </div>
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'fields/input_field',
                args: [
                    'type' => 'text',
                    'name' => 'login',
                    'label' => get_str('user_login'),
                    'value' => $user->login,
                    'required' => true,
                    'autocomplete' => 'new-password'
                ]
            );
            ?>
        </div>
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'fields/input_field',
                args: [
                    'type' => 'password',
                    'name' => 'password',
                    'label' => get_str('user_password'),
                    'value' => "",
                    'required' => $user->exists() ? false : true,
                    'autocomplete' => 'new-password'
                ]
            );
            ?>
        </div>
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'fields/input_field',
                args: [
                    'type' => 'password',
                    'name' => 'password_repeat',
                    'label' => get_str('user_password_repeat'),
                    'value' => "",
                    'required' => $user->exists() ? false : true,
                    'autocomplete' => 'new-password'
                ]
            );
            ?>
        </div>
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'fields/textarea_field',
                args: [
                    'name' => 'remarks',
                    'label' => get_str('user_remarks'),
                    'value' => $user->remarks,
                ]
            );
            ?>
        </div>
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'fields/radio_field',
                args: [
                    'name' => 'role',
                    'label' => get_str('user_role'),
                    'value' => $user->role,
                    'options' => [
                        ['value' => 0, 'label' => get_str('user_role_guest')],
                        ['value' => 1, 'label' => get_str('user_role_user')],
                        ['value' => 2, 'label' => get_str('user_role_author')],
                        ['value' => 3, 'label' => get_str('user_role_moderator')],
                        ['value' => 255, 'label' => get_str('user_role_admin')]
                    ]
                ]
            );
            ?>
        </div>
        <div class="mb-8">
            <?php
            Template::echo(
                template: 'fields/radio_field',
                args: [
                    'name' => 'status',
                    'label' => get_str('user_status'),
                    'value' => intval($user->status),
                    'options' => [
                        ['value' => 0, 'label' => get_str('user_status_inactive')],
                        ['value' => 1, 'label' => get_str('user_status_active')]
                    ]
                ]
            );
            ?>
        </div>
        <div class="mb-8 flex flex-col sm:flex-row sm:flex-nowrap gap-4">
            <?php
            if ($user->exists()) {
                echo <<<HTML
                <button class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=delete}}" data-delete>{{lang=delete}}</button>
                <button class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=save}}" data-save>{{lang=save}}</button>
                HTML;
            } else {
                echo <<<HTML
                <button class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=add}}" data-add>{{lang=add}}</button>
                HTML;
            }
            ?>
        </div>
    </form>
</main>
<script nonce="{{nonce}}">
    (function() {
        const form = document.getElementById('user-form')

        document.querySelector('button[data-save]')?.addEventListener('click', () => {
            fetch(`/user/${form.dataset.id}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            }).then((response) => {
                response.json().then((json) => {
                    alert(`${json.message}`)
                    window.location.reload()
                })
            })
        })

        document.querySelector('button[data-add]')?.addEventListener('click', () => {
            fetch(`/user`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            }).then((response) => {
                response.json().then((json) => {
                    alert(`${json.message}`)
                    window.location.href = `/user/${json.id}`
                })
            })
        })

        document.querySelector('button[data-delete]')?.addEventListener('click', () => {
            if (!confirm(`{{lang=confirm_delete}}`)) return

            fetch(`/user/${form.dataset.id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body
            }).then((response) => {
                response.json().then((json) => {
                    alert(`${json.message}`)
                    window.location.href = '/users'
                })
            })
        })
    })()
</script>