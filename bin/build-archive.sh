#!/usr/bin/env bash

rm -rf ./build

mkdir ./build
mkdir ./build/algolia

cp -R ./inc ./build/algolia
cp -R ./libs ./build/algolia
cp algolia.php ./build/algolia
cp README.md ./build/algolia

cd ./build
zip -r algolia.zip algolia
rm -rf ./algolia
cd ..


