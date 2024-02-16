<?php

use Tischmann\Atlantis\Pagination;

$pagination ??= new Pagination();

assert($pagination instanceof Pagination);

if ($pagination->last > 1) {
    echo <<<HTML
        <div class="flex items-center justify-end">
            <nav class="w-full md:w-auto flex justify-center md:justify-end flex-nowrap gap-2 rounded-xl text-base font-semibold">
                <a href="?{$pagination->getFirstQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-200 hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                    </svg>
                </a>
                <a href="?{$pagination->getPrevQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-200 hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>
        HTML;


    foreach ($pagination->prev_pages as $page) {
        echo <<<HTML
        <a href="?{$pagination->getPageQuery($page)}" class="hidden md:flex items-center justify-center h-10 w-10 rounded-lg bg-gray-200 hover:bg-gray-300">
            {$page}
        </a>
        HTML;
    }

    echo <<<HTML
        <a href="?{$pagination->getPageQuery($pagination->page)}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-sky-400">
            {$pagination->page}
        </a>
        HTML;

    foreach ($pagination->next_pages as $page) {
        echo <<<HTML
        <a href="?{$pagination->getPageQuery($page)}" class="hidden md:flex items-center justify-center h-10 w-10 rounded-lg bg-gray-200 hover:bg-gray-300">
            {$page}
        </a>
        HTML;
    }

    echo <<<HTML
                <a href="?{$pagination->getNextQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-200 hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
                <a href="?{$pagination->getLastQuery()}" class="flex items-center justify-center h-10 w-10 rounded-lg bg-gray-200 hover:bg-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </nav>
        </div>
        HTML;
}
