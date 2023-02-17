<?php

use App\Models\User;

?>
<div class="offcanvas offcanvas-start fixed bottom-0 flex flex-col max-w-full bg-white invisible bg-clip-padding shadow-sm outline-none transition duration-300 ease-in-out text-gray-700 top-0 left-0 border-none w-96" tabindex="-1" id="dashboardMenu" aria-labelledby="dashboardMenuLabel">
    <div class="offcanvas-header flex items-center justify-between p-4">
        <h5 class="offcanvas-title mb-0 leading-normal font-semibold" id="dashboardMenuLabel">
            <div class="flex items-center gap-4 uppercase">
                <img src="/favicon-32x32.png" class="w-10 h-10" alt="Logo">{{env=APP_TITLE}}
            </div>
        </h5>
        <button type="button" class="btn-close box-content w-4 h-4 p-2 -my-5 -mr-2 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow overflow-y-auto relative">
        <!-- Dashboard menu -->
        <div class="w-full h-full bg-white absolute" id="sidenavSecExample">
            <div class="pt-4 pb-2 px-6">
                <a href="#!">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <img src="/images/placeholder.webp" class="rounded-full w-10 h-10" alt="Avatar">
                        </div>
                        <div class="grow ml-3">
                            <p class="text-sm font-semibold text-blue-600"><?= User::current()->login ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <ul class="relative px-1">
                <li class="relative">
                    <a class="flex items-center text-sm py-4 px-6 h-12 overflow-hidden text-gray-700 text-ellipsis whitespace-nowrap rounded hover:text-blue-600 hover:bg-blue-50 transition duration-300 ease-in-out" href="/{{env=APP_LOCALE}}/admin/categories" data-mdb-ripple="true" data-mdb-ripple-color="primary" title="{{lang=categories}}" aria-label="{{lang=categories}}">
                        <i class="fas fa-sitemap w-3 h-3 mr-3"></i>
                        <span>{{lang=categories}}</span>
                    </a>
                </li>
                <li class="relative">
                    <a class="flex items-center text-sm py-4 px-6 h-12 overflow-hidden text-gray-700 text-ellipsis whitespace-nowrap rounded hover:text-blue-600 hover:bg-blue-50 transition duration-300 ease-in-out" href="/{{env=APP_LOCALE}}/admin/articles" data-mdb-ripple="true" data-mdb-ripple-color="primary" title="{{lang=articles}}" aria-label="{{lang=articles}}">
                        <i class="fas fa-newspaper w-3 h-3 mr-3"></i>
                        <span>{{lang=articles}}</span>
                    </a>
                </li>
            </ul>
            <div class="text-center bottom-0 absolute w-full">
                <hr class="m-0">
                <p class="py-2 text-sm text-gray-700"><?= $_SERVER['HTTP_HOST'] ?></p>
            </div>
        </div>
        <!-- Dashboard menu -->
    </div>
</div>