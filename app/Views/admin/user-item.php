<?php

use App\Models\User;
?>
<a href="/{{env=APP_LOCALE}}/edit/user/<?= $user->id ?>" class="user w-full md:w-auto flex p-4 gap-4 rounded-lg bg-white hover:text-sky-600 dark:hover:text-white dark:bg-sky-800 text-neutral-800 dark:text-neutral-50 dark:hover:bg-sky-700 shadow-lg" data-id="<?= $user->id ?>">
    <img class="block rounded-md shadow-md h-16 w-16 object-cover" width="400" height="400" src="<?= $user->avatar_src ?>" alt="{{lang=users_avatar}}" />
    <div class="flex flex-col justify-evenly">
        <h5 class="text-xlg font-medium truncate drop-shadow"><?= $user->login ?></h5>
        <span class="text-xs truncate drop-shadow">
            {{lang=user_role_<?= $user->role ?>}}
        </span>
        <span class="text-xs truncate drop-shadow">
            {{lang=user_status_<?= intval($user->status) ?>}}
        </span>
    </div>
</a>