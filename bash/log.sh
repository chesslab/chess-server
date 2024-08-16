#!/bin/bash

script_dir=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
file_path="${script_dir}/../storage/game.log"
max_size=10485760
file_size=$(stat -c%s "$file_path")

if (( file_size > max_size ))
then
    cat /dev/null > $file_path
fi
