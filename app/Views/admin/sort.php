    <div class="fixed bottom-4 left-4">
        <div class="dropdown relative">
            <button class=" dropdown-toggle h-12 w-12 flex items-center justify-center bg-purple-600 text-white  font-medium text-xs rounded-full shadow-md hover:bg-purple-700 hover:shadow-lg focus:bg-purple-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-purple-800 active:shadow-lg active:text-white  transition duration-150 ease-in-out" type="button" id="sortButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-sort text-lg"></i>
            </button>
            <ul class="dropdown-menu min-w-max absolute bg-white text-base z-50 float-left py-2 list-none text-left rounded-lg shadow-lg mt-1 hidden m-0 bg-clip-padding border-none" aria-labelledby="sortButton">
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
                        <a class=" dropdown-item text-sm py-2 px-4 font-normal block w-full whitespace-nowrap bg-transparent text-gray-700 hover:bg-gray-100" href="{$href}">{{lang=sorting_{$sorting->type}_{$sorting->order}}}
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