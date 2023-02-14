<main class="container mx-auto">
    <div class="text-pink-600 p-8 flex flex-col justify-center items-center">
        <svg width="200px" height="200px" viewBox="0 0 24 24" fill="transparent" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M12 15H12.01M12 12V9M4.98207 19H19.0179C20.5615 19 21.5233 17.3256 20.7455 15.9923L13.7276 3.96153C12.9558 2.63852 11.0442 2.63852 10.2724 3.96153L3.25452 15.9923C2.47675 17.3256 3.43849 19 4.98207 19Z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <h1 class="text-gray-800 font-semibold text-center">{{message}}</h1>
    </div>
    <form action="{{form_action}}" method="{{form_method}}" class="flex p-8 items-center justify-center gap-4">
        {{csrf}}
        <a href="{{back_url}}"
            class="inline-block px-6 py-2.5 bg-gray-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-gray-700 hover:shadow-lg focus:bg-gray-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-gray-800 active:shadow-lg transition duration-150 ease-in-out">{{lang=no}}</a>
        <button type="submit" class="inline-block px-6 py-2.5 bg-pink-600 text-white 
            font-medium text-xs leading-tight uppercase rounded shadow-md 
            hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 
            focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-800 
            active:shadow-lg transition duration-150 ease-in-out">{{lang=yes}}</button>
    </form>
</main>