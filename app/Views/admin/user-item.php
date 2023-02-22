<?php

use App\Models\User;
?>
<a href="/{{env=APP_LOCALE}}/edit/user/<?= $user->id ?>" class="user flex flex-col rounded-lg bg-white hover:bg-sky-800 dark:bg-sky-800 text-neutral-800 dark:text-neutral-50 dark:hover:bg-sky-700 hover:text-white shadow-lg md:flex-row" data-id="<?= $user->id ?>">
    <img class="rounded-t-lg object-cover w-24 h-24 md:rounded-none md:rounded-l-lg" src="/images/avatars/<?= $user->avatar ?>" alt="{{lang=users_avatar}}" />
    <div class="flex flex-col justify-start p-4">
        <h5 class="mb-2 text-xl font-medium"><?= $user->login ?></h5>
        <span class="font-medium">
            {{lang=user_role_<?= $user->role ?>}}
        </span>
    </div>
</a>
<script nonce="{{nonce}}">
    document.querySelector(`.user[data-id="<?= $user->id ?>"]`).addEventListener('error', function(e) {
        e.target.src = '/images/placeholder.svg';
    }, true)
</script>