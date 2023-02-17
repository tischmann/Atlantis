<li class="bg-white rounded-lg px-4 py-2 whitespace-nowrap flex items-center" data-id="<?= $category->id ?>">
    <i class="handle fas fa-arrows mr-4 hover:text-pink-600 cursor-grab"></i>
    <div><?= $category->title ?>
        <a href="/{{env=APP_LOCALE}}/category/edit/<?= $category->id ?>" aria-label="{{lang=edit}}">
            <i class="fas fa-pencil-alt ml-4 hover:text-pink-600"></i>
        </a>
    </div>
</li>