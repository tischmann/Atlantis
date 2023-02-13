{{layout=default}}
{{section=body}}
<main>
    <div class="m-4 text-red-600">{{lang=error}}</div>
    <div class="m-4 text-black">{{$message}}</div>
    {{if $trace}}
    <div class="m-4 text-black">
        <div class="col">
            <table class="border border-gray-200">
                <thead>
                    <tr class="bg-purple-800 text-white">
                        <th scope="col" class="border-r border-gray-200 px-3 py-2">#</th>
                        <th scope="col" class="border-r border-gray-200 px-3 py-2">File</th>
                        <th scope="col" class="border-r border-gray-200 px-3 py-2">Line</th>
                        <th scope="col" class="px-3 py-2">Function</th>
                    </tr>
                </thead>
                <tbody>
                    {{each $trace as $key => $value}}
                    <tr>
                        <td scope="row" class="border-r border-b border-gray-200 px-3 py-2">{{$key}}</td>
                        <td class="border-r border-b border-gray-200 px-3 py-2">{{$value->file}}</td>
                        <td class="border-r border-b border-gray-200 px-3 py-2">{{$value->line}}</td>
                        <td class="border-b border-gray-200 px-3 py-2">{{$value->function}}</td>
                    </tr>
                    {{/each}}
                </tbody>
            </table>
        </div>
    </div>
    {{/if}}
    <div class="m-4">
        <a class="text-blue-600 hover:text-blue-700 transition duration-300 ease-in-out mb-4 underline"
            href="{{$referer}}">{{lang=back}}</a>
    </div>
</main>
{{/section}}