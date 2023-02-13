{{layout=default}}
{{section=body}}
<main class="container mx-auto ">
    <div class="p-4 flex sticky-top bg-white">
        {{include=admin/breadcrumbs}}
    </div>
    <div class="mx-4 mb-4 text-gray-500 font-semibold flex items-center gap-4 uppercase">
        <h1>{{lang=adminpanel_categories}}</h1>
        <div class="h-0.5 w-full rounded-full bg-gray-500"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 font-medium mx-4">
        {{each $locales as $locale => $categories}}
        <form action="/categories/order" method="post"
            class="flex flex-wrap rounded-xl gap-4 bg-sky-600 p-4 shadow-lg text-sky-800">
            {{csrf}}
            <div
                class="flex-grow w-full bg-sky-400 rounded-lg text-white px-4 py-2 whitespace-nowrap uppercase text-center font-bold">
                {{lang=locale_{{$locale}}}}</div>
            <ul id="sortCategories" class="flex gap-4 flex-wrap">
                {{each $categories as $locale => $category}}
                <li class="bg-white rounded-lg px-4 py-2 whitespace-nowrap flex items-center">
                    <i class="handle fas fa-arrows mr-4 hover:text-pink-600"></i>
                    <input type="hidden" name="categories[]" value="{{$category->id}}" />
                    <div>{{$category->title}}
                        <a href="/{{env=APP_LOCALE}}/category/edit/{{$category->id}}">
                            <i class="fas fa-pencil-alt ml-4 hover:text-pink-600"></i>
                        </a>
                    </div>
                </li>
                {{/each}}
            </ul>
            <button type="submit"
                class="hidden bg-pink-400 hover:bg-pink-500 w-full flex-grow rounded-lg text-sm text-white px-4 py-2 whitespace-nowrap uppercase">
                {{lang=save}}</button>
        </form>
        {{/each}}
    </div>
    <script src="/js/sortable.js" nonce="{{nonce}}"></script>
    <script nonce="{{nonce}}">
        new Sortable(document.getElementById('sortCategories'), {
            handle: '.handle',
            animation: 150,
            ghostClass: 'bg-sky-200',
            onEnd: function (event) {
                event.target.closest('form').querySelector('button').classList.remove('hidden');
            },
        });
    </script>
</main>
{{/section}}