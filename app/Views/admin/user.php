<?php include __DIR__ . "/../header.php" ?>
<main class="md:container md:mx-auto">
    <form method="post" class="mx-4">
        {{csrf}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="lg:mb-4">
                <div class="mb-4">
                    <label for="userLogin" class="form-label inline-block mb-1 text-gray-500">{{lang=user_login}}</label>
                    <input type="text" class="form-control block w-full px-3 py-1.5
                text-base font-normal text-gray-700 bg-white bg-clip-padding
                border border-solid border-gray-300 rounded transition
                ease-in-out m-0 focus:text-gray-700 focus:bg-white 
                focus:border-blue-600 focus:outline-none" id="userLogin" name="title" placeholder="{{lang=user_login}}" value="<?= $user->login ?>" required />
                </div>
            </div>
        </div>
        <div class="mb-4 flex gap-4 flex-wrap justify-evenly md:justify-end items-center">
            <?php
            if ($user->id) {
                echo <<<HTML
                <button type="button" id="deleteUserButton" aria-label="{{lang=delete}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=delete}}</button>
                <script src="/js/dialog.js" nonce="{{nonce}}"></script>
                <script nonce="{{nonce}}">
                    const dialog = new Dialog({
                        title: `{{lang=warning}}!`,
                        message: `{{lang=user_delete_confirm}}?`,
                        buttons: [
                            {
                                text: `{{lang=no}}`,
                                callback: () => {}
                            },
                            {
                                text: `{{lang=yes}}`,
                                callback: () => {
                                    fetch(`/user/delete/{$user->id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-Requested-With': `XMLHttpRequest`,
                                            'X-Csrf-Token': `{{csrf-token}}`,
                                            'Accept': 'application/json',                    
                                        },
                                    }).then(response => response.json().then(data => {
                                        if (data?.status) {
                                            window.location.href = `/{{env=APP_LOCALE}}/admin/users`
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
                    })

                    document.getElementById('deleteUserButton')
                        .addEventListener('click', () => dialog.show())
                </script>
                HTML;
            }
            ?>
            <a href="/{{env=APP_LOCALE}}/admin/users" aria-label="{{lang=cancel}}" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-700 hover:shadow-lg focus:bg-gray-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-800 active:shadow-lg transition duration-150 ease-in-out text-center">{{lang=cancel}}</a>
            <button type="submit" class="inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-sky-500 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=save}}</button>
        </div>
    </form>
</main>