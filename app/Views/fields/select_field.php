<select name="{{name}}" title="{{title}}">
    <?php
    foreach ($options as $option) {
        $text = $option['text'] ?? null;

        $value = $option['value'] ?? null;

        if ($text === null || $value === null) continue;

        $value = strval($value);

        $text = strval($text);

        $level = intval($option['level'] ?? 0);

        $selected = boolval($option['selected'] ?? false);

        $selected = $selected ? 'selected' : '';

        echo <<<HTML
        <option value="{$value}" {$selected} data-level="{$level}">{$text}</option>
        HTML;
    }
    ?>
</select>