<main class="container mx-auto ">
    <div class="p-4 flex sticky-top bg-white">
        {{breadcrumbs}}
    </div>
    <div class="mx-4 mb-4 text-gray-500 font-semibold flex items-center gap-4 uppercase">
        <h1>{{lang=adminpanel_categories}}</h1>
        <div class="h-0.5 w-full rounded-full bg-gray-500"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 font-medium mx-4">
        {{items}}
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