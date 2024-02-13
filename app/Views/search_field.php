<form class="relative flex items-center flex-grow md:flex-grow-0" method="GET" action="/{{env=APP_LOCALE}}/search" autocomplete="off">
    <input type="search" class="relative m-0 h-10 block w-full md:focus:w-[300px] md:w-[130px] min-w-[130px] flex-auto rounded-xl border-2 border-solid border-gray-300 bg-transparent bg-clip-padding px-3 pr-10 py-1.5 text-base font-normal text-gray-800 outline-none transition-all duration-300 ease-in-out focus:border-gray-400 focus:text-gray-800" placeholder="{{lang=search}}" aria-label="{{lang=search}}" name="query" value="<?= $search ?>" autocomplete="off" />
    <span class="absolute right-0 input-group-text flex items-center whitespace-nowrap rounded px-3 py-1.5 text-center text-base font-normal text-gray-400">
        <i class="fa-solid fa-magnifying-glass"></i>
    </span>
</form>