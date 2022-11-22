
#!/bin/bash

cd resources/css;

cleancss -o ../../public/css/app.min.css *.css --with-rebase

cd ../js;

cat *.js | uglifyjs --compress --mangle --warn > ../../public/js/app.min.js;
