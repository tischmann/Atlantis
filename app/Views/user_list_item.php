<tr class="border-b hover:bg-gray-100 even:bg-gray-50 font-normal">
    <td class="whitespace-nowrap px-6 py-4">
        <div class="flex flex-row flex-nowrap gap-4">
            <a href="/user/<?= $user->id ?>" class="block bg-sky-600 hover:bg-sky-700 text-white font-bold p-2 rounded-xl outline-none transition select-none" title="{{lang=edit}}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </a>
            <button type="button" data-id="<?= $user->id ?>" class="usr-del-btn bg-red-600 hover:bg-red-700 text-white font-bold p-2 rounded-xl outline-none transition select-none" title="{{lang=delete}}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->login ?></td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->name ?></td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->getUserRoleText() ?></td>
    <td class="whitespace-nowrap px-6 py-4">{{lang=user_status_<?= $user->status ? ''  : 'in' ?>active}}</td>
</tr>