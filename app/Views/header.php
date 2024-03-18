<?php

use Tischmann\Atlantis\Request;

$request = Request::instance();

$search_query = strval($request->request('query'));
?>
<!-- HEADER -->
<header class="py-4 bg-white dark:bg-gray-800 sm:sticky top-0 z-50">
    <div class="sm:container mx-4 md:mx-auto">
        <div class="flex flex-row md:items-center flex-wrap md:justify-between gap-4">
            <div class="flex-grow flex flex-col flex-wrap sm:flex-row sm:items-center gap-4 sm:gap-8">
                <!-- LOGO -->
                <a href="/{{env=APP_LOCALE}}" class="flex items-center" aria-label="{{env=APP_TITLE}}">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 400 400" xml:space="preserve" width="32" height="32" class="text-sky-600 w-8 h-8">
                        <path fill="currentColor" d="M99.9,199.8C45,291.1,0,365.8,0,366s19.1,0.4,42.5,0.2l42.5-0.2l57.4-96c31.5-52.7,57.4-96,57.6-96s71.6,118.1,72,119.4
                c0.2,0.2-13.1,0.4-28.8,0.4h-29L193,329.7c-11.7,19.5-21.3,35.7-21.5,36.1c-0.2,0.4,40.1,0.4,114.1,0.4c62.9,0,114.3-0.2,114.3-0.4
                c0-0.4-199.7-332.2-199.9-332.2C199.9,33.9,154.9,108.5,99.9,199.8z" />
                    </svg>
                    <div class="uppercase text-4xl leading-8 font-bold -ml-1 tracking-wide select-none">TLANTIS</div>
                </a>
                <!-- LOGO -->
                <!-- VISUALLY IMPAIRED VERSION -->
                <div id="visually-impaired-version" class="no-print flex items-center gap-2 hover:underline font-medium cursor-pointer text-gray-800 dark:text-white whitespace-nowrap">
                    <svg id="visually-impaired-version-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    <svg id="normal-version-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span>{{lang=visually_impaired_version}}</span>
                </div>
                <!-- VISUALLY IMPAIRED VERSION -->
                <!-- DARK MODE -->
                <div id="dark-mode-switch" class="no-print flex items-center gap-2 hover:underline font-medium cursor-pointer text-gray-800 dark:text-white whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 hidden dark:block">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 dark:hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <span>{{lang=dark_mode}}</span>
                </div>
                <!-- DARK MODE -->
                <!-- SEARCH -->
                <form class="relative sm:ml-auto group/search no-print grow" action="/{{env=APP_LOCALE}}/search">
                    <input id="search-articles" type="search" name="query" class="w-full px-3 py-2 pr-10 rounded-xl bg-gray-200 dark:bg-gray-700 dark:text-white outline-none ring-0 focus:ring-0" value="<?= $search_query ?>" title="{{lang=search}}" />
                    <div class="absolute right-0 top-1/2 -translate-x-1/3 -translate-y-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                </form>
                <!-- SEARCH -->
            </div>
            <div class="no-print flex items-center gap-4">

                <?php

                use Tischmann\Atlantis\App;

                $user = App::getUser();

                if ($user->exists()) {
                    if ($user->canModerate() ||  $user->canAuthor() || $user->isAdmin()) {
                        echo <<<HTML
                        <a href="/{{env=APP_LOCALE}}/dashboard" title="{{lang=dashboard}}" class="inline-block select-none rounded-xl p-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </a>
                        HTML;
                    }

                    echo <<<HTML
                    <a href="/{{env=APP_LOCALE}}/signout" aria-label="{{lang=signout_submit}}" title="{{lang=signout_submit}}" class="flex items-center justify-center p-2 rounded-xl text-xl bg-gray-200 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </a>
                    HTML;
                } else {
                    echo <<<HTML
                    <a href="/{{env=APP_LOCALE}}/signin" aria-label="{{lang=signin_submit}}" title="{{lang=signin_submit}}" class="flex items-center justify-center h-10 w-10 rounded-xl text-xl bg-gray-200 hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                        </svg>
                    </a>
                    HTML;
                }
                ?>
            </div>
        </div>
    </div>
</header>
<script nonce="{{nonce}}" type="module">
    import Cookie from '/js/atlantis.cookie.min.js'
    (function() {
        const cookie = new Cookie()

        let isVisuallyImpaired = cookie.get('vi') === 'true'

        let isDakrMode = cookie.get('dark') === 'true'

        const header = document.querySelector('header')

        const darkModeSwitch = document.getElementById('dark-mode-switch')

        window.addEventListener('scroll', function() {
            if (this.scrollY <= 10) header.classList.remove('sm:shadow-xl')
            else header.classList.add('sm:shadow-xl')
        })

        window.addEventListener('load', function() {
            const button = document.getElementById('visually-impaired-version')

            function visuallyImparedHandler() {
                document.documentElement.classList.toggle('visually-impaired')
                document.querySelectorAll('iframe').forEach((iframe) => {
                    iframe.contentDocument.documentElement.classList.toggle('visually-impaired')
                })
                document.getElementById('visually-impaired-version-icon').classList.toggle('hidden')
                document.getElementById('normal-version-icon').classList.toggle('hidden')
                button.querySelector('span').textContent = document.documentElement.classList.contains('visually-impaired') ?
                    '{{lang=normal_version}}' :
                    '{{lang=visually_impaired_version}}'
            }

            button.addEventListener('click', function() {
                cookie.set('vi', isVisuallyImpaired ? 'false' : 'true')
                visuallyImparedHandler()
            })

            if (isVisuallyImpaired) visuallyImparedHandler()

            function setMode(dark = false) {
                if (dark) document.documentElement.classList.add('dark')
                else document.documentElement.classList.remove('dark')
                document.querySelectorAll('iframe').forEach((iframe) => {
                    if (dark) iframe.contentDocument.documentElement.classList.add('dark')
                    else iframe.contentDocument.documentElement.classList.remove('dark')
                })
                cookie.set('dark', dark ? 'true' : 'false')
                darkModeSwitch.querySelector('span').textContent = dark ?
                    '{{lang=light_mode}}' :
                    '{{lang=dark_mode}}'
            }

            darkModeSwitch.addEventListener('click', function() {
                if (isDakrMode) setMode()
                else setMode(true)
                isDakrMode = !isDakrMode
            })

            if (isDakrMode) setMode(true)
            else setMode()

            document.getElementById('search-articles').addEventListener('search', function(event) {
                if (this.value == '') window.location.href = `${window.location.origin}/{{env=APP_LOCALE}}`
            })
        })
    })()
</script>
<!-- HEADER -->