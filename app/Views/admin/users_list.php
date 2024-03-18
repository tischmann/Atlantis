<?php

use Tischmann\Atlantis\{Template};

?>
<main class="md:container mx-4 my-4 md:mx-auto">
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <?php
        Template::echo(
            template: 'fields/select_field',
            args: [
                'name' => 'order',
                'title' => get_str('order'),
                'options' => $order_options
            ]
        );

        Template::echo(
            template: 'fields/select_field',
            args: [
                'name' => 'direction',
                'title' => get_str('direction'),
                'options' => $direction_options
            ]
        );
        ?>
    </div>
    <a href="/{{env=APP_LOCALE}}/user" title="{{lang=article_new}}" class="mb-4 flex items-center justify-center p-3 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow hover:shadow-lg transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </a>
    <ul class="grid grid-cols-1 gap-2">
        <?php
        foreach ($users as $user) {
            include "users_list_item.php";
        }
        ?>
    </ul>
    <div class="my-4">{{pagination}}</div>
</main>
<script nonce="{{nonce}}" type="module">
    import Select from '/js/atlantis.select.min.js'

    ['order', 'direction'].forEach((name) => {
        new Select(document.querySelector(`select[name="${name}"]`), {
            onchange: (value, changed) => {
                if (!changed) return
                const url = new URL(window.location.href)
                url.searchParams.set(name, value)
                window.location.href = url.toString()
            }
        })
    })
</script>