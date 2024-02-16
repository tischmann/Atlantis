<?php

use Tischmann\Atlantis\App;

if (App::getCurrentUser()->isAdmin()) {
    echo <<<HTML
    <a href="/{{env=APP_LOCALE}}/users" aria-label="{{lang=user_list}}" title="{{lang=user_list}}" class="inline-block select-none font-semibold rounded-xl px-3 py-2 bg-gray-200 hover:bg-gray-300">{{lang=user_list}}</a>
    HTML;
}
