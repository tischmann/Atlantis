{{layout=default}}
{{section=body}}
{{if !auth}}
<main>
    <section class="h-screen select-none">
        <div class="container px-6 py-12 h-full mx-auto">
            <div class="flex justify-center items-center flex-wrap h-full g-6 text-gray-800">
                <div class="md:w-8/12 lg:w-6/12 mb-12 md:mb-0">
                    <img src="images/auth.svg" class="w-full" alt="{{lang=auth_login}}" />
                </div>
                <div class="md:w-8/12 lg:w-5/12 lg:ml-20">
                    <form action="/signin" method="post">
                        <!-- Email input -->
                        <div class="mb-6">
                            <input type="text" name="login"
                                class="form-control block w-full px-4 py-2 text-xl font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                placeholder="{{lang=auth_login}}" aria-label="{{lang=auth_login}}" />
                        </div>
                        <!-- Password input -->
                        <div class="mb-6">
                            <input type="password" name="password"
                                class="form-control block w-full px-4 py-2 text-xl font-normal text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
                                placeholder="{{lang=auth_password}}" aria-label="{{lang=auth_password}}" />
                        </div>
                        <!-- Submit button -->
                        <button type="submit"
                            class="inline-block px-7 py-3 bg-blue-600 text-white font-medium text-sm leading-snug uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out w-full"
                            data-mdb-ripple="true" data-mdb-ripple-color="light"
                            aria-label="{{lang=auth_signin}}">{{lang=auth_signin}}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
{{/if}}
{{/section}}