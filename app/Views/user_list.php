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
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php
        foreach ($users as $user) {
            include "user_list_item.php";
        }
        ?>
        <?php require 'pagination.php'; ?>
    </div>
    <?php require 'user_delete_script.php'; ?>
</main>
<script nonce="{{nonce}}">
    const fields = [
        'role',
        'status',
        'order',
        'direction'
    ]

    fields.forEach((field) => {
        document.select(
            document.getElementById(`select_field_${field}`), {
                onchange: (value) => {
                    const url = new URL(window.location.href);
                    url.searchParams.set(field, value);
                    window.location.href = url.toString();
                }
            })
    })
</script>