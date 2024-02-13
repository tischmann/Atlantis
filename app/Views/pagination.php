<?php

use Tischmann\Atlantis\Pagination;

assert($pagination instanceof Pagination);

if ($pagination->last > 1) {
    echo <<<HTML
    <div class="flex items-center justify-end">
        <nav class="flex bg-gray-200 rounded-xl text-base font-semibold">
            <a href="?{$pagination->getFirstQuery()}" class="flex items-center justify-center h-10 w-10 rounded-xl hover:bg-slate-300">
                <i class="fa-solid fa-angles-left"></i></a>
            <a href="?{$pagination->getPrevQuery()}" class="flex items-center justify-center h-10 w-10 rounded-xl hover:bg-slate-300">
                <i class="fa-solid fa-angle-left"></i>
            </a>
    HTML;


    foreach ($pagination->prev_pages as $page) {
        echo <<<HTML
        <a href="?{$pagination->getPageQuery($page)}" class="flex items-center justify-center h-10 w-10 rounded-xl hover:bg-slate-300">
            {$page}
        </a>
        HTML;
    }

    foreach ($pagination->next_pages as $page) {
        echo <<<HTML
        <a href="?{$pagination->getPageQuery($page)}" class="flex items-center justify-center h-10 w-10 rounded-xl hover:bg-slate-300">
            {$page}
        </a>
        HTML;
    }

    echo <<<HTML
            <a href="?{$pagination->getNextQuery()}" class="flex items-center justify-center h-10 w-10 rounded-xl hover:bg-slate-300">
                <i class="fa-solid fa-angle-right"></i>
            </a>
            <a href="?{$pagination->getLastQuery()}" class="flex items-center justify-center h-10 w-10 rounded-xl hover:bg-slate-300">
                <i class="fa-solid fa-angles-right"></i>
            </a>
        </nav>
    </div>
    HTML;
}
