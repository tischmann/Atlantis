<main class="w-screen h-screen flex flex-col items-center justify-center select-none p-6 bg-white dark:bg-gray-800 text-gray-800 dark:text-white">
    <div class="flex items-center mb-6">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 400 400" xml:space="preserve" class="h-[54px] text-sky-600">
            <path fill="currentColor" d="M99.9,199.8C45,291.1,0,365.8,0,366s19.1,0.4,42.5,0.2l42.5-0.2l57.4-96c31.5-52.7,57.4-96,57.6-96s71.6,118.1,72,119.4
                c0.2,0.2-13.1,0.4-28.8,0.4h-29L193,329.7c-11.7,19.5-21.3,35.7-21.5,36.1c-0.2,0.4,40.1,0.4,114.1,0.4c62.9,0,114.3-0.2,114.3-0.4
                c0-0.4-199.7-332.2-199.9-332.2C199.9,33.9,154.9,108.5,99.9,199.8z" />
        </svg>
        <span class="-ml-2 whitespace-nowrap text-[64px] leading-[54px] font-bold">TLANTIS</span>
    </div>
    <div class="block p-6 rounded-xl bg-gray-100 dark:bg-gray-700 w-full max-w-sm shadow-lg">
        <form method="post" class="flex flex-col gap-6">
            {{csrf}}
            <div class="relative w-full">
                <input type="text" class="py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 rounded-lg w-full bg-white dark:bg-gray-700 autofill:bg-white dark:autofill:bg-gray-700 text-gray-800 dark:text-white focus:border-sky-600 transition" id="login" name="login" value="" required autocomplete="on" title="{{lang=signin_login}}">
            </div>
            <div class="relative w-full">
                <input type="password" class="py-2 px-3 outline-none border-2 border-gray-200 dark:border-gray-600 rounded-lg w-full bg-white dark:bg-gray-700 autofill:bg-white dark:autofill:bg-gray-700 text-gray-800 dark:text-white focus:border-sky-600 transition" id="password" name="password" value="" required autocomplete="on" title="{{lang=signin_password}}">
            </div>
            <button type="submit" class="flex w-full justify-center font-medium text-sm rounded-lg bg-sky-600 px-3 py-3 uppercase text-white shadow transition duration-150 ease-in-out hover:bg-sky-500 hover:shadow-lg focus:bg-sky-500 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-500 active:shadow-lg">{{lang=signin_submit}}</button>
        </form>
    </div>
</main>