#!/bin/bash

cd resources/js;

for file in *; do
    if [[ -f $file ]]; then
        uglifyjs "$file" --compress --mangle --warn --output "../../public/js/${file%.js}.min.js";
    fi
done

cd ../../public;

uglifyjs app.js --compress --mangle --warn --output app.min.js;

uglifyjs service-worker.js --compress --mangle --warn --output service-worker.min.js;

cleancss -o app.min.css app.css --source-map --with-rebase;

cd ..;

if [ ! -f version.env ]; then
    touch version.env
    echo "VERSION=1.0" > version.env
fi

set -a; 
source version.env; 
set +a;

VERSION=$(echo "$VERSION + 0.1" | bc)

echo "VERSION=$VERSION" > version.env