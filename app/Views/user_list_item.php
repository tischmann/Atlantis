<tr class="border-b hover:bg-gray-100 even:bg-gray-50 font-normal">
    <td class="whitespace-nowrap px-6 py-4 text-center">
        <a href="/user/<?= $user->id ?>" class="inline-block bg-sky-600 hover:bg-sky-700 text-white font-bold p-2 rounded-xl outline-none transition select-none" title="{{lang=edit}}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
            </svg>
        </a>
    </td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->login ?></td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->name ?></td>
    <td class="whitespace-nowrap px-6 py-4"><?= $user->getUserRoleText() ?></td>
    <td class="whitespace-nowrap px-6 py-4">{{lang=user_status_<?= $user->status ? ''  : 'in' ?>active}}</td>
    <td class="whitespace-nowrap px-6 py-4 text-center">
        <button type="button" data-id="<?= $user->id ?>" class="usr-del-btn bg-red-600 hover:bg-red-700 text-white font-bold p-2 rounded-xl outline-none transition select-none" title="{{lang=delete}}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </td>
</tr>