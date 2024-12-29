## CREATE CRONJOB

```bash
sudo crontab -e -u www-data
```

```bash
* * * * * /usr/bin/php /var/www/laravel-datacenter-temp/artisan sample:cron >> /dev/null 2>&1
```

```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
```

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## BIG DUMMY DATA

- Directory: /bigdb
-  There is instructions in the README.md file
