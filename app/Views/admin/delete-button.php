<button id="delete-button-{{uniqid}}" type="button" aria-label="{{lang=delete}}" class="atlantis-delete-button inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=delete}}</button>
<script nonce="{{nonce}}" type="module">
    import Atlantis from '/js/atlantis.js'

    const $ = new Atlantis()

    const dialog = $.dialog({
        title: `{{title}}`,
        message: `{{message}}`,
        buttons: [{
                text: `<i class="fas fa-times text-xl"></i>`,
                class: `bg-sky-800 text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-700`
            },
            {
                text: `<i class="fas fa-check text-xl"></i>`,
                class: `bg-pink-600 text-white hover:bg-pink-500 focus:bg-pink-500 active:bg-pink-500`,
                callback: () => {
                    $.fetch(`{{url}}`, {
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        method: 'DELETE',
                        success: () => {
                            window.location.assign(`{{redirect}}`)
                        }
                    })
                }
            }
        ]
    })

    $.on(document.getElementById(`delete-button-{{uniqid}}`), 'click', () => {
        dialog.show()
    })
</script>