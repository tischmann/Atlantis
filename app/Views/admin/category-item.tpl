<span class="mt-4 mb-0 md:mt-4 md:mb-4 mx-4">{{$category->title}}</span>
<div class="flex gap-x-2 flex-wrap py-2 mx-2 text-sky-800 justify-end">
    <form method="post" action="/{{env=APP_LOCALE}}/category/indent/{{$category->id}}">
        <button type="submit" class="bg-sky-200 rounded-lg h-[36px] w-[36px] 
            flex items-center justify-center hover:text-pink-600">
            <i class="fas fa-indent"></i>
        </button>
    </form>
    <form method="post" action="/{{env=APP_LOCALE}}/category/outdent/{{$category->id}}">
        <button type="submit" class="bg-sky-200 rounded-lg h-[36px] w-[36px] 
                flex items-center justify-center hover:text-pink-600">
            <i class="fas fa-outdent"></i>
        </button>
    </form>
    <form method="post" action="/{{env=APP_LOCALE}}/category/order/up/{{$category->id}}">
        <button type="submit" class="bg-sky-200 rounded-lg h-[36px] w-[36px] 
        flex items-center justify-center hover:text-pink-600">
            <i class="fas fa-arrow-up"></i>
        </button>
    </form>
    <form method="post" action="/{{env=APP_LOCALE}}/category/order/down/{{$category->id}}">
        <button type="submit" class="bg-sky-200 rounded-lg h-[36px] w-[36px] 
        flex items-center justify-center hover:text-pink-600">
            <i class="fas fa-arrow-down"></i>
        </button>
    </form>
    <a href="/{{env=APP_LOCALE}}/category/edit/{{$category->id}}" class="bg-sky-200 rounded-lg h-[36px] w-[36px]
        flex items-center justify-center hover:text-pink-600">
        <i class="fas fa-pencil-alt"></i>
    </a>
    <a href="/{{env=APP_LOCALE}}/category/delete/{{$category->id}}" class="bg-sky-200 rounded-lg h-[36px] w-[36px]
        flex items-center justify-center hover:text-pink-600">
        <i class="fas fa-trash-alt"></i>
    </a>
</div>