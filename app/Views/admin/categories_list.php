<?php

use App\Models\{Category};

use Tischmann\Atlantis\{Template};
?>
<link rel="stylesheet" href="/jquery-ui.min.css" media="screen">
<style>
    .ui-state-highlight {
        min-height: 3.5rem;
        border-radius: .5rem;
    }
</style>
<script src="/jquery.min.js" nonce="{{nonce}}"></script>
<script src="/jquery-ui.min.js" nonce="{{nonce}}"></script>
<main class="md:container mx-4 my-4 md:mx-auto mb-4 select-none">
    <div class="mb-4 flex flex-col sm:flex-row gap-4">
        <?php
        Template::echo(
            template: 'fields/select_field',
            args: [
                'name' => 'locale',
                'title' => get_str('article_locale'),
                'options' => $locale_options
            ]
        );
        ?>
    </div>
    <a href="/{{env=APP_LOCALE}}/new/category" title="{{lang=category_new}}" class="mb-4 flex items-center justify-center p-3 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow hover:shadow-lg transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </a>
    <?php include 'categories_list_list.php' ?>
</main>
<script nonce="{{nonce}}" type="module">
    import Select from 'Select'

    ['locale'].forEach((name) => {
        new Select(document.querySelector(`select[name="${name}"]`), {
            onchange: (value, changed) => {
                if (!changed) return
                const url = new URL(window.location.href)
                url.searchParams.set(name, value)
                window.location.href = url.toString()
            }
        })
    })
</script>