{{layout=default}}
{{section=body}}
{{include=admin/breadcrumbs}}
{{include=alert}}
<main class="container mx-auto">
    <form method="post">
        <article class="m-6">
            <div class="flex justify-center">
                <div class="mb-4 w-full">
                    <label for="articleLocaleInput"
                        class="form-label inline-block mb-2 text-gray-700 font-bold">{{lang=article_locale}}</label>
                    <select class="form-select appearance-none
                              block
                              w-full
                              px-3
                              pr-10
                              py-1.5
                              text-base
                              font-normal
                              text-gray-700
                              bg-white bg-clip-padding bg-no-repeat
                              border border-solid border-gray-300
                              rounded
                              transition
                              ease-in-out
                              m-0
                              focus:text-gray-700 focus:bg-white focus:border-sky-600 focus:outline-none" name="locale"
                        id="articleLocaleInput">
                        {{each $locales as $key => $locale}}
                        <option value="{{$key}}" {{if $locale->selected}}selected{{/if}}>{{$locale->title}}</option>
                        {{/each}}
                    </select>
                </div>
            </div>
            <div class="flex justify-center">
                <div class="mb-4 w-full">
                    <label for="articleCategoryInput"
                        class="form-label inline-block mb-2 text-gray-700 font-bold">{{lang=article_category}}</label>
                    <select class="form-select appearance-none
                              block
                              w-full
                              px-3
                              pr-10
                              py-1.5
                              text-base
                              font-normal
                              text-gray-700
                              bg-white bg-clip-padding bg-no-repeat
                              border border-solid border-gray-300
                              rounded
                              transition
                              ease-in-out
                              m-0
                              focus:text-gray-700 focus:bg-white focus:border-sky-600 focus:outline-none"
                        name="category_id" id="articleCategoryInput">
                        {{each $categories as $category}}
                        <option value="{{$category->id}}" {{if $category->selected}}selected{{/if}}>{{$category->title}}
                        </option>
                        {{/each}}
                    </select>
                </div>
            </div>
            <div class="flex justify-center">
                <div class="mb-4 w-full">
                    <label for="articleTitleInput"
                        class="form-label inline-block mb-2 text-gray-700 font-bold">{{lang=article_title}}</label>
                    <input type="text" class="
                form-control
                block
                w-full
                px-3
                py-1.5
                text-base
                font-normal
                text-gray-700
                bg-white bg-clip-padding
                border border-solid border-gray-300
                rounded
                transition
                ease-in-out
                m-0
                focus:text-gray-700 focus:bg-white focus:border-sky-600 focus:outline-none
              " id="articleTitleInput" placeholder="{{lang=article_title}}" value="{{$article->title}}" name="title"
                        required />
                </div>
            </div>
            <div class="mb-4 w-full">
                <label for="articleImageInput"
                    class="form-label inline-block mb-2 text-gray-700 font-bold">{{lang=article_image}}</label>
                <input type="hidden" value="" name="image" id="articleImageInput">
                <input type='file' id="articleImageFile" class="hidden" aria-label="{{lang=article_image}}">
                <img src="{{$article->image}}" id="articleImage" width="400" height="300" alt="{{$article->title}}"
                    class="rounded max-sm:w-full object-cover">
            </div>
            <div class="flex justify-center">
                <div class="mb-4 w-full">
                    <label for="articleShortTextInput"
                        class="form-label inline-block mb-2 text-gray-700 font-bold">{{lang=article_short_text}}</label>
                    <textarea class="
                form-control
                block
                w-full
                px-3
                py-1.5
                text-base
                font-normal
                text-gray-700
                bg-white bg-clip-padding
                border border-solid border-gray-300
                rounded
                transition
                ease-in-out
                m-0
                focus:text-gray-700 focus:bg-white focus:border-sky-600 focus:outline-none
              " id="articleShortTextInput" rows="4" placeholder="{{lang=article_short_text}}" name="short_text"
                        required>{{$article->short_text}}</textarea>
                </div>
            </div>
            <div class="flex justify-center">
                <div class="mb-4 w-full">
                    <label for="articleFullTextInput"
                        class="form-label inline-block mb-2 text-gray-700 font-bold">{{lang=article_full_text}}</label>
                    <textarea class="tinymce-editor
                        form-control
                        block
                        w-full
                        px-3
                        py-1.5
                        text-base
                        font-normal
                        text-gray-700
                        bg-white bg-clip-padding
                        border border-solid border-gray-300
                        rounded
                        transition
                        ease-in-out
                        m-0
                        focus:text-gray-700 focus:bg-white focus:border-sky-600 focus:outline-none
                      " id="articleFullTextInput" placeholder="{{lang=article_full_text}}" name="full_text"
                        required>{{$article->full_text}}</textarea>
                </div>
            </div>
            <div class="flex space-x-2 justify-end">
                <a class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                    href="/delete/article/{{$article->id}}">{{lang=delete}}</a>
                <a href="/admin/articles"
                    class="inline-block px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-700 hover:shadow-lg focus:bg-gray-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=cancel}}</a>
                <button type="submit"
                    class="inline-block px-6 py-2.5 bg-sky-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=save}}</button>
            </div>
        </article>
    </form>
    <script src="/tinymce/tinymce.min.js" nonce="{{nonce}}"></script>
    <script src="/js/image.js" nonce="{{nonce}}"></script>
    <script nonce="{{nonce}}">
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            editimage_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_sticky: false,
            height: 600,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_class: 'mceNonEditable',
            toolbar_mode: 'sliding',
            contextmenu: 'link image table',
            skin: 'oxide',
            content_css: 'default',
        });

        const img = document.getElementById('articleImage')

        const file = document.getElementById('articleImageFile')

        file.addEventListener('change', function (event) {
            loadImage(
                event.target.files[0],
                img,
                document.getElementById('articleImageInput')
            )
        })

        img.addEventListener('click', function (event) {
            file.dispatchEvent(new MouseEvent('click'));
        })

        function loadImage(
            file,
            img,
            picture,
            width = 800,
            height = 600
        ) {
            if (!file) return

            const imageReader = new FileReader();

            imageReader.onload = (function (f) {
                return function (e) {
                    const image = new Image();

                    image.src = e.target.result;

                    image.onload = (function () {
                        new IMage(
                            image.src,
                            image.width,
                            image.height
                        ).rect(width, height).then((base64) => {
                            img.setAttribute('src', base64)
                            picture.value = base64
                        })
                    })()
                }
            })(file)

            imageReader.readAsDataURL(file)
        }
    </script>
</main>
{{/section}}