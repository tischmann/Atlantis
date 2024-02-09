 <?php

    use Tischmann\Atlantis\{Request, Sorting};

    $request = new Request();

    $sort_type = $request->request('sort') ?: 'created_at';

    $sort_order = $request->request('order') ?: 'desc';

    ?>
 <div class="flex items-center gap-4 m-4 justify-end">
     <div class="relative" data-te-dropdown-ref>
         <div class="flex items-center gap-2 text-sm">
             <span class="drop-shadow"><i class="fa-solid fa-sort"></i></span>
             <button class="flex items-center justify-center whitespace-nowrap px-3 py-2 text-white rounded-lg bg-pink-600 hover:bg-pink-700 shadow active:bg-pink-700 focus:bg-pink-700 max-w-[256px] transition-all ease-in-out" type="button" id="sortingDropdown" data-te-dropdown-toggle-ref aria-expanded="false" data-te-ripple-init data-te-ripple-color="light">
                 <span class="drop-shadow max-w-[256px] truncate" title="{{lang=sorting_<?= $sort_type ?>_<?= $sort_order ?>_title}}">{{lang=sorting_<?= $sort_type ?>_<?= $sort_order ?>}}<span>
             </button>
             <ul class="absolute z-[1000] float-left m-0 hidden min-w-max list-none overflow-hidden rounded-lg border-none bg-white bg-clip-padding text-left text-base shadow-lg [&[data-te-dropdown-show]]:block" aria-labelledby="sortingDropdown" data-te-dropdown-menu-ref>
                 <?php

                    foreach ($sortings ?? [] as $sorting) {
                        assert($sorting instanceof Sorting);

                        $href = "?sort={$sorting->type}&order={$sorting->order}";

                        if ($search) $href .= "&query={$search}";

                        $selected = $sorting->type == $sort_type && $sorting->order == $sort_order;

                        echo <<<HTML
                    <li>
                        <a class="flex items-center w-full whitespace-nowrap bg-transparent py-2 px-4 text-sm font-normal text-neutral-700 hover:bg-neutral-100 active:text-neutral-800 active:no-underline disabled:pointer-events-none disabled:bg-transparent disabled:text-neutral-400" href="{$href}" data-te-dropdown-item-ref title="{{lang=sorting_{$sort_type}_{$sort_order}_title}}">{{lang=sorting_{$sorting->type}_{$sorting->order}}}
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
 </div>