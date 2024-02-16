<main class="w-screen h-screen flex flex-col items-center justify-center select-none p-6 bg-gray-200">
    <div class="flex items-center mb-6">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 400 400" xml:space="preserve" class="h-[54px] text-sky-800">
            <path fill="currentColor" d="M99.9,199.8C45,291.1,0,365.8,0,366s19.1,0.4,42.5,0.2l42.5-0.2l57.4-96c31.5-52.7,57.4-96,57.6-96s71.6,118.1,72,119.4
                c0.2,0.2-13.1,0.4-28.8,0.4h-29L193,329.7c-11.7,19.5-21.3,35.7-21.5,36.1c-0.2,0.4,40.1,0.4,114.1,0.4c62.9,0,114.3-0.2,114.3-0.4
                c0-0.4-199.7-332.2-199.9-332.2C199.9,33.9,154.9,108.5,99.9,199.8z" />
        </svg>
        <span class="-ml-2 whitespace-nowrap text-[64px] leading-[54px] font-bold">TLANTIS</span>
    </div>
    <div class="block p-6 rounded-xl bg-white w-full max-w-sm shadow-lg">
        <form method="post" class="flex flex-col gap-6">
            {{csrf}}
            <div class="flex flex-row flex-nowrap">
                <label for="loginInput" class="flex items-center bg-sky-800 text-white px-3 py-2 rounded-l-lg font-semibold">{{lang=signin_login}}</label>
                <input type="text" name="login" required class="w-full outline-none rounded-r-lg border-sky-800 border-2 px-3 py-2 font-semibold" id=" loginInput" placeholder="{{lang=signin_login}}" />
            </div>
            <div class="flex flex-row flex-nowrap">
                <label for="passwordInput" class="flex items-center bg-sky-800 text-white px-3 py-2 rounded-l-lg font-semibold">{{lang=signin_password}}</label>
                <input type="password" name="password" required class="w-full outline-none rounded-r-lg border-sky-800 border-2 px-3 py-2 font-semibold" id=" passwordInput" placeholder="{{lang=signin_password}}" />
            </div>
            <button type="submit" class="flex w-full justify-center font-semibold text-sm rounded-lg bg-sky-800 px-3 py-3 uppercase text-white shadow transition duration-150 ease-in-out hover:bg-sky-600 hover:shadow-lg focus:bg-sky-600 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-700 active:shadow-lg">{{lang=signin_submit}}</button>
        </form>
    </div>
</main>