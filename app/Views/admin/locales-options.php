<?php

use Tischmann\Atlantis\{Locale};

foreach (Locale::available() as $value) {
    $selected = $value === $locale ? 'selected' : '';

    echo <<<HTML
    <option value="{$value}" {$selected} title="{{lang=locale_{$value}}}">{{lang=locale_{$value}}}</option>
    HTML;
}
