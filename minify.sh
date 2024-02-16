#!/bin/bash

cd public;

uglifyjs app.js --compress --mangle --warn --output app.min.js;

uglifyjs service-worker.js --compress --mangle --warn --output service-worker.min.js;

cleancss -o app.min.css app.css --source-map --with-rebase;