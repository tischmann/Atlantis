<div class="relative">
    <label class="absolute select-none -top-3 left-2 mb-2 text-sm text-gray-600 bg-white px-1">{{label}}</label>
    <input value="{{value}}" name="{{name}}" class="hidden" required />
    <div class="px-3 py-2 outline-none border-2 border-gray-200 rounded-lg w-full focus:border-sky-600 transition" data-select>{{title}}</div>
    <ul class="absolute select-none mt-1 hidden bg-white border-2 border-gray-200 rounded-lg shadow-lg max-h-[50vh] overflow-y-auto z-20" data-options>{{items}}</ul>
</div>