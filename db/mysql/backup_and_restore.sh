#!/bin/bash
set -e

MASTER_CONTAINER="mysql-master"
SLAVE1_CONTAINER="mysql-slave1"
SLAVE2_CONTAINER="mysql-slave2"
ROOT_PASSWORD="rootpass"
BACKUP_FILE="master_backup.sql"

echo "Создаем дамп с мастера..."

docker exec -i $MASTER_CONTAINER mysqldump \
  --all-databases \
  --single-transaction \
  --master-data=2 \
  --flush-logs \
  --hex-blob \
  --triggers \
  --routines \
  --events \
  -uroot -p${ROOT_PASSWORD} > $BACKUP_FILE

echo "Дамп создан: $BACKUP_FILE"

for SLAVE in $SLAVE1_CONTAINER $SLAVE2_CONTAINER; do
  echo "Останавливаем слейв в контейнере $SLAVE"
  docker exec -i $SLAVE mysql -uroot -p${ROOT_PASSWORD} -e "STOP SLAVE; RESET SLAVE ALL;"

  echo "Импортируем дамп в $SLAVE"
  docker exec -i $SLAVE mysql -uroot -p${ROOT_PASSWORD} < $BACKUP_FILE

  echo "Очищаем репликацию и настроим слейв для репликации позже"
done

echo "Дамп импортирован в слейвы. Теперь нужно настроить репликацию (см. команды ниже)."


#STOP SLAVE;
#
#CHANGE MASTER TO
#  MASTER_HOST='mysql-master',
#  MASTER_USER='root',
#  MASTER_PASSWORD='rootpass',
#  MASTER_LOG_FILE='mysql-bin.000019',
#  MASTER_LOG_POS=157;
#
#START SLAVE;
#
#SHOW SLAVE STATUS\G

#- CHANGE MASTER TO MASTER_LOG_FILE='mysql-