
#!/bin/bash

# Сжатие и оптимизация файлов стилей
cd resources/css;

purgecss --css **/*.css --content "../../app/Views/**/*.tpl" "../js/**/*.js" --output app.purged.css --font-face

cleancss -o ../../public/css/app.min.css app.purged.css --source-map --with-rebase

rm app.purged.css

cat ../js/*.js | uglifyjs --compress --mangle --warn > ../../public/js/app.min.js;
