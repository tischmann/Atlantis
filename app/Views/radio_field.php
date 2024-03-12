<?php

$name ??= "unknown_input_field_" . uniqid(more_entropy: true);

$value ??= "";

$label ??= "Label";

$options ??= [];

?>
<div class="relative">
    <span class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-1 rounded-md"><?= $label ?></span>
    <div class="flex flex-col sm:flex-row justify-evenly flex-wrap gap-2 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-lg p-4">
        <?php
        $i = 0;

        foreach ($options as $option) {
            $option_id = "{$name}_option_{$i}";

            $option_value = $option['value'] ?? "";

            $option_checked = $value === $option_value ? true : false;

            $option_checked = $option_checked ? "checked" : "";

            $option_label = $option['label'] ?? "Option {$i}";

            $option_active = $option['active'] ?? 'peer-checked:bg-sky-600 peer-checked:text-white';

            echo <<<HTML
            <div class="relative grow">
                <input type="radio" name="{$name}" id="{$option_id}" value="{$option_value}" class="peer hidden" {$option_checked}/>
                <label for="{$option_id}" class="block grow cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 text-gray-800 dark:text-white dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 {$option_active} font-medium text-ellipsis overflow-hidden transition">{$option_label}</label>
            </div>
            HTML;

            $i++;
        }
        ?>
    </div>
</div>