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
<link rel="stylesheet" href="/css/jquery-ui.min.css" media="screen">
<script src="/js/jquery.min.js" nonce="{{nonce}}"></script>
<script src="/js/jquery-ui.min.js" nonce="{{nonce}}"></script>
<style>
    .ui-state-highlight {
        min-height: 2.5rem;
        border-radius: .375rem;
    }
</style>
<main class="md:container mx-8 md:mx-auto">
    <a href="/{{env=APP_LOCALE}}/article/<?= $article->url ?>.html" class="w-full mt-4 flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg mb-8" title="{{lang=show}}">{{lang=show}}</a>
    <form data-article="<?= $article->id ?>">
        <div class="mb-8 grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="col-span-1 xl:col-span-2">
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'input_field',
                        args: [
                            'type' => 'text',
                            'name' => 'title',
                            'label' => get_str('article_title'),
                            'value' => $article->title,
                            'required' => true,
                            'autocomplete' => 'off'
                        ]
                    );
                    ?>
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
                <div class="mb-8">
                    <?php
                    $src = $article->getImage() ? "/images/articles/{$article->id}/image/{$article->getImage()} " : "/images/placeholder.svg";

                    $content = <<<HTML
                    <input type="hidden" id="image" name="image" value="{$article->getImage()}">
                            <img id="article-image" src="{$src}" alt="{$article->title}" width="{$image_width}" height="{$image_height}" class="bg-gray-200 rounded-lg w-full" decoding="async" loading="auto">
                            <div id="pre-upload-image" class="w-full mt-4 flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg" title="{{lang=upload}}">{{lang=upload}}</div>
                            <div id="upload-image-container" class="mt-4 grid grid-cols-3 gap-4 hidden">
                    HTML;

                    $content .= Template::html(
                        template: 'select_field',
                        args: [
                            'name' => 'image_size',
                            'title' => get_str('article_image_size'),
                            'options' => $image_sizes_options
                        ]
                    );

                    $content .= <<<HTML
                        <div id="upload-image" class="col-span-2 w-full flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg font-medium" title="{{lang=upload}}">{{lang=upload}}</div>
                    </div>
                    <div id="delete-image" class="absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                    HTML;

                    Template::echo(
                        template: 'container_field',
                        args: [
                            'label' => get_str('article_image'),
                            'content' => $content,
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'textarea_field',
                        args: [
                            'name' => 'short_text',
                            'label' => get_str('article_short_text'),
                            'value' => $article->short_text,
                            'rows' => 4
                        ]
                    );
                    ?>
                </div>
                <div>
                    <?php

                    $article_text = htmlentities(strval($article->text));

                    Template::echo(
                        template: 'container_field',
                        args: [
                            'label' => get_str('article_text'),
                            'content' => <<<HTML
                            <textarea name="text" id="text" id="text">{$article_text}</textarea>
                            HTML,
                        ]
                    );
                    ?>
                </div>
            </div>
            <div class="relative">
                <div class="mb-8">
                    <?php

                    $gallery_images = implode(";", $article->getGalleryImages());

                    $content = <<<HTML
                    <input type="hidden" name="gallery" id="gallery" value="{$gallery_images}">
                    <div id="pre-upload-gallery" class="mb-4 w-full p-3 rounded-lg bg-sky-600 text-white flex items-center justify-center hover:bg-sky-500 shadow hovr:shadow-lg transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div id="upload-gallery-container" class="my-4 grid grid-cols-3 gap-4 hidden">
                    HTML;

                    $content .= Template::html(
                        template: 'select_field',
                        args: [
                            'name' => 'gallery_image_size',
                            'title' => get_str('article_image_size'),
                            'options' => $image_sizes_options
                        ]
                    );

                    $content .= <<<HTML
                        <div id="upload-gallery" class="col-span-2 w-full flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg font-medium" title="{{lang=upload}}">{{lang=upload}}</div>
                    </div>
                    <ul class="gallery-container grid grid-cols-2 gap-4">
                    HTML;

                    foreach ($article->getGalleryImages() as $src) {
                        $src = $article->id
                            ? "/images/articles/{$article->id}/gallery/thumb_{$src}"
                            : "/images/articles/temp/thumb_{$src}";

                        $content .= <<<HTML
                        <li class="text-sm select-none relative">
                            <img src="{$src}" width="320" height="180" alt="..." decoding="async" loading="lazy" class="block w-full rounded-md">
                            <button type="button" class="block outline-none absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}" data-delete>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </li>
                        HTML;
                    }

                    $content .= <<<HTML
                    </ul>
                    HTML;

                    Template::echo(
                        template: 'container_field',
                        args: [
                            'label' => get_str('article_gallery'),
                            'content' => $content,
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php

                    $article_videos = implode(";", $article->getVideos());

                    $content = <<<HTML
                    <input type="hidden" id="title" name="videos" value="{$article_videos}">
                    <div id="upload-video" class="mb-4 w-full p-3 rounded-lg bg-sky-600 text-white flex items-center justify-center hover:bg-sky-500 transition cursor-pointer shadow hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <ul class="videos-container grid grid-cols-2 gap-4">
                    HTML;

                    foreach ($article->getVideos() as $video) {
                        $content .= <<<HTML
                        <li class="text-sm select-none relative">
                            <video src="/uploads/articles/{$article->id}/video/{$video}" class="block w-full rounded-md" controls></video>
                            <button type="button" class="block outline-none absolute top-0 right-0 p-2 text-white bg-red-600 rounded-md hover:bg-red-500 cursor-pointer transition drop-shadow" title="{{lang=delete}}" data-delete>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </li>
                        HTML;
                    }

                    $content .= <<<HTML
                    </ul>
                    HTML;

                    Template::echo(
                        template: 'container_field',
                        args: [
                            'label' => get_str('article_video'),
                            'content' => $content,
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php

                    $article_attachements = implode(";", $article->getAttachements());

                    $content = <<<HTML
                    <input type="hidden" id="attachement" name="attachements" value="{$article_attachements}">
                    <div id="upload-attachement" class="mb-4  w-full p-3 rounded-lg bg-sky-600 text-white flex items-center justify-center hover:bg-sky-500 transition cursor-pointer shadow hover:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <ul class="attachements-container grid grid-cols-1 gap-4" aria-label="attachement" name="attachement">
                    HTML;

                    foreach ($article->getAttachements() as $attachement) {
                        $href = $article->id
                            ? "/uploads/articles/{$article->id}/attachements/{$attachement}"
                            : "/uploads/articles/temp/{$attachement}";

                        $content .= <<<HTML
                       <li class="flex flex-nowrap gap-2 items-center justify-between text-gray-800 dark:text-white w-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="handle w-6 h-6 cursor-grab hover:text-sky-500 ml-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                            </svg>
                            <a href="{$href}" class="text-ellipsis hover:underline overflow-hidden whitespace-nowrap grow pr-3 py-2" target="_blank" title="{{lang=delete}}">{$attachement}</a>
                            <button type="button" class="block outline-none cursor-pointer text-white hover:bg-red-500 transition bg-red-600 rounded-lg p-3" title="{{lang=delete}}" data-delete>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </li>
                       HTML;
                    }

                    $content .= <<<HTML
                    </ul>
                    HTML;

                    Template::echo(
                        template: 'container_field',
                        args: [
                            'label' => get_str('article_attachement'),
                            'content' => $content,
                        ]
                    );

                    ?>
                </div>
                <div class="mb-8 relative">
                    <label for="tags" class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 px-1 rounded-md">{{lang=article_tags}}</label>
                    <textarea class="block flex-grow min-h-48 py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 rounded-lg w-full bg-white dark:bg-gray-800 text-gray-800 dark:text-white focus:border-sky-600 transition rounded-t-lg rounded-b-none" aria-label="tags" id="tags" name="tags"><?= implode(", ", $article->tags) ?></textarea>
                    <div class="flex flex-nowrap">
                        <input class="block w-16 py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 border-t-0 rounded-bl-lg focus:border-sky-600 text-gray-800 dark:text-white bg-white dark:bg-gray-800 font-medium transition" aria-label="tags-limit" id="tags-limit" name="tags-limit" type="number" min="1" max="20" step="1" value="5">
                        <div id="generate-tags" class="grow flex items-center justify-center border-2 border-gray-200 dark:border-gray-600 border-t-0 border-l-0 px-3 py-2 w-full bg-sky-600 hover:bg-sky-500 text-white cursor-pointer font-medium transition shadow hover:shadow-lg rounded-br-lg" title="{{lang=article_generate_tags}}">{{lang=article_generate_tags}}</div>
                    </div>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'input_field',
                        args: [
                            'type' => 'datetime-local',
                            'name' => 'created_at',
                            'label' => get_str('article_created_at'),
                            'value' => $article->created_at?->format("Y-m-d H:i"),
                            'required' => true,
                            'autocomplete' => 'off'
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'radio_field',
                        args: [
                            'name' => 'visible',
                            'label' => get_str('article_visible'),
                            'value' => intval($article->visible),
                            'options' => [
                                ['value' => 0, 'label' => get_str('article_visible_invisible')],
                                ['value' => 1, 'label' => get_str('article_visible_visible')]
                            ]
                        ]
                    );
                    ?>
                </div>
                <div class="mb-8">
                    <?php
                    Template::echo(
                        template: 'radio_field',
                        args: [
                            'name' => 'fixed',
                            'label' => get_str('article_fixed'),
                            'value' => intval($article->fixed),
                            'options' => [
                                ['value' => 0, 'label' => get_str('article_fixed_off')],
                                ['value' => 1, 'label' => get_str('article_fixed_on')]
                            ]
                        ]
                    );
                    ?>
                </div>
                <?php
                if ($user->canModerate()) {
                    echo <<<HTML
                    <div class="mb-8">
                    HTML;

                    Template::echo(
                        template: 'radio_field',
                        args: [
                            'name' => 'moderated',
                            'label' => get_str('article_moderated'),
                            'value' => intval($article->moderated),
                            'options' => [
                                ['value' => 0, 'label' => get_str('article_moderated_no')],
                                ['value' => 1, 'label' => get_str('article_moderated_yes')]
                            ]
                        ]
                    );

                    echo <<<HTML
                    </div>
                    HTML;
                }
                ?>
                <div class="flex flex-col gap-4">
                    <?php
                    if ($article->exists()) {
                        echo <<<HTML
                            <button id="delete-article" class="flex items-center justify-center px-3 py-2 bg-red-600 hover:bg-red-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=delete}}"  data-confirm="{{lang=confirm_delete}}">{{lang=delete}}</button>
                            <button id="save-article" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=save}}">{{lang=save}}</button>
                            HTML;
                    } else {
                        echo <<<HTML
                            <button id="add-article" class="flex items-center justify-center px-3 py-2 bg-sky-600 hover:bg-sky-500 text-white cursor-pointer transition shadow hover:shadow-lg rounded-lg w-full font-medium" type="button" title="{{lang=add}}">{{lang=add}}</button>
                            HTML;
                    }
                    ?>
                </div>
            </div>

        </div>
    </form>
</main>
<script src="/tinymce/tinymce.min.js" nonce="{{nonce}}"></script>
<script src="/js/article.editor.min.js" nonce="{{nonce}}" type="module"></script>