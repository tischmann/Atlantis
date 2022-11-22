<nav class="sticky top-0 flex p-2 items-center">
    <img src="images/atlantis-logo.svg" class="flex-none m-2 h-auto w-8" alt="{{env=app_title}}">
    <div class="grow flex justify-end">
        {{if !auth}}
        <a href="/signin" data-mdb-ripple="true" data-mdb-ripple-color="light"
            class="inline-block m-2 px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=auth_signin}}</a>
        {{/if}}
        {{if auth}}
        <a href="/signout" data-mdb-ripple="true" data-mdb-ripple-color="light"
            class="inline-block m-2 px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=auth_signout}}</a>
        {{/if}}
    </div>
</nav>