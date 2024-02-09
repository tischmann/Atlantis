<?php

use Tischmann\Atlantis\{Locale, Template};

?>
<main class="md:container md:mx-auto">
    <form method="post" class="m-4" autocomplete="off">
        {{csrf}}
        <?php

        if (!$code) {
            Template::echo(
                'admin/input-field',
                [
                    'type' => 'text',
                    'label' => Locale::get('locale_code'),
                    'name' => 'code',
                    'value' => '',
                    'required' => true,
                    'autocomplete' => false,
                    'id' => 'localeCode',
                ]
            );

            Template::echo(
                'admin/input-field',
                [
                    'type' => 'text',
                    'label' => Locale::get('locale_title'),
                    'name' => 'title',
                    'value' => '',
                    'required' => true,
                    'autocomplete' => false,
                    'id' => 'localeTitle',
                ]
            );
        }

        ?>

        <div class="mb-4">
            <?php

            $i = 0;

            foreach ($strings as $key => $value) {
                echo <<<HTML
                <div class="w-full flex items-center gap-4 strings-template">
                HTML;

                Template::echo(
                    'admin/input-field',
                    [
                        'type' => 'text',
                        'label' => Locale::get('locale_key'),
                        'name' => 'keys[]',
                        'value' => $key,
                        'id' => "localeString-{$i}-0",
                        'flex' => true,
                    ]
                );

                Template::echo(
                    'admin/input-field',
                    [
                        'type' => 'text',
                        'label' => Locale::get('locale_value'),
                        'name' => 'values[]',
                        'value' => $value,
                        'id' => "localeString-{$i}-1",
                        'flex' => true,
                    ]
                );

                echo <<<HTML
                    <i class="mb-4 delete-string fas fa-times text-pink-600 hover:text-pink-500 text-xl cursor-pointer"></i>                    
                </div>
                HTML;

                $i++;
            }
            ?>
            <button id="addStringsButton" data-last="<?= $i ?>" type="button" class="inline-block w-full px-6 py-2.5 bg-sky-800 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-700 active:shadow-lg transition duration-150 ease-in-out"><i class="fas fa-plus"></i></button>
        </div>
        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($code) {
                Template::echo(
                    'admin/delete-button',
                    [
                        'id' => "delete-locale-{$code}",
                        'title' => Locale::get('warning'),
                        'message' => Locale::get('locale_delete_confirm') . "?",
                        'url' => "/" . getenv('APP_LOCALE') . "/locale/delete/{$code}",
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
        document.querySelectorAll('.delete-string').forEach(element => {
            element.addEventListener('click', (event) => {
                event.target.parentElement.remove()
            })
        })

        const addButton = document.getElementById('addStringsButton');

        let i = parseInt(addButton.dataset.last, 10);

        addButton.addEventListener('click', (event) => {
            const template = addButton.parentElement.querySelector('.strings-template')

            const clone = template.cloneNode(true);

            let j = 0

            clone.querySelectorAll('input').forEach(element => {
                element.value = ''
                element.id = `localeString-${i}-${j++}`
            })

            j = 0

            clone.querySelectorAll('label').forEach(element => {
                element.setAttribute('for', `localeString-${i}-${j++}`)
            })

            clone.querySelectorAll('.delete-string').forEach(element => {
                element.addEventListener('click', (event) => {
                    event.target.parentElement.remove()
                })
            })

            addButton.before(clone);

            i++
        })
    </script>
</main>