<dialog id="alertDialod" class="rounded-xl shadow-xl relative w-96">
    <form method="dialog">
        <button value="cancel" class="absolute top-4 right-4 ring-0 focus:ring-0 outline-none text-gray-500"><i
                class="fas fa-times text-xl"></i></button>
        <h5 class="block text-xl font-medium leading-normal text-gray-800 pr-12 mb-4 truncate" id="exampleModalLabel">
            {{title}}</h5>
        <div class="mb-4">{{message}}</div>
        <div class="mb-4">{{html}}</div>
        <button value="default"
            class="inline-block w-full px-6 py-2.5 bg-sky-500 text-white font-medium text-xs leading-tight uppercase rounded-lg shadow-md hover:bg-sky-700 hover:shadow-lg focus:bg-sky-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-sky-800 active:shadow-lg transition duration-150 ease-in-out">OK</button>
    </form>
</dialog>
<script nonce="{{nonce}}">
    document.getElementById('alertDialod').showModal()
</script>