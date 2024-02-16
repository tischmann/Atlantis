<main class="md:container mx-8 md:mx-auto h-[calc(100vh-104px)] flex flex-col items-center justify-center">
    <h1 class="text-center text-3xl font-bold mt-8">500</h1>
    <p class="text-center mt-4">{{lang=error}}</p>
    <?php

    if (isset($exception)) {
        echo <<<HTML
        <p class="text-center mt-4">
            <div class="bg-gray-100 p-4 rounded-xl">
                {$exception->getMessage()}
            </div>
        </p>
        HTML;
    }

    ?>
    <div class="flex justify-center mt-8">
        <a href="/" class="bg-gray-500 hover:bg-sky-800 text-white font-bold py-2 px-4 rounded-xl">{{lang=back}}</a>
    </div>
</main>