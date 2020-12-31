#!/bin/bash

#用法 bash modify.sh '2018-07-19 10' '2018-07-20 10' 
#订正[start,end] 间的数据

overwrite=""
skipclick=""
start==$(date +'%Y-%m-%d %H' -d '1 hour ago')
range=false
if [ getopts ":o" opt ]; then
	overwrite="overwrite"
fi
if [ getopts ":s" opt ]; then
	skipclick="skipclick"
fi
if [ getopts ":r" opt ]; then
	range=true
fi

if [ $range -eq true ];then
	bet_s=`date -d "$4" +%s`
	end_s=`date -d "$5" +%s`
	echo "处理时间范围：$beg_s 至 $end_s"

	while [ "$beg_s" -le "$end_s" ];do
	    hour=`date -d @$beg_s +"%Y-%m-%d %H"`;
	    bash /tmp/crontabs/stat_hr/stat_hr.sh $hour $overwrite $skipclick
	    #echo "当前时间：$hour"
	    beg_s=$((beg_s+3600));
	
done

