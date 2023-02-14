<main class="container mx-auto ">
    <div class="p-4 flex sticky-top bg-white">
        {{breadcrumbs}}
    </div>
    <div class="mx-4 mb-4 text-gray-500 font-semibold flex items-center gap-4 uppercase">
        <h1>{{lang=articles}}</h1>
        <div class="h-0.5 w-full rounded-full bg-gray-500"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mx-4">
        {{items}}
    </div>
    <a href="/{{env=APP_LOCALE}}/article/add" class="h-16 w-16 fixed flex 
    items-center justify-center bottom-4 right-4 text-white text-xl
    rounded-full bg-pink-600 hover:bg-pink-700 hover:shadow-lg 
    active:bg-pink-700 focus:bg-pink-700 transition-all ease-in-out"><i class="fas fa-plus"></i></a>
    <div class="h-24"></div>
</main>