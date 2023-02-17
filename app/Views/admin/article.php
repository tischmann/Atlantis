<main class="container mx-auto">
    <div class="p-4 flex sticky-top bg-white">
        <?php include __DIR__ . "/../breadcrumbs.php" ?>
    </div>
    <form method="post" class="mx-4">
        {{csrf}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="flex flex-col">
                <div class="mb-4">
                    <label for="articleLocaleInput" class="form-label inline-block mb-1 text-gray-500">{{lang=article_locale}}</label>
                    <select class="form-select appearance-none block w-full px-3 py-1.5
                                          text-base font-normal text-gray-700 bg-white bg-clip-padding 
                                          bg-no-repeat border border-solid border-gray-300 rounded
                                          transition ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                                          focus:border-blue-600 focus:outline-none" name="locale" id="articleLocaleInput">
                        {{locales_options}}
                    </select>
                </div>
                <div class="mb-4">
                    <label for="articleCategoryInput" class="form-label inline-block mb-1 text-gray-500">{{lang=article_category}}</label>
                    <select class="form-select appearance-none block w-full px-3 py-1.5
                                          text-base font-normal text-gray-700 bg-white bg-clip-padding 
                                          bg-no-repeat border border-solid border-gray-300 rounded
                                          transition ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                                          focus:border-blue-600 focus:outline-none" name="category_id" id="articleCategoryInput">
                        {{category_options}}
                    </select>
                </div>
                <div class="mb-4">
                    <label for="articleTitleInput" class="form-label inline-block mb-1 text-gray-500">{{lang=article_title}}</label>
                    <input type="text" class="form-control block w-full px-3 py-1.5
                            text-base font-normal text-gray-700 bg-white bg-clip-padding
                            border border-solid border-gray-300 rounded transition
                            ease-in-out m-0 focus:text-gray-700 focus:bg-white
                            focus:border-sky-600 focus:outline-none" id="articleTitleInput" value="{{article_title}}" name="title" required />
                </div>
                <div class="mb-4 flex-grow flex flex-col">
                    <label for="articleShortTextInput" class="form-label inline-block mb-1 text-gray-500">{{lang=article_short_text}}</label>
                    <textarea class="form-control block w-full px-3 py-1.5
                     text-base font-normal text-gray-700 bg-white 
                     bg-clip-padding border border-solid border-gray-300
                     rounded transition ease-in-out m-0 focus:text-gray-700
                     focus:bg-white focus:border-sky-600 focus:outline-none flex-grow" id="articleShortTextInput" name="short_text">{{article_short_text}}</textarea>
                </div>
            </div>
            <div class="mb-4">
                <label for="articleImageInput" class="form-label inline-block mb-1 text-gray-500">{{lang=article_image}}</label>
                <select class="form-select appearance-none block w-full px-3 py-1.5
                        text-base font-normal text-gray-700 bg-white bg-clip-padding 
                        bg-no-repeat border border-solid border-gray-300 rounded
                        transition ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                        focus:border-blue-600 focus:outline-none mb-4" id="articleImageSelect">
                    <option value="1920|1080">1920x1080 (16:9)</option>
                    <option value="1280|720">1280x720 (16:9)</option>
                    <option value="1024|576">1024x576 (16:9)</option>
                    <option value="800|450">800x450 (16:9)</option>
                    <option value="1920|1080">1920x1440 (4:3)</option>
                    <option value="1280|960">1280x960 (4:3)</option>
                    <option value="1024|768">1024x768 (4:3)</option>
                    <option value="800|600" selected>800x600 (4:3)</option>
                    <option value="1920|1920">1920x1920 (1:1)</option>
                    <option value="1280|1280">1280x1280 (1:1)</option>
                    <option value="1024|1024">1024x1024 (1:1)</option>
                    <option value="800|800">800x800 (1:1)</option>
                </select>
                <input type="hidden" value="{{article_image}}" name="image" id="articleImageInput">
                <input type='file' id="articleImageFile" class="hidden" aria-label="{{lang=article_image}}">
                <img src="{{article_image_url}}" id="articleImage" width="800" height="600" alt="{{article_title}}" class="rounded w-full object-cover">
                <div id="imageDeleteButton" class="w-full block mt-4 text-center px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-pink-800 active:shadow-lg transition duration-150 ease-in-out cursor-pointer">
                    {{lang=delete_image}}
                </div>
            </div>
        </div>
        <div class="mb-4 w-full">
            <label for="articleFullTextInput" class="form-label inline-block mb-1 text-gray-500">{{lang=article_full_text}}</label>
            <textarea class="tinymce-editor form-control block w-full px-3 py-1.5
                     text-base font-normal text-gray-700 bg-white 
                     bg-clip-padding border border-solid border-gray-300
                     rounded transition ease-in-out m-0 focus:text-gray-700
                     focus:bg-white focus:border-sky-600 focus:outline-none
                      " id="articleFullTextInput" name="full_text">{{article_full_text}}</textarea>
        </div>
        <div class="flex gap-4 justify-end mb-4">
            {{delete_button}}
            <a href="/admin/articles" aria-label="{{lang=delete}}" class="inline-block px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-700 hover:shadow-lg focus:bg-gray-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=cancel}}</a>
            <button type="submit" class="inline-block px-6 py-2.5 bg-sky-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=save}}</button>
        </div>
    </form>
    <script src="/tinymce/tinymce.min.js" nonce="{{nonce}}"></script>
    <script nonce="{{nonce}}">
        let csrf = `{{csrf-token}}`

        const imageUploadHandler = (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.withCredentials = true;

            xhr.open('POST', `/upload/article/image/{{article_id}}`);

            xhr.setRequestHeader('Accept', 'application/json');

            xhr.setRequestHeader('X-Csrf-Token', csrf);

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = () => {
                if (xhr.status === 403) {
                    reject({
                        message: 'HTTP Error: ' + xhr.status,
                        remove: true
                    });
                    return;
                }

                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                console.log(xhr.responseText)

                const json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                csrf = json.csrf

                resolve(json.location);
            };

            xhr.onerror = () => {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };

            const formData = new FormData();

            formData.append('file', blobInfo.blob(), blobInfo.filename());

            xhr.send(formData);
        });

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
            image_caption: true,
            skin: 'oxide',
            content_css: 'default',
            image_advtab: true,
            images_upload_handler: imageUploadHandler
        });

        const img = document.getElementById('articleImage')

        const file = document.getElementById('articleImageFile')

        const input = document.getElementById('articleImageInput')

        const dimensions = document.getElementById('articleImageSelect')

        document.getElementById('imageDeleteButton').addEventListener('click', () => {
            img.setAttribute('src', '/images/placeholder.svg')
            input.value = ''
        })

        const loadImage = (file, width, height) => {
            if (!file || !width || !height) return

            const formData = new FormData();

            formData.append('width', width);

            formData.append('height', height);

            formData.append('file', file, file.name);

            fetch(`/upload/article/image/{{article_id}}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Csrf-Token': csrf
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    input.value = data.image
                    img.src = data.location
                    csrf = data.csrf
                })
                .catch(error => {
                    console.error('Error:', error)
                })
        }

        file.addEventListener('change', function(event) {
            loadImage(event.target.files[0],
                img.getAttribute('width'),
                img.getAttribute('height'))
        })

        img.addEventListener('click', function(event) {
            file.dispatchEvent(new MouseEvent('click'));
        })

        dimensions.addEventListener('change', function(event) {
            const [width, height] = event.target.value.split('|')
            img.setAttribute('width', width)
            img.setAttribute('height', height)
        })
    </script>
</main>