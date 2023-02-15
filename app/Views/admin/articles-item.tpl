<div class="rounded-lg shadow-lg bg-white max-w-sm">
    <a href="/{{env=APP_LOCALE}}/articles/{{article_id}}">
        <img class="rounded-t-lg" src="{{article_image_url}}" alt="" />
    </a>
    <div class="p-6">
        <h5 class="text-gray-900 text-xl font-medium mb-2 truncate">{{article_title}}</h5>
        <p class="text-gray-700 text-base mb-4 truncate">{{article_description}}</p>
        <a href="/{{env=APP_LOCALE}}/edit/article/{{article_id}}"
            class=" inline-block px-4 py-3 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=edit}}</a>
    </div>
</div>