#!/bin/bash
# Get the directory of the script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Set maximum execution time (in seconds)
TIMEOUT=290

# Start time
START_TIME=$(date +%s)

# Process a limited number of jobs
/usr/local/bin/php $DIR/artisan queue:work --stop-when-empty --max-jobs=20 --max-time=$TIMEOUT

# Log completion
echo "$(date): Queue processed" >> $DIR/storage/logs/queue.log