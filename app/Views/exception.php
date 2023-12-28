<?php include __DIR__ . "/header.php" ?>
<main>
    <div class="md:container mx-4 md:mx-auto">
        <div class="flex flex-col gap-8 items-center mt-24">
            <div class="text-md font-medium">{{message}}</div>
            <?php

            if (isset($trace)) {
                echo <<<HTML
                <div class="text-md font-medium">{$trace}</div>
                HTML;
            }

            ?>
        </div>
    </div>
</main>