<?php

use App\Models\{Article, Category, User};

use Tischmann\Atlantis\{Locale};

include __DIR__ . "/../header.php"

?>
<main class="md:container md:mx-auto">
    <div class="m-4">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <div class="m-4">
        <div class="flex flex-wrap gap-4">
            <div class="w-1/2 flex-grow h-[30vh] bg-gray-600 rounded-lg"></div>
            <div class="w-1/3 flex-grow h-[30vh] bg-gray-600 rounded-lg"></div>
            <div class="w-1/4 flex-grow h-[30vh] bg-gray-600 rounded-lg"></div>
            <div class="w-1/2 flex-grow h-[30vh] bg-gray-600 rounded-lg"></div>
        </div>
    </div>
</main>