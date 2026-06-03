## About

PHP web crawler for tgndata

## preparing for the installation.

- clone the project
- navigate in the project
- create a .env file and copy the .env.example contents into it.

```bash
cp .env.example .env
```

- create a folder in the project for your database and logs to be stored and use it's name in the .env for example /data

```bash
mkdir data
```

- in the .env:

```
DATA_PATH="data"
```

- install the composer dependecies

```bash
composer install
```

- instal the npm packages

```bash
npm init -y && npm install puppeteer
```

## Use the cli

- call the script by typing:

```bash
php run.php
```

- add flags by preference (default values are set to curl and hardcoded array of urls)

```bash
php run.php --fetch=curl --urlfile=/path/to/file.txt
```

## Results

- Once you run it the logs file and the database will be populated.
- you can check the .slite file and the log.txt file that i have attached for reference.
