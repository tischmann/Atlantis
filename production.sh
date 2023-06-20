#!/bin/bash

echo "Minifying app.css";

npx tailwindcss -i ./public/app.css -o ./public/app.min.css --minify;

echo "";

echo "Minifying app.js";

cd public;

uglifyjs app.js --compress --mangle --warn --output app.min.js;

cd ../resources/js;

for FILE in *;
do 

echo "";

echo "Minifying: $FILE";

uglifyjs $FILE --compress --mangle --warn --output ../../public/js/$FILE;

done;

echo "";

echo "Minifying complete";