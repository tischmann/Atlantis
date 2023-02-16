<div class="rounded-lg shadow-lg bg-white w-full">
    <a href="/{{env=APP_LOCALE}}/article/{{article_id}}" aria-label="{{article_title}}">
        <div class="relative">
            <img class="rounded-t-lg" src="{{article_image_url}}" alt="{{article_title}}" />
            <div class="absolute flex top-0 inset-x-0 gap-4 py-4 flex-wrap text-xs">
                <a href="/category/edit/{{article_category_id}}" aria-label="{{article_category_title}}"
                    class="inline-block px-3 py-2 bg-white text-sky-600 uppercase rounded-lg shadow-md outline-none ring-0 transition duration-150 ease-in-out font-semibold">{{article_category_title}}</a>
            </div>
            <div class="absolute flex bottom-0 inset-x-0 gap-4 p-4 justify-end text-xs text-sky-600">
                <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i
                        class="fas fa-eye mr-2"></i>{{article_views}}</span>
                <span class="px-3 py-2 rounded-lg bg-white shadow-md font-semibold"><i
                        class="fas fa-star mr-2"></i>{{article_rating}}</span>
            </div>
        </div>
    </a>
    <div class="p-6">
        <h5 class="text-gray-900 text-xl font-medium mb-2 truncate">{{article_title}}</h5>
        <p class="text-gray-700 text-base mb-4 truncate">{{article_description}}</p>
    </div>
</div>