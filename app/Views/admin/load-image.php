<div class="mb-4">
    <input id="image-load-input-{{uniqid}}" type="hidden" value="{{value}}" name="{{name}}">
    <input id="image-load-file-{{uniqid}}" type='file' class="hidden" aria-label="{{label}}" accept=".jpg, .png, .jpeg, .gif, .bmp, .webp">
    <img id="image-load-{{uniqid}}" src="{{src}}" width="{{width}}" height="{{height}}" alt="{{label}}" class="rounded w-full object-cover border border-gray-300 cursor-pointer">
    <button id="image-load-delete-{{uniqid}}" type="button" class="mt-4 w-full inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">
        {{lang=delete_image}}
    </button>
</div>
<script nonce="{{nonce}}">
    const img = document.getElementById(`image-load-{{uniqid}}`)

    const file = document.getElementById(`image-load-file-{{uniqid}}`)

    const input = document.getElementById(`image-load-input-{{uniqid}}`)

    file.addEventListener('change', (event) => {
        const file = event.target.files[0]

        if (!file) return

        const body = new FormData()

        body.append('width', img.getAttribute('width'))

        body.append('height', img.getAttribute('height'))

        body.append('file', file, file.name)

        fetch(`{{url}}`, {
            method: 'POST',
            body
        }).then(response => response.json()).then(({
            image,
            location
        }) => {
            input.value = image
            img.src = location
        }).catch(error => {
            dialog({
                message: error.message,
                onclose: () => {
                    window.location.reload()
                }
            }).show()
        })
    })

    img.addEventListener('click', () => {
        file.click()
    })

    document.getElementById(`image-load-delete-{{uniqid}}`)
        ?.addEventListener('click', () => {
            img.setAttribute('src', '/placeholder.svg')
            input.value = ''
        })
</script>