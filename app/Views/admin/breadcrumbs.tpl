{{if $breadcrumbs}}
<nav class="w-full flex gap-2 items-center text-xs uppercase font-medium">
    <a href="/" class="bg-sky-600 text-white px-3 py-2 rounded-lg hover:bg-pink-600"><i class="fas fa-home"></i></a>
    {{each $breadcrumbs as $breadcrumb}}
    {{if $breadcrumb->url}}<a href="{{$breadcrumb->url}}" class="bg-sky-500 text-white px-3 py-2 rounded-lg 
        hover:px-5 hover:bg-pink-500 transition-all ease-in-out">{{$breadcrumb->label}}</a>{{/if}}
    {{if !$breadcrumb->url}}<span
        class="bg-sky-400 text-white px-3 py-2 rounded-lg cursor-default">{{$breadcrumb->label}}</span>{{/if}}
    {{/each}}
</nav>
{{/if}}