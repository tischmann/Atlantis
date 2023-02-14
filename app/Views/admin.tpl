<a class="fixed z-50 bottom-4 left-4 text-white bg-pink-600 h-16 w-16 
    flex justify-center items-center rounded-full
    hover:rotate-180 transition-all ease-in-out hover:bg-pink-700" data-bs-toggle="offcanvas"
    href="#offCanvasAdminMenu" role="button" aria-controls="offCanvasAdminMenu">
    <i class="fas fa-gear text-2xl"></i>
</a>
<div class="offcanvas offcanvas-start fixed bottom-0 flex flex-col max-w-full bg-white invisible bg-clip-padding shadow-sm outline-none transition duration-300 ease-in-out text-gray-700 top-0 left-0 border-none w-96"
    tabindex="-1" id="offCanvasAdminMenu" aria-labelledby="offCanvasAdminMenuLabel">
    <div class="offcanvas-header flex items-center justify-between p-4">
        <h5 class="offcanvas-title mb-0 leading-normal font-semibold flex items-center" id="offCanvasAdminMenuLabel">
            <img src="/favicon-16x16.png" width="16px" height="16px" alt="Atlantis">
            <span class="text-base">DMINPANEL</span>
        </h5>
        <button type="button"
            class="btn-close box-content w-4 h-4 p-2 -my-5 -mr-2 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline"
            data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow p-4 overflow-y-auto">
        <div class="flex flex-col gap-8 md:gap-4 text-gray-500 text-xl md:text-base">
            {{menu}}
        </div>
    </div>
</div>