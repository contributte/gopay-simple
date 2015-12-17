#!/bin/bash

if [ -z "$1" ]; then
    FOLDER=`pwd`
else
    FOLDER=$1
fi

echo "Start PHP buil-in server at $FOLDER"
php -S localhost:8080 -t $FOLDER
