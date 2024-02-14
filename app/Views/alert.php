<dialog id="alertDialod" class="rounded-xl shadow-xl relative w-full md:max-w-screen-xl">
    <form method="dialog" class="p-8">
        <button value="cancel" class="absolute top-4 right-4 ring-0 focus:ring-0 outline-none text-gray-500 hover:text-red-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
        <h5 class="block text-xl font-medium leading-normal text-gray-800 pr-12 mb-4 truncate" id="exampleModalLabel">
            {{lang=warning}}
        </h5>
        <div class="mb-4 w-full overflow-y-auto max-h-32">{{message}}</div>
        <div class="mb-6">{{html}}</div>
        <button value="default" class="inline-block w-full px-6 py-2.5 bg-sky-800 text-white font-medium leading-tight uppercase rounded-lg shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-700 active:shadow-lg transition duration-150 ease-in-out">OK</button>
    </form>
</dialog>
<script nonce="{{nonce}}">
    document.getElementById('alertDialod').showModal()
</script>