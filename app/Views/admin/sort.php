<div class="fixed bottom-4 left-4">
    <div class="relative" data-te-dropdown-ref>
        <button class="flex items-center justify-center whitespace-nowrap rounded-full bg-primary h-12 w-12 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] motion-reduce:transition-none" type="button" id="dropdownMenuButton1" data-te-dropdown-toggle-ref aria-expanded="false" data-te-ripple-init data-te-ripple-color="light">
            <i class="fas fa-sort text-lg"></i>
        </button>
        <ul class="absolute z-[1000] float-left m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-left text-base shadow-lg dark:bg-neutral-700 [&[data-te-dropdown-show]]:block" aria-labelledby="dropdownMenuButton1" data-te-dropdown-menu-ref>
            <?php

            use Tischmann\Atlantis\{Request, Sorting};

            $request = new Request();

            $sort_type = $request->request('sort');

            $sort_order = $request->request('order');

            foreach ($sortings as $sorting) {
                assert($sorting instanceof Sorting);

                $href = "?sort={$sorting->type}&order={$sorting->order}";

                $selected = $sorting->type == $sort_type && $sorting->order == $sort_order;

                echo <<<HTML
                <li>
                    <a class="flex items-center w-full whitespace-nowrap bg-transparent py-2 px-4 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400 dark:text-neutral-200 dark:hover:bg-neutral-600" href="{$href}" data-te-dropdown-item-ref>{{lang=sorting_{$sorting->type}_{$sorting->order}}}
                HTML;

                if ($selected) {
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
</div>