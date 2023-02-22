<?php

use Tischmann\Atlantis\{Locale, Template};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <form method="post" class="px-4 my-4" autocomplete="off">
        {{csrf}}
        <div class="relative mb-4" data-te-input-wrapper-init>
            <input type="text" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="localeCode" placeholder="{{lang=locale_code}}" name="code" value="" autocomplete="off" required />
            <label for="localeCode" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=locale_code}}</label>
        </div>
        <div class="relative mb-4" data-te-input-wrapper-init>
            <input type="text" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="localeTitle" placeholder="{{lang=locale_title}}" name="title" value="" autocomplete="off" />
            <label for="localeTitle" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=locale_title}}</label>
        </div>
        <div class="mb-4">
            <label class="mb-4 block text-neutral-500 dark:text-neutral-200">{{lang=locale_strings}}</label>
            <div class="flex items-center gap-4 mb-4 strings-template">
                <div class="relative" data-te-input-wrapper-init>
                    <input type="text" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="localeStringInput00" placeholder="{{lang=locale_key}}" name="keys[]" value="" autocomplete="off" />
                    <label for="localeStringInput00" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=locale_key}}</label>
                </div>
                <div class="relative" data-te-input-wrapper-init>
                    <input type="text" class="peer block min-h-[auto] w-full rounded border-0 bg-transparent py-[0.32rem] px-3 leading-[1.6] outline-none transition-all duration-200 ease-linear focus:placeholder:opacity-100 data-[te-input-state-active]:placeholder:opacity-100 motion-reduce:transition-none dark:text-neutral-200 dark:placeholder:text-neutral-200 [&:not([data-te-input-placeholder-active])]:placeholder:opacity-0" id="localeStringInput01" placeholder="{{lang=locale_value}}" name="values[]" value="" autocomplete="off" />
                    <label for="localeStringInput01" class="pointer-events-none absolute top-0 left-3 mb-0 max-w-[90%] origin-[0_0] truncate pt-[0.37rem] leading-[1.6] text-neutral-500 transition-all duration-200 ease-out peer-focus:-translate-y-[0.9rem] peer-focus:scale-[0.8] peer-focus:text-primary peer-data-[te-input-state-active]:-translate-y-[0.9rem] peer-data-[te-input-state-active]:scale-[0.8] motion-reduce:transition-none dark:text-neutral-200 dark:peer-focus:text-neutral-200">{{lang=locale_value}}</label>
                </div>
            </div>
            <button id="addStringsButton" type="button" class="inline-block w-full px-6 py-2.5 bg-sky-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-700 active:shadow-lg transition duration-150 ease-in-out"><i class="fas fa-plus"></i></button>
        </div>
        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($locale) {
                $locale = getenv('APP_LOCALE');

                Template::echo(
                    'admin/delete-button',
                    [
                        'id' => "delete-locale-{$locale}",
                        'title' => Locale::get('warning'),
                        'message' => Locale::get('locale_delete_confirm') . "?",
                        'url' => "/" . getenv('APP_LOCALE') . "/locale/delete/{$locale}",
                        'redirect' => "/" . getenv('APP_LOCALE') . "/admin/locales",
                    ]
                );
            }
            ?>
            <?= Template::html('admin/cancel-button', ['href' => '/{{env=APP_LOCALE}}/admin/locales']) ?>
            <?= Template::html('admin/save-button') ?>
        </div>
    </form>
    <script nonce="{{nonce}}">
        const addButton = document.getElementById('addStringsButton');

        let i = 0

        addButton.addEventListener('click', (event) => {
            const template = addButton.parentElement.querySelector('.strings-template')

            const clone = template.cloneNode(true);

            let j = 0

            clone.querySelectorAll('input').forEach(element => {
                element.value = ''
                element.id = `localeStringInput${i}${j++}`
            })

            j = 0

            clone.querySelectorAll('label').forEach(element => {
                element.setAttribute('for', `localeStringInput${i}${j++}`)
            })

            addButton.before(clone);

            i++
        })
    </script>
</main>