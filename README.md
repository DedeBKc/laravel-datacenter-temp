## CREATE CRONJOB

```bash
sudo crontab -e -u www-data
```

```bash
* * * * * /usr/bin/php /var/www/laravel-datacenter-temp/artisan sample:cron >> /dev/null 2>&1
```
