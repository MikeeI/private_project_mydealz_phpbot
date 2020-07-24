#!/bin/bash
echo "crontab creation"
cp scripts/crontab_mydealz_phpparserbot /etc/cron.d/
#chmod 777 -R /etc/cron.d/
chmod 600 -R /etc/cron.d/
#sudo chmod 600 /etc/cron.d/crontab_extension_monitoring
sudo service cron restart