<ul class="grid grid-cols-1 gap-2" data-categories>
    <?php

    use App\Models\{Category};

    use Tischmann\Atlantis\{Template};

    foreach ($categories as $category) {
        assert($category instanceof Category);

        Template::echo(
            template: 'category_list_item',
            args: [
                'category' => $category
            ]
        );
    }

    ?>
</ul>