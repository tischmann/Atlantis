<?php

use App\Models\{Article};

use Tischmann\Atlantis\{App, DateTime, Locale, Template};

assert($article instanceof Article);

$user = App::getCurrentUser();

if (!$article->exists()) {
    $article->created_at = new DateTime();
    $article->locale = getenv('APP_LOCALE');
    $article->category_id = 0;
}

$category = $article->getCategory();

list($image_width, $image_height) = $article->getImageSizes();

?>
<main class="md:container mx-8 md:mx-auto">
    <form data-article="<?= $article->id ?>">
        <div class="mb-8 grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="col-span-1 xl:col-span-2">
                <div class="mb-8 relative">
                    <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_title}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="title" id="title" name="title" value="<?= $article->title ?>" required>
                </div>
                <div class="mb-8">
                    <select id="locale-select" name="locale" title="{{lang=locale}}">
                        <?php
                        foreach (Locale::available() as $value) {
                            $selected = $article->locale === $value
                                ? 'selected'
                                : '';
                            echo <<<HTML
                            <option value="{$value}" {$selected} title="" data-level="0">{{lang=locale_{$value}}}</option>
                            HTML;
                        }

                        ?>
                    </select>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'select_field',
                        args: [
                            'name' => 'category_id',
                            'title' => get_str('article_category'),
                            'options' => $category_options
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8 relative">
                    <div class="rounded-lg border-2 border-gray-200 select-none">
                        <div class="rounded-lg border-[16px] border-white relative">
                            <input type="hidden" id="image" name="image" value="<?= $article->getImage() ?>">
                            <img id="article-image" src="<?= $article->getImage() ? "/images/articles/{$article->id}/image/{$article->getImage()} " : "/images/placeholder.svg" ?>" alt="<?= $article->title ?>" width="<?= $image_width ?>" height="<?= $image_height ?>" class="bg-gray-200 rounded-lg w-full" decoding="async" loading="auto">
                            <div id="pre-upload-image" class="w-full mt-4 flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg" title="{{lang=upload}}">{{lang=upload}}</div>
                            <div id="upload-image-container" class="mt-8 grid grid-cols-3 gap-4 hidden">
                                <?php
                                Template::echo(
                                    template: 'select_field',
                                    args: [
                                        'name' => 'image_size',
                                        'title' => get_str('article_image_size'),
                                        'options' => $image_sizes_options
                                    ]
                                );
                                ?>
                                <div id="upload-image" class="col-span-2 w-full flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg" title="{{lang=upload}}">{{lang=upload}}</div>
                            </div>
                            <div id="delete-image" class="absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <label for="image" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_image}}</label>
                </div>
                <div class="mb-8 flex flex-col relative">
                    <label for="short_text" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_short_text}}</label>
                    <div class="flex-grow w-full border-2 border-gray-200 rounded-lg p-4">
                        <textarea class="w-full min-h-48 border-none outline-none block" id="short_text" name="short_text"><?= $article->short_text ?></textarea>
                    </div>
                </div>
                <div class="flex flex-col relative">
                    <label for="text" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_text}}</label>
                    <div class="flex-grow w-full min-h-96 outline-none border-2 border-gray-200 rounded-lg p-4 focus:border-sky-600 transition">
                        <input type="hidden" name="text" id="text" value="<?= htmlentities(strval($article->text)) ?>">
                        <div id="quill-editor" class="max-h-[85vh] overflow-y-auto"><?= $article->text ?></div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="mb-8 relative">
                    <label for="gallery" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_gallery}}</label>
                    <input type="hidden" name="gallery" id="gallery" value="<?= implode(";", $article->getGalleryImages()) ?>">
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <div id="pre-upload-gallery" class="mb-4 w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <div id="upload-gallery-container" class="my-4 grid grid-cols-3 gap-4 hidden">
                                <?php
                                Template::echo(
                                    template: 'select_field',
                                    args: [
                                        'name' => 'gallery_image_size',
                                        'title' => get_str('article_image_size'),
                                        'options' => $image_sizes_options
                                    ]
                                );
                                ?>
                                <div id="upload-gallery" class="col-span-2 w-full flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg" title="{{lang=upload}}">{{lang=upload}}</div>
                            </div>
                            <ul class="gallery-container grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-2 gap-4">
                                <?php
                                foreach ($article->getGalleryImages() as $src) {
                                    Template::echo(
                                        template: 'article_gallery_item',
                                        args: [
                                            'src' => $article->id
                                                ? "/images/articles/{$article->id}/gallery/thumb_{$src}"
                                                : "/images/articles/temp/thumb_{$src}"
                                        ]
                                    );
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <label for="title" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_video}}</label>
                    <input type="hidden" id="title" name="videos" value="<?= implode(";", $article->getVideos()) ?>">
                    <div class="rounded-lg border-2 border-gray-200">
                        <div class="rounded-lg border-[16px] border-white">
                            <div id="upload-video" class="mb-4 w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <ul class="videos-container grid grid-cols-1 gap-4">
                                <?php
                                foreach ($article->getVideos() as $video) {
                                    require "article_video_item.php";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <label for="attachement" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_attachement}}</label>
                    <input type="hidden" id="attachement" name="attachements" value="<?= implode(";", $article->getAttachements()) ?>">
                    <div class="border-2 border-gray-200 rounded-lg p-4 transition">
                        <div id="upload-attachement" class="mb-4  w-full p-3 rounded-lg bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <ul class="attachements-container flex flex-col gap-4" aria-label="attachement" name="attachement">
                            <?php

                            foreach ($article->getAttachements() as $attachement) {
                                Template::echo(
                                    template: 'article_attachement_item',
                                    args: [
                                        'file' => $attachement,
                                        'href' => $article->id
                                            ? "/uploads/articles/{$article->id}/attachements/{$attachement}"
                                            : "/uploads/articles/temp/{$attachement}"
                                    ]
                                );
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <label for="tags" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_tags}}</label>
                    <textarea class="block flex-grow w-full min-h-48 outline-none border-2 border-gray-200 rounded-t-lg p-4 focus:border-sky-600 transition" aria-label="tags" id="tags" name="tags"><?= implode(", ", $article->tags) ?></textarea>
                    <div class="flex flex-nowrap">
                        <input class="block w-16 py-2 px-3 outline-none border-2 border-gray-200 border-t-0 rounded-bl-lg focus:border-sky-600 transition" aria-label="tags-limit" id="tags-limit" name="tags-limit" type="number" min="1" max="20" step="1" value="5">
                        <div id="generate-tags" class="grow flex items-center justify-center border-2 border-gray-200 border-t-0 border-l-0 px-3 py-2 w-full bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-br-lg" title="{{lang=article_generate_tags}}">{{lang=article_generate_tags}}</div>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <label for="created_at" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_created_at}}</label>
                    <input class="py-2 px-3 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" aria-label="created_at" id="created_at" name="created_at" type="datetime-local" value="<?= $article->created_at?->format("Y-m-d H:i") ?>">
                </div>
                <div class="mb-8 relative">
                    <label for="visible_active" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_visible}}</label>
                    <div class="grid w-full grid-cols-1 md:grid-cols-2 gap-4 bg-white border-2 border-gray-200 rounded-lg p-4">
                        <div>
                            <input type="radio" name="visible" id="visible_inactive" value="0" class="peer hidden" <?= !$article->visible ? 'checked' : '' ?> />
                            <label for="visible_inactive" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-red-600 peer-checked:text-white">{{lang=article_visible_invisible}}</label>
                        </div>
                        <div>
                            <input type="radio" name="visible" id="visible_active" value="1" class="peer hidden" <?= $article->visible ? 'checked' : '' ?> />
                            <label for="visible_active" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-green-600 peer-checked:text-white">{{lang=article_visible_visible}}</label>
                        </div>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <label for="fixed_on" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_fixed}}</label>
                    <div class="grid w-full grid-cols-1 md:grid-cols-2 gap-4 bg-white border-2 border-gray-200 rounded-lg p-4">
                        <div>
                            <input type="radio" name="fixed" id="fixed_off" value="0" class="peer hidden" <?= !$article->fixed ? 'checked' : '' ?> />
                            <label for="fixed_off" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-red-600 peer-checked:text-white">{{lang=article_fixed_off}}</label>
                        </div>
                        <div>
                            <input type="radio" name="fixed" id="fixed_on" value="1" class="peer hidden" <?= $article->fixed ? 'checked' : '' ?> />
                            <label for="fixed_on" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-green-600 peer-checked:text-white">{{lang=article_fixed_on}}</label>
                        </div>
                    </div>
                </div>
                <?php
                if ($user->canModerate()) {
                    $is_moderated = $article->moderated ? 'checked' : '';

                    $is_not_moderated = $article->moderated ? '' : 'checked';

                    echo <<<HTML
                    <div class="mb-8 relative">
                        <label for="moderated_yes" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{lang=article_moderated}}</label>
                        <div class="grid w-full grid-cols-1 md:grid-cols-2 gap-4 bg-white border-2 border-gray-200 rounded-lg p-4">
                            <div>
                                <input type="radio" name="moderated" id="moderated_no" value="0" class="peer hidden" {$is_not_moderated} />
                                <label for="moderated_no" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-red-600 peer-checked:text-white">{{lang=article_moderated_no}}</label>
                            </div>
                            <div>
                                <input type="radio" name="moderated" id="moderated_yes" value="1" class="peer hidden" {$is_moderated} />
                                <label for="moderated_yes" class="block cursor-pointer select-none rounded-md p-2 text-center bg-gray-200 peer-checked:bg-green-600 peer-checked:text-white">{{lang=article_moderated_yes}}</label>
                            </div>
                        </div>
                    </div>
                    HTML;
                }
                ?>
                <div class="flex flex-col gap-4">
                    <?php
                    if ($article->exists()) {
                        echo <<<HTML
                            <button id="delete-article" class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=delete}}"  data-confirm="{{lang=confirm_delete}}">{{lang=delete}}</button>
                            <button id="save-article" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=save}}">{{lang=save}}</button>
                            HTML;
                    } else {
                        echo <<<HTML
                            <button id="add-article" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full" type="button" title="{{lang=add}}">{{lang=add}}</button>
                            HTML;
                    }
                    ?>
                </div>
            </div>

        </div>
    </form>
</main>
<script src="/js/quill.min.js" nonce="{{nonce}}"></script>
<script src="/js/article.editor.min.js" nonce="{{nonce}}" type="module"></script>