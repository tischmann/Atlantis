<?php

use Tischmann\Atlantis\Template;

include __DIR__ . "/../header.php";

?>
<main class="md:container md:mx-auto">
    <div class="m-4">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <div class="flex items-center gap-4 m-4 justify-end">
        <?php include __DIR__ . "/../sort.php" ?>
    </div>
    <?php

    $lazyload = Template::html(
        'lazyload',
        [
            'pagination' => $pagination,
            'url' => "/fetch/admin/users"
        ]
    );

    ?>
    <div class="flex flex-wrap gap-4 m-4" <?= $lazyload ?>>
        <?php

        foreach ($users as $user) {
            Template::echo('admin/user-item', ['user' => $user]);
        }

        ?>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/user']) ?>
</main>