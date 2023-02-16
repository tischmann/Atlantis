<li class="bg-white rounded-lg px-4 py-2 whitespace-nowrap flex items-center">
    <i class="handle fas fa-arrows mr-4 hover:text-pink-600 cursor-grab"></i>
    <input type="hidden" name="categories[]" value="{{category_id}}" />
    <div>{{category_title}}
        <a href="/{{env=APP_LOCALE}}/category/edit/{{category_id}}" aria-label="{{lang=edit}}">
            <i class="fas fa-pencil-alt ml-4 hover:text-pink-600"></i>
        </a>
    </div>
</li>