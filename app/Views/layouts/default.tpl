<!DOCTYPE html>
<html lang="{{env=APP_LOCALE}}">

<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#ffffff" />
    <meta name="Description" content="{{env=APP_DESCR}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="{{env=APP_AUTHOR}}">
    <meta name="keywords" content="{{env=APP_KEYWORDS}}">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <link rel='icon' href='/favicon.ico'>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/webmanifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <title>{{env=APP_TITLE}}</title>
    <link rel="stylesheet" href="/tailwind.css" media="all">
    <link rel="preload" as="style" href="/fontawesome.css" media="all">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" />
    <link rel="preload" as="style" href="/app.css" media="all">
    <script src="/js/uuid.js" nonce="{{nonce}}"></script>
    <style>
        body {
            /* font-family: 'Inter'; */
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    {{body}}
    {{alert}}
    <script src="/tailwind.js" nonce="{{nonce}}" async></script>
    <script src="/js/lazyfetch.js" nonce="{{nonce}}" async></script>
    <script src="/js/lazyload.js" nonce="{{nonce}}" async></script>
    <script src="/app.js" nonce="{{nonce}}" async></script>
    <script src="/pwa.js" nonce="{{nonce}}" async></script>
</body>

</html>