#!/bin/bash

step=16  #间隔的秒数，不能大于60

for (( i = 0; i < 60; i=(i+step) )); do
    curl https://testacx.alliancecapitals.com/cron/halfHour
    sleep $step
done

exit 0