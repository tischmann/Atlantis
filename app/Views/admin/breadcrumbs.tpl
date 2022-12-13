<nav class="bg-gray-100 px-5 py-3 rounded-md w-full sticky-top">
    <ol class="list-reset flex flex-wrap gap-2">
        <li><a href="/" class="text-sky-600 hover:text-sky-700">{{lang=home}}</a></li>
        {{each $breadcrumbs as $breadcrumb}}
        <li><span class="text-gray-500">/</span></li>
        {{if !$breadcrumb->href}}<li class="text-gray-500">{{$breadcrumb->title}}</li>{{/if}}
        {{if $breadcrumb->href}}<li><a href="{{$breadcrumb->href}}"
                class="text-sky-600 hover:text-sky-700">{{$breadcrumb->title}}</a></li>{{/if}}
        {{/each}}
    </ol>
</nav>