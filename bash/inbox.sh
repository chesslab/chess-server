#!/bin/bash
# Delete correspondence inboxes after thirty days from their modification date.

script_dir=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
dir_path="${script_dir}/../storage/inbox/data"

cd $dir_path

for file in *
do
    ftime=`stat -c %Y $file`
    ctime=`date +%s`
    diff=$(( (ctime - ftime) / 86400 ))
    if (( diff > 30 ))
    then
        cat /dev/null > "$dir_path/$file"
    fi
done
