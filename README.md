# POC Integracao

## Install dependencies

### SO Dependencies

```
# Fedora
sudo dnf install php-pecl-xdebug php-pecl-redis5
```

### Project Dependencies

```
composer install
```

## Infra Dependencies

```
docker-composer up
```

After minio running, you should create `integracao` bucket.

## Build And Run

```
# build
make

# run
# dist/integracao <mode>

# process to list files from ftp
dist/integracao list-files

# consumer to dowload file from ftp
dist/integracao download-files

# consumer to process file from ftp
dist/integracao process-files
```

## Run Tests

To run unit tests

```
make test
```

Report coverage will be generated in coverage directory.
