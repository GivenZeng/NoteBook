// 重启 

git pull && ansible om_web_00 -m copy -a 'src=oams/define.yaml dest=/home/ymserver/bin/oams_report/config/define.yaml backup=1' && ansible om_web_00 -m shell -a 'touch bin/oams_report/canary/reload'
