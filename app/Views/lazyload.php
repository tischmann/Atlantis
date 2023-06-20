<?php

echo <<<EOL
data-atlantis-lazyload data-url="{{url}}" data-page="{$pagination->page}" data-next="{$pagination->next}" data-last="{$pagination->last}" data-limit="{$pagination->limit}" data-search="{$search}" data-sort="{$sorting->type}" data-order="{$sorting->order}"
EOL;
