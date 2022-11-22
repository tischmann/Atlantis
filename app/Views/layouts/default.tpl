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
    <link rel="preload" href="/css/app.css" as="style" media="all">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        main {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    {{yield=body}}
    <script src="/pwa.js"></script>
</body>

</html>