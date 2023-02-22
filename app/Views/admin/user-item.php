<?php

use App\Models\User;
?>
<a href="/{{env=APP_LOCALE}}/edit/user/<?= $user->id ?>" class="user w-full md:w-auto flex flex-col rounded-lg bg-white hover:text-sky-600 dark:hover:text-white dark:bg-sky-800 text-neutral-800 dark:text-neutral-50 dark:hover:bg-sky-700 shadow-lg md:flex-row" data-id="<?= $user->id ?>">
    <img class="rounded-t-lg object-cover w-full md:w-24 h-auto md:h-24 md:rounded-none md:rounded-l-lg" src="<?= $user->avatar_src ?>" alt="{{lang=users_avatar}}" />
    <div class="flex flex-col justify-start p-4">
        <h5 class="mb-2 text-xl font-medium"><?= $user->login ?></h5>
        <span class="text-sm">
            {{lang=user_role_<?= $user->role ?>}}
        </span>
    </div>
</a>