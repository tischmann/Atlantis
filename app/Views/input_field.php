<?php

$name ??= "unknown_input_field_" . uniqid(more_entropy: true);

$id ??= $name;

$value ??= "";

$label ??= "Label";

$type ??= 'text';

$autocomplete = isset($autocomplete) ? "autocomplete=\"{$autocomplete}\"" : "";

$required = isset($required) ? (boolval($required) ? "required" : "") : "";

?>
<div class="relative w-full">
    <label for="<?= $id ?>" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-1 rounded-md"><?= $label ?></label>
    <input type="<?= $type ?>" class="py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 rounded-lg w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:border-sky-600 transition" id="<?= $id ?>" name="<?= $name ?>" value="<?= $value ?>" <?= $required ?> <?= $autocomplete ?>>
</div>