<div class="block w-full rounded-lg bg-white shadow-lg dark:bg-neutral-700">
    <a href="/<?= $article->locale ?>/article/<?= $article->id ?>" aria-label="<?= $article->title ?>">
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
    </a>
    <div class="p-6">
        <h5 class="mb-3 text-xl font-medium leading-tight text-neutral-800 dark:text-neutral-50 truncate"><?= $article->title ?></h5>
        <span class="flex items-center gap-2 text-neutral-400 text-xs mb-2">
            <i class="fas fa-circle-plus"></i><?= $article->created_at->format('Y-m-d H:i') ?></span>
        <span class="flex items-center gap-2 text-neutral-400 text-xs mb-4">
            <i class="fas fa-rotate"></i><?= $article->updated_at->format('Y-m-d H:i') ?></span>
        <a href="/{{env=APP_LOCALE}}/edit/article/<?= $article->id ?>" aria-label="{{lang=edit}}" class="inline-block rounded bg-primary px-6 pt-2.5 pb-2 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-primary-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-primary-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-primary-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]" data-te-ripple-init data-te-ripple-color="light">{{lang=edit}}</a>
    </div>
</div>