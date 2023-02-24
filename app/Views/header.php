<header class="bg-white dark:bg-gray-800 sticky top-0 z-50">
    <div class="md:container md:mx-auto p-4">
        <div class="flex flex-row md:items-center flex-wrap md:justify-between mb-4 gap-4">
            <div class="order-1 flex-grow flex items-center gap-4">
                <a href="/{{env=APP_LOCALE}}" class="flex items-center" aria-label="{{env=APP_TITLE}}">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 400 400" xml:space="preserve" width="32px" height="32px" class="text-sky-600 dark:text-sky-500">
                        <path fill="currentColor" d="M99.9,199.8C45,291.1,0,365.8,0,366s19.1,0.4,42.5,0.2l42.5-0.2l57.4-96c31.5-52.7,57.4-96,57.6-96s71.6,118.1,72,119.4
                c0.2,0.2-13.1,0.4-28.8,0.4h-29L193,329.7c-11.7,19.5-21.3,35.7-21.5,36.1c-0.2,0.4,40.1,0.4,114.1,0.4c62.9,0,114.3-0.2,114.3-0.4
                c0-0.4-199.7-332.2-199.9-332.2C199.9,33.9,154.9,108.5,99.9,199.8z" />
                    </svg>
                    <div class="uppercase text-4xl leading-8 font-bold -ml-1 tracking-wide">TLANTIS</div>
                </a>
                <?php

                use App\Models\User;

                if (User::current()->isAdmin()) {
                    echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/admin" class="text-white bg-pink-600 h-8 w-8 flex justify-center items-center rounded-lg transition-all ease-in-out hover:bg-pink-700 hover:shadow-lg" aria-label="dashboard">
                    <i class="fas fa-gear"></i>
                </a>
                HTML;
                }
                ?>
            </div>
            <form class="order-3 md:order-2 relative flex items-center flex-grow md:flex-grow-0" method="GET">
                <input type="search" class="relative m-0 block w-full md:w-1/3 min-w-[200px] flex-auto rounded-lg border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 pr-10 py-1.5 text-base font-normal text-neutral-700 outline-none transition duration-300 ease-in-out focus:border-primary-600 focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200" placeholder="{{lang=search}}" aria-label="{{lang=search}}" name="search" value="<?= $search ?>" />
                <span class="absolute right-0 input-group-text flex items-center whitespace-nowrap rounded px-3 py-1.5 text-center text-base font-normal text-neutral-700 dark:text-neutral-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </span>
            </form>
            <div class="flex-grow-0 order-2 md:order-3"><?php require __DIR__ . "/lang.php" ?></div>
        </div>
        <?php include __DIR__ . "/breadcrumbs.php" ?>
    </div>
    <script nonce="{{nonce}}">
        document.querySelector('input[type="search"][name="search"]')
            .addEventListener('search', function(e) {
                if (e.target.value == '') {
                    let search = window.location.search.split('?')[1]?.split('&')

                    search = search.filter(function(value, index, arr) {
                        return value.split('=')[0] != 'search'
                    });

                    search = (search.length > 0 ? '?' + search.join('&') : '')

                    window.location.href = window.location.pathname + search
                }
            });
    </script>
</header>