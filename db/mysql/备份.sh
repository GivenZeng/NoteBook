#!/bin/bash
/usr/bin/mysqldump -u root -pbytedance --all-databases > `date +'%Y-%m-%d-%H'`.sql
