<div class="absolute inset-x-8 top-8 sm:inset-x-auto sm:right-8 bg-green-600 
shadow-lg mx-auto sm:w-96 max-w-full text-sm pointer-events-auto 
bg-clip-padding rounded-lg block" id="atlantisAlert" role="alert" aria-live="assertive" aria-atomic="true"
    data-mdb-autohide="false">
    <div class="bg-green-600 flex justify-between items-center py-2 px-3 
    bg-clip-padding border-b border-green-500 rounded-t-lg">
        <p class="font-bold text-white flex items-center">
            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check-circle"
                class="w-4 h-4 mr-2 fill-current" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path fill="currentColor"
                    d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z">
                </path>
            </svg>
            {{title}}
        </p>
        <div class="close-alert"></div>
    </div>
    <div class="p-3 rounded-b-lg break-words text-white bg-green-600">{{message}}</div>
</div>
<script nonce=" {{nonce}}" id="alertScript">
    setTimeout(() => {
        document.getElementById('atlantisAlert').remove()
        document.getElementById('alertScript').remove()
    }, 3000);
</script>