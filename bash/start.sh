#!/bin/bash

# Start the first process
php cli/workerman/game.php start &

# Start the second process
php cli/workerman/data.php start &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?
