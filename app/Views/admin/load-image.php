<div class="mb-4">
    <input id="image-load-input-{{uniqid}}" type="hidden" value="{{value}}" name="{{name}}">
    <input id="image-load-file-{{uniqid}}" type='file' class="hidden" aria-label="{{label}}" accept=".jpg, .png, .jpeg, .gif, .bmp, .webp">
    <img id="image-load-{{uniqid}}" src="{{src}}" width="{{width}}" height="{{height}}" alt="{{label}}" class="rounded w-full object-cover border border-gray-300 cursor-pointer">
    <button id="image-load-delete-{{uniqid}}" type="button" data-te-ripple-init data-te-ripple-color="light" class="mt-4 w-full inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">
        {{lang=delete_image}}
    </button>
</div>
<script nonce="{{nonce}}" type="module">
    import Atlantis from '/js/atlantis.js'

    const $ = new Atlantis()

    const imgElement = document.getElementById(`image-load-{{uniqid}}`)

    const fileElement = document.getElementById(`image-load-file-{{uniqid}}`)

    const inputElement = document.getElementById(`image-load-input-{{uniqid}}`)

    const deleteElement = document.getElementById(`image-load-delete-{{uniqid}}`)

    $.on(fileElement, 'change', (event) => {
        const file = event.target.files[0]

        if (!file) return

        const body = new FormData()

        body.append('width', imgElement.getAttribute('width'))

        body.append('height', imgElement.getAttribute('height'))

        body.append('file', file, file.name)

        $.fetch(`{{url}}`, {
            body,
            success: ({
                image,
                location
            }) => {
                inputElement.value = image
                imgElement.src = location
            },
            failure: (message) => {
                $.dialog({
                    message,
                    onclose: () => {
                        window.location.reload()
                    }
                }).show()
            }
        })
    })

    $.on(imgElement, 'click', () => {
        fileElement.click()
    })

    $.on(deleteElement, 'click', () => {
        imgElement.setAttribute('src', '/placeholder.svg')
        inputElement.value = ''
    })
</script>