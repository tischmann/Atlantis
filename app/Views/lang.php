<div class="relative" data-te-dropdown-ref>
    <button class="flex items-center whitespace-nowrap rounded-lg bg-gray-300 dark:bg-sky-800 px-3 py-2 text-xs font-medium uppercase leading-normal transition duration-150 ease-in-out hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-200 dark:hover:bg-sky-700 dark:focus:bg-sky-700 dark:active:bg-sky-700 focus:outline-none focus:ring-0  motion-reduce:transition-none" type="button" id="localeSelector" data-te-dropdown-toggle-ref aria-expanded="false" data-te-ripple-init data-te-ripple-color="light">
        <img src="/images/flags/1x1/{{env=APP_LOCALE}}.svg" width="16" height="16" alt="{{lang=locale_{{env=APP_LOCALE}}}}" />
        <span class="pl-2 pr-4 w-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>
    <ul class="absolute z-[1000] float-left m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-left text-base shadow-lg dark:bg-neutral-700 [&[data-te-dropdown-show]]:block" aria-labelledby="localeSelector" data-te-dropdown-menu-ref>
        <?php

        use Tischmann\Atlantis\Locale;

        foreach (Locale::available() as $locale) {
            $current = $locale === getenv('APP_LOCALE');

            $bg = $current
                ? 'bg-neutral-100 dark:bg-neutral-600'
                : 'bg-transparent';

            $uri = $_SERVER['REQUEST_URI'];

            $href = preg_match('/^\/[a-z]{2}\/.*/', $uri)
                ? preg_replace('/^\/[a-z]{2}/', "/{$locale}", $uri)
                : "/{$locale}{$uri}";

            echo <<<HTML
                <li>
                    <a class="flex items-center gap-3 w-full whitespace-nowrap {$bg} py-2 px-4 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-neutral-600" href="{$href}" data-te-dropdown-item-ref>
                        <img src="/images/flags/1x1/{$locale}.svg" width="16" height="16" alt="{{lang=locale_{$locale}}}" />
                        <span>{{lang=locale_{$locale}}}</span>
                HTML;


            if ($current) {
                echo <<<HTML
                            <i class="fas fa-check pl-3 text-green-500 float-right"></i>
                        HTML;
            }

            echo <<<HTML
                    </a>
                </li>
                HTML;
        }

        ?>
    </ul>
</div>