<div class="rounded-lg shadow-lg <?= $article->visible ? 'bg-white' : 'bg-gray-200' ?> w-full">
    <a href="/<?= $article->locale ?>/article/<?= $article->id ?>" aria-label="<?= $article->title ?>">
        <div class="relative">
            <img class="rounded-t-lg <?= !$article->visible ? 'grayscale' : '' ?>" src="<?= $article->image_url ?>" alt="<?= $article->title ?>" />
            <div class="absolute flex top-0 inset-x-0 gap-4 p-4 flex-wrap text-xs">
                <span class="block px-3 py-2 bg-white text-sky-600 uppercase rounded-lg shadow-md outline-none ring-0 transition duration-150 ease-in-out font-semibold"><?= $article->category->title ?></span>
            </div>
            <div class="absolute flex bottom-0 inset-x-0 gap-4 p-4 justify-end text-xs text-sky-600">
                <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i class="fas fa-eye mr-2"></i><?= $article->views ?></span>
                <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i class="fas fa-star mr-2"></i><?= $article->rating ?></span>
            </div>
        </div>
    </a>
    <div class="p-6">
        <h5 class="text-gray-900 text-xl font-medium mb-2 truncate"><?= $article->title ?></h5>
        <p class="text-gray-700 text-base mb-3 truncate"><?= $article->short_text ?></p>
        <span class="flex items-center gap-2 text-gray-500 text-xs mb-2">
            <i class="fas fa-circle-plus"></i><?= $article->created_at->format('Y-m-d H:i') ?></span>
        <span class="flex items-center gap-2 text-gray-500 text-xs mb-4">
            <i class="fas fa-rotate"></i><?= $article->updated_at->format('Y-m-d H:i') ?></span>
        <div class="flex items-center justify-between">
            <a href="/{{env=APP_LOCALE}}/edit/article/<?= $article->id ?>" aria-label="{{lang=edit}}" class="inline-block px-4 py-3 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=edit}}</a>
            <?= !$article->visible ? '<i class="fas fa-eye-slash text-xl text-gray-500"></i>' : '' ?>
        </div>
    </div>
</div>