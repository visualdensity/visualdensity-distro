#!/bin/bash

if [[ $EUID -ne 0 ]];
then
    echo "Sorry, but this script needs to be run as root!"
    exit 1
fi

echo "deb http://www.rabbitmq.com/debian/ testing main"  > /etc/apt/sources.list.d/rabbitmq.list
wget https://www.rabbitmq.com/rabbitmq-signing-key-public.asc
apt-key add rabbitmq-signing-key-public.asc

apt-get install rabbitmq-server

rm rabbitmq-signing-key-public.asc
