<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto px-4">
    <div class="flex items-center gap-4 px-4 justify-end">
        <?php include __DIR__ . "/../sort.php" ?>
    </div>
    <div class="intersection-loader-container flex flex-wrap gap-4 my-4">
        <?php

        use Tischmann\Atlantis\Template;

        foreach ($users as $user) {
            Template::echo('admin/user-item', ['user' => $user]);
        }

        Template::echo(
            'intersection-loader-target',
            [
                'pagination' => $pagination,
                'url' => "/fetch/admin/users"
            ]
        );
        ?>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/user']) ?>
</main>