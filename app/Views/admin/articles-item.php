<div class="block w-full rounded-lg bg-sky-800 text-white shadow-lg">
    <div class="relative">
        <img class="rounded-t-lg <?= !$article->visible ? 'grayscale' : '' ?>" src="<?= $article->image_url ?>" alt="<?= $article->title ?>" />
        <div class="absolute flex top-0 inset-x-0 gap-4 p-4 flex-wrap text-xs">
            <span class="block px-3 py-2 bg-white text-primary uppercase rounded-lg shadow-md outline-none ring-0 transition duration-150 ease-in-out font-semibold"><?= $article->category->title ?></span>
        </div>
        <div class="absolute flex bottom-0 inset-x-0 gap-4 p-4 justify-end text-xs text-primary">
            <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i class="fas fa-eye mr-2"></i><?= $article->views ?></span>
            <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i class="fas fa-star mr-2"></i><?= $article->rating ?></span>
        </div>
    </div>
    <div class="p-6">
        <h5 class="mb-3 text-xl font-medium leading-tight truncate"><?= $article->title ?></h5>
        <span class="flex items-center gap-2 text-xs mb-2">
            <i class="fas fa-circle-plus"></i><?= $article->created_at->format('Y-m-d H:i') ?></span>
        <span class="flex items-center gap-2 text-xs mb-4">
            <i class="fas fa-rotate"></i><?= $article->updated_at->format('Y-m-d H:i') ?></span>
        <a href="/{{env=APP_LOCALE}}/article/<?= $article->id ?>" aria-label="{{lang=show}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out" data-te-ripple-init data-te-ripple-color="light">{{lang=show}}</a>
        <a href="/{{env=APP_LOCALE}}/edit/article/<?= $article->id ?>" aria-label="{{lang=edit}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out" data-te-ripple-init data-te-ripple-color="light">{{lang=edit}}</a>
    </div>
</div>