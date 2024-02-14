#!/bin/bash

cd public;

uglifyjs app.js --compress --mangle --warn --output app.min.js;

cleancss -o app.min.css app.css --source-map --with-rebase;