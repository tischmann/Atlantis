<header class="bg-white my-8">
    <div class="md:container mx-8 md:mx-auto">
        <div class="flex flex-row md:items-center flex-wrap md:justify-between gap-8">
            <div class="flex-grow flex items-center gap-4">
                <a href="/{{env=APP_LOCALE}}" class="flex items-center" aria-label="{{env=APP_TITLE}}">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 400 400" xml:space="preserve" width="32px" height="32px" class="text-sky-600">
                        <path fill="currentColor" d="M99.9,199.8C45,291.1,0,365.8,0,366s19.1,0.4,42.5,0.2l42.5-0.2l57.4-96c31.5-52.7,57.4-96,57.6-96s71.6,118.1,72,119.4
                c0.2,0.2-13.1,0.4-28.8,0.4h-29L193,329.7c-11.7,19.5-21.3,35.7-21.5,36.1c-0.2,0.4,40.1,0.4,114.1,0.4c62.9,0,114.3-0.2,114.3-0.4
                c0-0.4-199.7-332.2-199.9-332.2C199.9,33.9,154.9,108.5,99.9,199.8z" />
                    </svg>
                    <div class="uppercase text-4xl leading-8 font-bold -ml-1 tracking-wide">TLANTIS</div>
                </a>
            </div>

            <?php

            use Tischmann\Atlantis\App;

            if (App::getCurrentUser()->id) {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/signin" aria-label="{{lang=signin_submit}}" title="{{lang=signin_submit}}" class="flex items-center justify-center h-10 w-10 rounded-xl text-xl bg-gray-200 text-gray-800 transition hover:bg-gray-400 focus:bg-gray-300 focus:outline-none focus:ring-0 active:bg-gray-300 active:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </a>
                HTML;
            } else {
                echo <<<HTML
                <a href="/{{env=APP_LOCALE}}/signout" aria-label="{{lang=signout_submit}}" title="{{lang=signout_submit}}" class="flex items-center justify-center h-10 w-10 rounded-xl text-xl bg-gray-200 text-gray-800 transition hover:bg-gray-400 focus:bg-gray-300 focus:outline-none focus:ring-0 active:bg-gray-300 active:shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                </a>
                HTML;
            }
            ?>
        </div>
    </div>
</header>