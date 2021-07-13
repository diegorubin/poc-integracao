# POC Integracao

## Install dependencies

### SO Dependencies

#### Fedora

Essentials:

```
sudo dnf install php-pecl-xdebug php-pecl-redis5
```

#### Ubuntu

Essentials:

```
sudo apt install ca-certificates apt-transport-https software-properties-common -y
sudo add-apt-repository ppa:ondrej/php
sudo apt update -y
sudo apt install php7.4 php7.4-xdebug php7.4-redis -y
sudo apt install docker docker-compose -y
```

Configure Xdebug:

```
sudo vi /etc/php/7.4/mods-available/xdebug.ini
```

Put the following lines at the end of the file:

```
xdebug.mode=debug,coverage
xdebug.client_host=127.0.0.1
xdebug.client_port=9003
xdebug.start_with_request=yes
```

Install Composer (latest version):

```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
```

### Project Dependencies

At the root of the project:

```
composer install
```

### Infra Dependencies

Run the dependencies through Docker:

```
docker-composer up
```

* You can access Redis GUI through http://localhost:16379/ (No credentials required)
* You can access MinIO GUI through http://localhost:9000/ (Use `minioadmin` user and `minioadmin` passwd)
	* After minio running, you should create `integracao` bucket
* You can access RabbitMQ GUI through http://localhost:15672/ (Use `guest` user and `guest` passwd) 

## Build And Run

To **build** (at the root of the project):
```
make
```  

To **run the integrations** (at the root of the project):
* dist/integracao **\<mode\>**

```
# process to list files from ftp
dist/integracao list-files

# consumer to download files from ftp
dist/integracao download-files

# consumer to process files from ftp
dist/integracao process-files
```

  

## Run Tests

To **run unit tests** (at the root of the project):

```
make test
```

Report coverage will be generated in coverage directory.