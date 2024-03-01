<?php

use Tischmann\Atlantis\{Template};

?>
<main class="md:container mx-4 md:mx-auto">
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <?php
        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'order',
                'title' => get_str('order'),
                'options' => $order_options
            ]
        );

        Template::echo(
            template: 'select_field',
            args: [
                'name' => 'direction',
                'title' => get_str('direction'),
                'options' => $direction_options
            ]
        );
        ?>
    </div>
    <a href="/{{env=APP_LOCALE}}/user" title="{{lang=article_new}}" class="mb-4 flex items-center justify-center p-3 rounded-lg bg-gray-200 hover:bg-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </a>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php
        foreach ($users as $user) {
            include "users_list_item.php";
        }
        ?>
        <?php require 'pagination.php'; ?>
    </div>
</main>
<script src="/js/users.list.min.js" nonce="{{nonce}}"></script>