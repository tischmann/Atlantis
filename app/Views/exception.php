<main class="md:container mx-4 md:mx-auto">
    <div class="flex flex-col gap-8 items-center">
        <div class="text-2xl font-semibold m-4">{{message}}</div>
        <?php

        if (isset($trace)) {
            echo <<<HTML
            <div class="m-4">{$trace}</div>
            HTML;
        }

        ?>
    </div>
</main>