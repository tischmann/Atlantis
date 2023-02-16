<main class="container mx-auto">
    <div class="m-4">
        <a class="flex items-center" aria-label="{{env=APP_TITLE}}">
            <img src="/android-chrome-192x192.png" width="32px" height="32px" alt="{{env=APP_TITLE}}" />
            <div class="uppercase text-[34px] text-gray-400 leading-8 font-bold">TLANTIS</div>
        </a>
    </div>
    <div class="m-4 flex sticky-top bg-white">
        {{breadcrumbs}}
    </div>
    <div class="m-4">
        <div class="text-3xl font-bold flex items-center gap-4">{{article_title}}{{edit}}</div>
        <div class="text-gray-400 flex items-center gap-4">
            <span>{{article_updated_at}}</span>
            <div class="text-sm"><i class="fas fa-eye mr-2"></i>{{article_views}}</div>
            <div class="text-sm"><i class="fas fa-star mr-2"></i>{{article_rating}}</div>
        </div>
        <div class="mt-4">
            <img class="lazy w-full md:max-w-lg md:float-left mb-4 md:mr-8 md:mb-8 rounded-xl shadow-md"
                data-src="{{article_image_url}}" src="/images/placeholder.svg" width="400" height="300"
                alt="{{article_title}}">
            {{article_full_text}}
        </div>
    </div>
</main>