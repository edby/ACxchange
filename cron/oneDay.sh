#!/bin/bash

step=785  #间隔的秒数，不能大于60

for (( i = 0; i < 60; i=(i+step) )); do
    curl https://testacx.alliancecapitals.com/cron/oneDay
    sleep $step
done

exit 0