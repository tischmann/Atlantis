<?php
assert($user instanceof \App\Models\User);
?>
<main class="md:container mx-4 md:mx-auto">
    <h1 class="text-2xl font-bold mb-4 select-none bg-gray-200 text-gray-800 rounded-xl px-4 py-3">
        <?= $user?->exists() ? get_str('user_update') : get_str('user_new') ?>
    </h1>
    <form method="POST" class="" autocomplete="new-password">
        {{csrf}}
        <div class="mb-4">
            <label for="name" class="block mb-2 select-none">{{lang=user_name}}:</label>
            <input type="text" id="name" name="name" value="<?= strval($user?->name) ?>" class="w-full px-3 py-2 border focus:outline-none border-gray-300 rounded-xl focus:border-sky-600" required autocomplete="new-password">
        </div>
        <div class="mb-4">
            <label for="login" class="block mb-2 select-none">{{lang=user_login}}:</label>
            <input type="text" id="login" name="login" value="<?= strval($user?->login) ?>" class="w-full px-3 py-2 border focus:outline-none border-gray-300 rounded-xl focus:border-sky-600" required autocomplete="new-password">
        </div>
        <div class="mb-4">
            <label for="password" class="block mb-2 select-none">{{lang=user_password}}:</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border focus:outline-none border-gray-300 rounded-xl focus:border-sky-600" <?= $user?->exists() ? '' : 'required' ?> autocomplete="new-password">
        </div>
        <div class="mb-4">
            <label for="password_repeat" class="block mb-2 select-none">{{lang=user_password_repeat}}:</label>
            <input type="password" id="password_repeat" name="password_repeat" class="w-full px-3 py-2 border focus:outline-none border-gray-300 rounded-xl focus:border-sky-600" <?= $user?->exists() ? '' : 'required' ?> autocomplete="new-password">
        </div>
        <div class="mb-4">
            <label for="remarks" class="block mb-2 select-none">{{lang=user_remarks}}:</label>
            <textarea id="remarks" name="remarks" class="w-full px-3 py-2 border focus:outline-none border-gray-300 rounded-xl focus:border-sky-600 resize-y min-h-16" autocomplete="new-password"><?= strval($user?->remarks) ?></textarea>
        </div>
        <div class="mb-4 select-none">
            <label class="block mb-2 select-none">{{lang=user_role}}:</label>
            <div class="grid w-full grid-cols-1 md:grid-cols-3 gap-2 rounded-xl bg-white border border-gray-300 p-2">
                <div>
                    <input type="radio" name="role" id="role_guest" value="0" class="peer hidden" <?= $user?->isGuest() ? 'checked' : '' ?> />
                    <label for="role_guest" class="block cursor-pointer select-none rounded-xl p-2 text-center bg-gray-200 peer-checked:bg-gray-600 peer-checked:font-bold peer-checked:text-white">{{lang=user_role_guest}}</label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_user" value="1" class="peer hidden" <?= $user?->isUser() ? 'checked' : '' ?> />
                    <label for="role_user" class="block cursor-pointer select-none rounded-xl p-2 text-center bg-gray-200 peer-checked:bg-sky-600 peer-checked:font-bold peer-checked:text-white">{{lang=user_role_user}}</label>
                </div>
                <div>
                    <input type="radio" name="role" id="role_admin" value="255" class="peer hidden" <?= $user?->isAdmin() ? 'checked' : '' ?> />
                    <label for="role_admin" class="block cursor-pointer select-none rounded-xl p-2 text-center bg-gray-200 peer-checked:bg-red-600 peer-checked:font-bold peer-checked:text-white">{{lang=user_role_admin}}</label>
                </div>
            </div>
        </div>
        <div class="mb-4 select-none">
            <label class="block mb-2 select-none">{{lang=user_status}}:</label>
            <div class="grid w-full grid-cols-1 md:grid-cols-2 gap-2 rounded-xl bg-white border border-gray-300 p-2">
                <div>
                    <input type="radio" name="status" id="status_inactive" value="0" class="peer hidden" <?= $user?->isInactive() ? 'checked' : '' ?> />
                    <label for="status_inactive" class="block cursor-pointer select-none rounded-xl p-2 text-center bg-gray-200 peer-checked:bg-red-600 peer-checked:font-bold peer-checked:text-white">{{lang=user_status_inactive}}</label>
                </div>
                <div>
                    <input type="radio" name="status" id="status_active" value="1" class="peer hidden" <?= $user?->isActive() ? 'checked' : '' ?> />
                    <label for="status_active" class="block cursor-pointer select-none rounded-xl p-2 text-center bg-gray-200 peer-checked:bg-green-600 peer-checked:font-bold peer-checked:text-white">{{lang=user_status_active}}</label>
                </div>
            </div>
        </div>
        <div class="mb-4 text-right">
            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded-xl w-full md:w-auto outline-none transition select-none">{{lang=add}}</button>
        </div>
    </form>
</main>