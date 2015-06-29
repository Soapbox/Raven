#!/usr/bin/env bash

soapboxRoot=~/.soapbox

mkdir -p "$soapboxRoot"

cp -i src/stubs/Soapbox.yaml "$soapboxRoot/Soapbox.yaml"
cp -i src/stubs/after.sh "$soapboxRoot/after.sh"
cp -i src/stubs/aliases "$soapboxRoot/aliases"

echo "Soapbox initialized!"
