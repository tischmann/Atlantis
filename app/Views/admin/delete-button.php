<button type="button" id="{{id}}" aria-label="{{lang=delete}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-500 hover:shadow-lg focus:bg-pink-500 active:bg-pink-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=delete}}</button>
<script nonce="{{nonce}}">
    document.getElementById(`{{id}}`)
        .addEventListener('click', () => {
            new Dialog({
                title: `{{title}}`,
                message: `{{message}}`,
                buttons: [{
                        text: `{{lang=no}}`,
                        class: `bg-sky-800 text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-700`,
                        callback: () => {}
                    },
                    {
                        text: `{{lang=yes}}`,
                        class: `bg-pink-600 text-white hover:bg-pink-500 focus:bg-pink-500 active:bg-pink-500`,
                        callback: () => {
                            fetch(`{{url}}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': `XMLHttpRequest`,
                                    'X-Csrf-Token': `{{csrf-token}}`,
                                    'Accept': 'application/json',
                                },
                            }).then(response => response.json().then(data => {
                                if (data?.status) {
                                    window.location.href = `{{redirect}}`
                                } else {
                                    alert(data.message)
                                    console.error(data.message)
                                }
                            }).catch(error => {
                                alert(error)
                                console.error(error)
                            })).catch(error => {
                                alert(error)
                                console.error(error)
                            })
                        }
                    },
                ]
            }).show()
        })
</script>