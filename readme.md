# Install

## Need change:
````
// .env
MAIN_SERVER
// resources/lang/en/company.php
powered_url
````

## schedule queue email 
```bash
// use crontab
env EDITOR=nano crontab -e
php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
ctr+o -> enter -> ctr+x
// check cron list
crontab -l

```

## app id
https://apps.dev.microsoft.com/?lc=1033#/application/c79693bd-db12-49b9-90d7-4c88b39761f1
