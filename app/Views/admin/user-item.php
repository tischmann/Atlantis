<div class="rounded-lg shadow-lg bg-white w-full">
    <div class="p-6">
        <h5 class="text-gray-900 text-xl font-medium mb-2 truncate"><?= $user->login ?></h5>
        <a href="/{{env=APP_LOCALE}}/user/<?= $user->id ?>" aria-label="{{lang=edit}}" class="inline-block px-4 py-3 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=edit}}</a>
    </div>
</div>