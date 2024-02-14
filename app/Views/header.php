<header class="bg-white">
    <div class="md:container mx-8 md:mx-auto">
        <div class="flex flex-row md:items-center flex-wrap md:justify-between gap-8 my-8">
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
                include __DIR__ . "/user.php";
            } else {
                include __DIR__ . "/guest.php";
            }
            ?>
        </div>
    </div>
</header>