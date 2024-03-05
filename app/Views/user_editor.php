<?php

use App\Models\{User};

assert($user instanceof User);
?>

<main class="md:container mx-4 md:mx-auto">
    <form autocomplete="new-password" id="user-form" data-id="<?= $user?->id ?>" data-confirm="{{lang=confirm_delete}}">
        {{csrf}}
        <div class="mb-8 relative">
            <label for="name" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=user_name}}</label>
            <input type="text" class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" id="name" name="name" value="<?= $user?->name ?>" required autocomplete="new-password">
        </div>
        <div class="mb-8 relative">
            <label for="login" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=user_login}}</label>
            <input type="text" class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" id="login" name="login" value="<?= $user?->login ?>" required autocomplete="new-password">
        </div>
        <div class="mb-8 relative">
            <label for="password" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=user_password}}</label>
            <input type="password" class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" id="password" name="password" value="" <?= $user?->exists() ? '' : 'required' ?> autocomplete="new-password">
        </div>
        <div class="mb-8 relative">
            <label for="password_repeat" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=user_password_repeat}}</label>
            <input type="password" class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" id="password_repeat" name="password_repeat" value="" <?= $user?->exists() ? '' : 'required' ?> autocomplete="new-password">
        </div>
        <div class="mb-8 relative">
            <label for="remarks" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=user_remarks}}</label>
            <textarea id="remarks" name="remarks" class="block py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition"><?= $user?->remarks ?></textarea>
        </div>
        <div class="mb-8 relative">
            <label for="role_guest" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=user_role}}</label>
            <div class="grid w-full grid-cols-1 lg:grid-cols-5 gap-4 bg-white border-2 border-gray-200 rounded-lg p-4">
                <div>
                    <input type="radio" name="role" id="role_guest" value="0" class="peer hidden" <?= $user?->isGuest() ? 'checked' : '' ?> />
                    <label for="role_guest" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:text-white">{{lang=user_role_guest}}</label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_user" value="1" class="peer hidden" <?= $user?->isUser() ? 'checked' : '' ?> />
                    <label for="role_user" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:text-white">{{lang=user_role_user}}</label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_author" value="2" class="peer hidden" <?= $user?->isAuthor() ? 'checked' : '' ?> />
                    <label for="role_author" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:text-white">{{lang=user_role_author}}</label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_moderator" value="3" class="peer hidden" <?= $user?->isModerator() ? 'checked' : '' ?> />
                    <label for="role_moderator" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:text-white">{{lang=user_role_moderator}}</label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_admin" value="255" class="peer hidden" <?= $user?->isAdmin() ? 'checked' : '' ?> />
                    <label for="role_admin" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:text-white">{{lang=user_role_admin}}</label>
                </div>
            </div>
        </div>
        <div class="mb-8 relative">
            <label for="status_inactive" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=user_status}}</label>
            <div class="grid w-full grid-cols-1 sm:grid-cols-2 gap-4 bg-white border-2 border-gray-200 rounded-lg p-4">
                <div>
                    <input type="radio" name="status" id="status_inactive" value="0" class="peer hidden" <?= $user?->isInactive() ? 'checked' : '' ?> />
                    <label for="status_inactive" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:text-white">{{lang=user_status_inactive}}</label>
                </div>
                <div>
                    <input type="radio" name="status" id="status_active" value="1" class="peer hidden" <?= $user?->isActive() ? 'checked' : '' ?> />
                    <label for="status_active" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:text-white">{{lang=user_status_active}}</label>
                </div>
            </div>
        </div>
        <div class="mb-8 flex flex-col sm:flex-row sm:flex-nowrap gap-4">
            <?php
            if ($user->exists()) {
                echo <<<HTML
                <button id="delete-user" class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=delete}}">{{lang=delete}}</button>
                <button id="save-user" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=save}}">{{lang=save}}</button>
                HTML;
            } else {
                echo <<<HTML
                <button id="add-user" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=add}}">{{lang=add}}</button>
                HTML;
            }
            ?>
        </div>
    </form>
</main>
<script src="/js/user.editor.min.js" nonce="{{nonce}}" type="module"></script>