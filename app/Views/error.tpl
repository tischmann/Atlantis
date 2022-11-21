{{layout=default}}
{{section=body}}
<main>
    <section>
        <p>{{$error->message}}</p>
        {{if $error->trace}}
        <hr>
        <table>
            <thead>
                <tr>
                    <th>{{lang=error_trace_file}}</th>
                    <th>{{lang=error_trace_line}}</th>
                    <th>{{lang=error_trace_function}}</th>
                </tr>
            </thead>
            <tbody>
                {{each $error->trace as $trace}}
                <tr>
                    <td>{{$trace->file}}</td>
                    <td>{{$trace->line}}</td>
                    <td>{{$trace->function}}</td>
                </tr>
                {{/each}}
            </tbody>
        </table>
        {{/if}}
        <hr>
        <a rel="noopener noreferrer" href="{{referrer}}">{{lang=error_go_back}}</a>
    </section>
</main>
{{/section}}