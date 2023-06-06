<div class="mb-4">
    <input type="hidden" value="{{value}}" name="{{name}}" data-atlantis-image-load>
    <input type='file' class="hidden" aria-label="{{label}}" accept=".jpg, .png, .jpeg, .gif, .bmp, .webp" data-atlantis-image-load>
    <img src="{{src}}" width="{{width}}" height="{{height}}" alt="{{label}}" class="rounded w-full object-cover border border-gray-300 cursor-pointer" data-atlantis-image-load data-token="{{csrf-token}}" data-url="{{url}}" data-placeholder="/placeholder.svg">
    <button type="button" data-te-ripple-init data-te-ripple-color="light" class="mt-4 w-full inline-block flex-grow md:flex-grow-0 px-6 py-2.5 bg-pink-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-pink-700 hover:shadow-lg focus:bg-pink-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-rpinked-800 active:shadow-lg transition duration-150 ease-in-out" data-atlantis-image-load>
        {{lang=delete_image}}
    </button>
</div>