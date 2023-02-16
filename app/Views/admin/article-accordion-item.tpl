<div class="accordion-item rounded-none bg-white border border-gray-200">
    <h2 class="accordion-header mb-0" id="heading-{{category_id}}">
        <button class="accordion-button collapsed relative flex items-center
                     w-full py-4 px-5 text-base text-gray-800 text-left 
                     bg-white border-0 rounded-none transition
                      focus:outline-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-{{category_id}}" aria-expanded="false"
            aria-controls="collapse-{{category_id}}">{{category_title}}</button>
    </h2>
    <div id="collapse-{{category_id}}" class="accordion-collapse border-0 collapse"
        aria-labelledby="heading-{{category_id}}" data-bs-parent="#accordionArticles">
        <div class="accordion-body py-4 px-5">
            <div
                class="intersection-loader-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                {{items}}
                <div class="intersection-loader-target" data-url="/fetch/admin/articles/{{category_id}}"
                    data-page="{{pagination_page}}" data-limit="{{pagination_limit}}" data-search="{{search_value}}"
                    data-sort="{{sort_type}}" data-order="{{sort_order}}"></div>
            </div>
        </div>
    </div>
</div>