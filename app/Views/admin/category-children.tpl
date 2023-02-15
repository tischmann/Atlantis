<div class="mb-4">
    <div class="form-label inline-block mb-1 text-gray-500">{{lang=category_children}}</div>
    <ul class="sortable-container list-none flex flex-wrap gap-4 p-4 bg-sky-400 rounded-xl text-sky-800">
        {{childs}}
    </ul>
</div>
<script src="/js/sortable.js" nonce="{{nonce}}"></script>
<script nonce="{{nonce}}">
    document.querySelectorAll('sortable-container').forEach((container) => {
        new Sortable(container, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'bg-sky-200'
        })
    })
</script>