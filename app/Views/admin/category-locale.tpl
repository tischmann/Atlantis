<form action="/admin/categories/order" method="post"
    class="flex flex-wrap rounded-xl gap-4 bg-sky-600 p-4 shadow-lg text-sky-800">
    {{csrf}}
    <div
        class="flex-grow w-full bg-sky-400 rounded-lg text-white px-4 py-2 whitespace-nowrap uppercase text-center font-bold">
        {{locale_title}}</div>
    <ul id="sortCategories" class="flex gap-4 flex-wrap">
        {{items}}
    </ul>
    <button type="submit"
        class="hidden bg-pink-400 hover:bg-pink-500 w-full flex-grow rounded-lg text-sm text-white px-4 py-2 whitespace-nowrap uppercase">
        {{lang=save}}</button>
</form>