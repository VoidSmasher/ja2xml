#!/bin/sh

# Configure application
mkdir application/cache
chmod -R 777 application/cache
mkdir application/logs
chmod -R 777 application/logs

# Daemons
mkdir application/daemons
chmod -R 777 application/daemons

# Data
mkdir application/data
chmod -R 777 application/data

# Uploads
mkdir htdocs/uploads
mkdir htdocs/uploads/images
mkdir htdocs/uploads/images/temp

mkdir htdocs/uploads/images/summernote

mkdir htdocs/uploads/images/avatars
mkdir htdocs/uploads/images/avatars/large
mkdir htdocs/uploads/images/avatars/small

mkdir htdocs/uploads/images/article
mkdir htdocs/uploads/images/article/large
mkdir htdocs/uploads/images/article/small
mkdir htdocs/uploads/images/article/regular

chmod -R 777 htdocs/uploads

# Создание файла конфигурации проекта
touch application/config/project_custom.php
chmod 777 application/config/project_custom.php

# Локальная настройка базы - после создания файл необходимо отредактировать вручную
if [ ! -f "./application/config/database.php" ]; then
	printf "\nNew database config created in path: application/config/database.php"
	printf "\nYou have to edit database config manually\n\n"
	cp application/config/database.example.php application/config/database.php
fi
