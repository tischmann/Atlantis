{{layout=default}}
{{section=body}}
<main style="height:100vh;width:100vw;display:flex;flex-direction:column;align-items:center;justify-content:center">
    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24"
        xml:space="preserve" width="30vh" height="30vh">
        <path fill="currentColor"
            d="M22.8,9.7h-1.8V5c0-1.9-1.6-3.5-3.5-3.5H5.9C3.9,1.5,2.3,3,2.3,5v4.7H1.2C0.5,9.7,0,10.2,0,10.8S0.5,12,1.2,12h21.7
        		c0.6,0,1.2-0.5,1.2-1.2S23.5,9.7,22.8,9.7z M18.7,9.7h-14V5c0-0.6,0.5-1.2,1.2-1.2h11.7c0.6,0,1.2,0.5,1.2,1.2V9.7z" />
        <path fill="currentColor"
            d="M17.6,13.2c-1.7,0-3.2,0.9-4,2.3H9.9c-0.8-1.4-2.3-2.3-4-2.3c-2.6,0-4.7,2.1-4.7,4.7s2.1,4.7,4.7,4.7s4.7-2.1,4.7-4.7h2.3
        		c0,2.6,2.1,4.7,4.7,4.7s4.7-2.1,4.7-4.7S20.1,13.2,17.6,13.2z M5.9,20.2c-1.3,0-2.3-1.1-2.3-2.3s1.1-2.3,2.3-2.3s2.3,1.1,2.3,2.3
        		S7.1,20.2,5.9,20.2z M17.6,20.2c-1.3,0-2.3-1.1-2.3-2.3s1.1-2.3,2.3-2.3s2.3,1.1,2.3,2.3S18.9,20.2,17.6,20.2z" />
    </svg>
    {{if $message}}
    <h1>{{$message}}</h1>
    <hr>
    {{/if}}
    <a rel="noopener noreferrer" href="{{referrer}}">{{lang=error_go_back}}</a>
</main>
{{/section}}