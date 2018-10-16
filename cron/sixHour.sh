#!/bin/bash

step=196  #间隔的秒数，不能大于60

for (( i = 0; i < 60; i=(i+step) )); do
    curl https://testacx.alliancecapitals.com/cron/sixHour
    sleep $step
done

exit 0