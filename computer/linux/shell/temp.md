#!/usr/bin/zsh


# 将数据推送到s3
# $1 本地数据所在的目录名
# $2 s3 bucket名

if [ "$#" -ne 3 ]
then
    echo "错误：没有指定数据目录或缺少s3目录"
    exit 2
fi

echo $1 , $2
for fileName in `ls -1 $1 | tr ' ' '#'`
do

	fileName=`echo "$fileName" | tr '#' ' '`
	filePath=$1"/"$fileName
    #如果是文件
    if test -f $filePath
    then
        #取出文件日期作为在s3的prefix
        # prefix= `echo "$fileName" | cut -d ' ' -f 1`
        # parts=($fileName)
        # prefix=${parts[1]}
        prefix="${fileName:0:10}"
        #将该文件上传到s3
        echo "get a file , start upload file:" $filePath " , prefix:${prefix}"
        # `aws --profile ad_log s3 cp ${filePath} s3://datamining.ym/ad_log/${prefix}/`
        #

    else
    	echo "get a dir , ignore it:" $filePath    
    fi
done
