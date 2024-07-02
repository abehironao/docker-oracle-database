# Docker OracleDatabase
ローカルのDocker環境に、OracleDatabaseを構築する手順  
PHP Laravelの、artisanコマンドでデータベースの操作までが確認できる

## 構築環境
```
macOS Sonoma 14.4.1
Apple M2 Pro
```
_Apple Siliconは、使えるエディションが限定されている_

## 成果物
```
./
├── docker-compose.yml
├── docker-images
│ ...
│   ├── OracleDatabase
│   │ ...
│   │   └── SingleInstance
│   │       │ ...
│   │       │   ├── 19.3.0
│   │       │   │ ...
│   │       │   │   ├── LINUX.ARM64_1919000_db_home.zip
│   ...     ... ... ... 
├── nginx
│   └── default.conf
├── oracle
│   └── oradata
│       ├── ORCLCD
│       ...
├── web
│   ├── Dockerfile
│   ├── instantclient-basic-linux.arm64-19.10.0.0.0dbru.zip
│   ├── instantclient-sdk-linux.arm64-19.10.0.0.0dbru.zip
│   └── php
│       └── php.ini
└── work
    │
    ... 
```
```
Oracle Database 19c
Nginx 1.23.3
PHP 8.3
Laravel 11
```
## Docker
```
Docker version 25.0.3, build 4debf41
Docker Compose version v2.24.6-desktop.1
```
## 事前準備
- Dockerの構築
- Oracleアカウントの作成

# Oracle Database
## Docker Image ダウンロード
[Oracle公式](https://github.com/oracle/docker-images)から配布されている、docker-imagesをDLする
```bash
$ git clone https://github.com/oracle/docker-images.git
```

## Oracle Database Software ダウンロード
[Oracle公式のダウンロードページ](https://www.oracle.com/database/technologies/oracle-database-software-downloads.html)から配布されている、Oracle Database Enterprise Editionの、LINUX ARM版をDLする
- LINUX.ARM64_1919000_db_home.zip

```bash
$ mv ~/Downloads/LINUX.ARM64_1919000_db_home.zip ./docker-images/OracleDatabase/SingleInstance/dockerfiles/19.3.0
```
_zipのまま、19.3.0直下に格納する_

## Oracle Database ビルド

```bash
$ ./docker-images/OracleDatabase/SingleInstance/dockerfiles/buildContainerImage.sh -v 19.3.0 -t oracle-db:19.3.0 -e
```
```
  Oracle Database container image for 'ee' version 19.3.0 is ready to be extended:

    --> oracle-db:19.3.0

  Build completed in 170 seconds.
```

## Docker Image 確認
```bash
$ docker images
```
```
REPOSITORY                     TAG               IMAGE ID       CREATED         SIZE
oracle-db                      19.3.0            6c943a0ccc34   2 minutes ago   5.54GB
```

## Oracle Database コンテナ
```bash
$ docker run --name oracle-19-db -e ORACLE_PWD=password oracle-db:19.3.0
```
```
#########################
DATABASE IS READY TO USE!
#########################
...

ALTER SYSTEM SET local_listener='' SCOPE=BOTH;
   ALTER PLUGGABLE DATABASE ORCLPDB1 SAVE STATE
Completed:    ALTER PLUGGABLE DATABASE ORCLPDB1 SAVE STATE
```

## Oracle Database 接続
```bash
$ docker exec -it oracle-19-db bash
```

```bash
bash-4.4$ sqlplus / as sysdba
```
```
Connected to:
Oracle Database 19c Enterprise Edition Release 19.0.0.0.0 - Production
Version 19.19.0.0.0

SQL>
```

# Docker Compose
## Instant Client LINUX ARM64 ダウンロード
[Oracle公式のダウンロードページ](https://www.oracle.com/jp/database/technologies/instant-client/linux-arm-aarch64-downloads.html)から接続に必要なパッケージをDLする

- instantclient-basic-linux.arm64-19.10.0.0.0dbru.zip
- instantclient-sdk-linux.arm64-19.10.0.0.0dbru.zip

```bash
$ mv ~/Downloads/instantclient-*.zip ./web
```
_zipのまま、web直下に格納する_

## Build and Up
```bash
$ docker-compose build
$ docker-compose up -d
```

## Oracle Database接続ローカルユーザ作成
_ローカルユーザは、PDB上に作成する必要がある_
```bash
$ docker exec -it oracle bash
```
```bash
bash-4.4$ sqlplus system/passw0rd@ORCLCDB
```
```sql
SQL> alter session set container=ORCLPDB1;
```
```
Session altered.
```
```sql
SQL> create user DB_USER identified by db_pass;
```
```
User created.
```
```sql
SQL> grant connect, resource to DB_USER;
```
```shell
Grant succeeded.
```
```sql
SQL> alter user DB_USER quota unlimited on users;
```
```
User altered.
```

# Laravel
## インストール
```bash
$ docker exec -it docker-oracle-database-web-1 bash
```
```bash
root@62dc08dfabf5:/var/www/html# composer install
```
## Oracle Database接続情報
```bash
root@62dc08dfabf5:/var/www/html# vi .env
```
```diff
...
-DB_CONNECTION=mysql
-DB_HOST=127.0.0.1
-DB_PORT=3306
-DB_DATABASE=laravel
-DB_USERNAME=root
-DB_PASSWORD=

+DB_CONNECTION=oracle
+DB_HOST=oracle
+DB_PORT=1521
+DB_SERVICE_NAME=ORCLPDB1
+DB_DATABASE=
+DB_USERNAME=DB_USER
+DB_PASSWORD=db_pass
...
```
## artisan マイグレーション
```bash
root@62dc08dfabf5:/var/www/html# php artisan migrate
```

## artisan シーダ
```bash
root@62dc08dfabf5:/var/www/html# php artisan db:seed --class=ColorsSeeder
```

# クライアントツール
Oracle Databaseはコンソールで操作するにはクセがあるので、GUIのクラアントスールのがおすすめ  
[DBeaverを公式からダウンロード](https://dbeaver.io/download/)