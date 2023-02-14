<main class="container mx-auto">
    <div class="p-4 flex sticky-top bg-white">
        {{breadcrumbs}}
    </div>
    <form method="post" class="mx-4">
        {{csrf}}
        <div class="mb-4">
            <label for="categoryLocale"
                class="form-label inline-block mb-1 text-gray-500">{{lang=category_locale}}</label>
            <select class="form-select appearance-none block w-full px-3 py-1.5
                              text-base font-normal text-gray-700 bg-white bg-clip-padding 
                              bg-no-repeat border border-solid border-gray-300 rounded
                              transition ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                              focus:border-blue-600 focus:outline-none" id="categoryLocale" name="locale"
                aria-label="{{lang=category_locale}}">
                {{locales_options}}
            </select>
        </div>
        <div class="mb-4">
            <label for="categoryTitle"
                class="form-label inline-block mb-1 text-gray-500">{{lang=category_title}}</label>
            <input type="text" class="form-control block w-full px-3 py-1.5
                text-base font-normal text-gray-700 bg-white bg-clip-padding
                border border-solid border-gray-300 rounded transition
                ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                focus:border-blue-600 focus:outline-none" id="categoryTitle" name="title"
                placeholder="{{lang=category_title}}" value="" required />
        </div>
        <div class="mb-4">
            <label for="categoryParent"
                class="form-label inline-block mb-1 text-gray-500">{{lang=category_parent}}</label>
            <select class="form-select appearance-none block w-full px-3 py-1.5
                      text-base font-normal text-gray-700 bg-white bg-clip-padding 
                      bg-no-repeat border border-solid border-gray-300 rounded
                      transition ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                      focus:border-blue-600 focus:outline-none" id="categoryParent" name="parent_id"
                aria-label="{{lang=category_parent}}">
                {{parents_options}}
            </select>
        </div>
        <div class="mb-4">
            <label for="categorySlug" class="form-label inline-block mb-1 text-gray-500">{{lang=category_slug}}</label>
            <input type="text" class="form-control block w-full px-3 py-1.5
                        text-base font-normal text-gray-700 bg-white bg-clip-padding
                        border border-solid border-gray-300 rounded transition
                        ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                        focus:border-blue-600 focus:outline-none" id="categorySlug" name="slug"
                placeholder="{{lang=category_slug}}" value="" required />
        </div>
        <div class="mt-8 mb-4 flex gap-4 justify-end items-center">
            <a href="/categories"
                class="inline-block px-6 py-2.5 bg-gray-200 text-gray-700 font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-300 hover:shadow-lg focus:bg-gray-300 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-400 active:shadow-lg transition duration-150 ease-in-out">{{lang=cancel}}</a>
            <button type="submit"
                class="inline-block px-6 py-2.5 bg-sky-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=add}}</button>
        </div>
    </form>
</main>