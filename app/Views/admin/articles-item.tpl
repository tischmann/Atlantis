<div class="rounded-lg shadow-lg bg-white w-full">
    <a href="/{{env=APP_LOCALE}}/articles/{{article_id}}">
        <div class="relative">
            <img class="rounded-t-lg" src="{{article_image_url}}" alt="" />
            <div class="absolute flex top-0 inset-x-0 gap-4 py-4 flex-wrap text-xs">
                <a href="/category/edit/{{article_category_id}}"
                    class="inline-block px-3 py-2 bg-pink-600 text-white uppercase rounded-lg shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-pink-800 active:shadow-lg transition duration-150 ease-in-out">{{article_category_title}}</a>
            </div>
            <div class="absolute flex bottom-0 inset-x-0 gap-4 p-4 justify-end text-xs text-gray-500">
                <span><i class="fas fa-eye mr-2"></i>{{article_views}}</span>
                <span><i class="fas fa-star mr-2"></i>{{article_rating}}</span>
            </div>
        </div>
    </a>
    <div class="p-6">
        <h5 class="text-gray-900 text-xl font-medium mb-2 truncate">{{article_title}}</h5>
        <p class="text-gray-700 text-base mb-4 truncate">{{article_description}}</p>
        <a href="/{{env=APP_LOCALE}}/edit/article/{{article_id}}"
            class="inline-block px-4 py-3 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=edit}}</a>
    </div>
</div>