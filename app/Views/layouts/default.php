<!DOCTYPE html>
<?php
$resource_version = get_resource_version();

$imports = [];

foreach (glob(getenv("APP_ROOT") . "/public/js/*.js") as $file) {
    $filename = basename($file, ".min.js");
    $imports[] = "\"{$filename}\": \"/js/{$filename}.min.js?v={$resource_version}\"";
}

?>
<html lang="{{env=APP_LOCALE}}" class="<?= is_visually_impaired() ? "visually-impaired" : "" ?> <?= is_dark_mode() ? "dark" : "" ?>">

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
    <link rel="manifest" href="/webmanifest.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <title>{{title}}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="/app.min.css" media="all">
    <script type="importmap" nonce="{{nonce}}">
        {
            "imports": { <?= implode(",\n", $imports) ?>}
        }
    </script>
    <script src="/app.min.js" nonce="{{nonce}}" type="module"></script>
</head>

<body class="bg-white text-gray-800 dark:bg-gray-800 dark:text-white antialiased m-0 p-0">
    <?php include __DIR__ . "/../header.php" ?>

    {{body}}
    <?php include __DIR__ . "/../footer.php" ?>
</body>

</html>