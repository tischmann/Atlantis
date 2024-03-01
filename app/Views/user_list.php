<?php

use Tischmann\Atlantis\{Template};

?>
<main class="md:container mx-4 md:mx-auto">
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <select id="select_field_order" name="order" title="{{lang=order}}">
            <?php
            foreach ($order_options as $option) {
                echo <<<HTML
                <option value="{$option['value']}" {$option['selected']} data-level="{$option['level']}">{$option['text']}</option>
                HTML;
            }
            ?>
        </select>
        <select id="select_field_direction" name="direction" title="{{lang=direction}}">
            <?php
            foreach ($direction_options as $option) {
                echo <<<HTML
                <option value="{$option['value']}" {$option['selected']} data-level="{$option['level']}">{$option['text']}</option>
                HTML;
            }
            ?>
        </select>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php
        foreach ($users as $user) {
            Template::echo('user_list_item', ['user' => $user]);
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