{{layout=default}}
{{section=body}}
<main class="w-screen h-screen flex flex-col items-center justify-center">
    <img src="android-chrome-512x512.png" width="256px" height="256px" alt="Atlantis">
    {{if auth}}Welcome, {{$user->login}}!{{/if}}
    {{include=admin}}
</main>
{{/section}}