<?php assert($user instanceof \App\Models\User) ?>
<tr class="border-b hover:bg-gray-100 even:bg-gray-50 <?= $user->isActive() ? ''  : 'text-red-600' ?> <?= $user->isAdmin() ? 'text-sky-600' : '' ?>">
    <td class="whitespace-nowrap px-6 py-4"><?= $user->login ?></td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->name ?></td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->getUserRoleText() ?></td>
    <td class="whitespace-nowrap px-6 py-4">{{lang=user_status_<?= $user->isActive() ? ''  : 'in' ?>active}}</td>
    <td class="whitespace-nowrap px-6 py-4 text-center">
        <a href="/user/<?= $user->id ?>" class="select-none underline" title="{{lang=edit}}">{{lang=edit}}</a>
    </td>
    <td class="whitespace-nowrap px-6 py-4 text-center">
        <span data-id="<?= $user->id ?>" class="usr-del-btn select-none underline cursor-pointer" title="{{lang=delete}}">{{lang=delete}}</span>
    </td>
</tr>