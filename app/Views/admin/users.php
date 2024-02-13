<?php

use Tischmann\Atlantis\Template;

?>
<main class="md:container md:mx-auto">
    <?php

    include __DIR__ . "/../sort.php";

    ?>
    <div class="flex flex-wrap gap-4">
        <?php

        foreach ($users as $user) {
            Template::echo('admin/user-item', ['user' => $user]);
        }

        ?>
    </div>
    <?= Template::html('admin/add-button', ['href' => '/{{env=APP_LOCALE}}/add/user']) ?>
</main>