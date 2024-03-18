<?php

use Tischmann\Atlantis\Pagination;

$pagination ??= new Pagination();

assert($pagination instanceof Pagination);

if ($pagination->last > 1) {
    echo <<<HTML
    <div class="flex items-center justify-end text-gray-800 dark:text-white">
        <nav class="w-full md:w-auto flex justify-center md:justify-end flex-nowrap gap-2 rounded-xl text-sm font-semibold">
            <a href="?{$pagination->getFirstQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 shadow-md" title="{{lang=pagination_first}}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                </svg>
            </a>
            <a href="?{$pagination->getPrevQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 shadow-md" title="{{lang=pagination_prev}}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
    HTML;

    foreach ($pagination->prev_pages as $page) {
        echo <<<HTML
        <a href="?{$pagination->getPageQuery($page)}" class="hidden md:flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 shadow-md" title="{{lang=pagination_page}} {$page}">
            {$page}
        </a>
        HTML;
    }

    echo <<<HTML
    <span class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 text-sky-500 cursor-default shadow-md" title="{{lang=pagination_page}} {$pagination->page}">
        {$pagination->page}
    </span>
    HTML;

    foreach ($pagination->next_pages as $page) {
        echo <<<HTML
        <a href="?{$pagination->getPageQuery($page)}" class="hidden md:flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 shadow-md" title="{{lang=pagination_page}} {$page}">
            {$page}
        </a>
        HTML;
    }

    echo <<<HTML
            <a href="?{$pagination->getNextQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 shadow-md" title="{{lang=pagination_next}}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
            <a href="?{$pagination->getLastQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 shadow-md" title="{{lang=pagination_last}}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </nav>
    </div>
    HTML;
}
