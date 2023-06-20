<?php

use Tischmann\Atlantis\Template;

include __DIR__ . "/../header.php";

?>
<main class="md:container md:mx-auto">
    <?php

    include __DIR__ . "/../sort.php";

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