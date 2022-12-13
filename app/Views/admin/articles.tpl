{{layout=default}}
{{section=body}}
{{include=admin/breadcrumbs}}
{{include=alert}}
<main class="container mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 m-4">
        {{each $articles as $article}}
        <div class="bg-gray-100 rounded p-4">
            <div class="relative">
                <img src="{{$article->image}}" width="400" height="300" alt="{{$article->title}}"
                    class="w-full rounded">
                <div class="absolute top-0 p-4 flex flex-col gap-4">
                    <a href="/edit/category/{{$article->category_id}}"
                        class="inline-block py-2 px-3 backdrop-blur-lg text-white text-xs font-bold rounded border-2 border-white hover:underline">{{$article->category_title}}</a>
                </div>
                <a href="/edit/article/{{$article->id}}"
                    class="absolute bottom-4 inset-x-4 p-4 text-center backdrop-blur-lg rounded font-semibold border-2 border-white text-white text-xl hover:underline">{{$article->title}}</a>
            </div>
        </div>
        {{/each}}
    </div>
</main>
{{/section}}