<?php 
#!/bin/bash

SSH_PORT_NUMBER=22
EDUSOHO_DIR='\/var\/www\/edusoho'
EDUSOHO_DB_NAME='smeagonline'
EDUSOHO_DB_USER='root'
EDUSOHO_SERVER_NAME=192.168.0.1
EDUSOHO_DB_PASSWORD=`cat /dev/urandom | head -1 | md5sum | head -c 6`

upgrade_system(){
    printf '* 开始更新Ubuntu系统软件...\n'
    sudo apt-get update -y
    sudo apt-get upgrade -y
    sudo apt-get install -y python-software-properties vim curl
    printf '* 更新Ubuntu系统软件...[FINISHED]\n'
    return 0
}

install_nginx()
{
    printf '* 开始安装Nginx...\n'
    sudo add-apt-repository ppa:nginx/stable -y
    sudo apt-get install -y nginx
    printf '* 安装Nginx结束...[FINISHED]\n'
    return 0
}

install_mysql()
{
    printf '* 开始安装MySQL...\n'
    export DEBIAN_FRONTEND="noninteractive"

    sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $EDUSOHO_DB_PASSWORD"
    sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $EDUSOHO_DB_PASSWORD"

    sudo apt-get install -y mysql-server

    printf '* 安装MySQL结束...[FINISHED]\n'
}

install_php()
{
    printf '* 开始安装PHP...\n'
    sudo apt-get install -y php5 php5-cli php5-curl php5-fpm php5-intl php5-mcrypt php5-mysqlnd php5-gd
    printf '* 安装PHP结束...[FINISHED]\n'
}

install_edusoho()
{
    printf '* 开始下载配置EduSoho...\n'
    # wget -O /tmp/edusoho.tar.gz http://dl.edusoho.com/edusoho-6.11.0.tar.gz

    # if [ ! -d "/var/www" ]; then
    #   sudo mkdir /var/www
    # fi

    # sudo tar -zxvf /tmp/edusoho.tar.gz -C /var/www
    # sudo chown www-data:www-data /var/www/edusoho/ -Rf

    wget -O /etc/nginx/sites-enabled/edusoho http://dl.edusoho.com/oneclick/edusoho-nginx-configuration.tpl

    sudo sed "s/EDUSOHO_SERVER_NAME/${EDUSOHO_SERVER_NAME}/g" -i /etc/nginx/sites-enabled/edusoho
    sudo sed "s/EDUSOHO_DIR/${EDUSOHO_DIR}/g" -i /etc/nginx/sites-enabled/edusoho

    printf '* 下载配置EduSoho结束...[FINISHED]\n'

    return 0;
}

clean_system()
{
    service php5-fpm restart
    service nginx restart

    printf '\n\n***********安装已完成************\n'
    printf "请在浏览器中打开页面 http://${EDUSOHO_SERVER_NAME} 完成EduSoho的安装配置！\n\n\n"
    return 0;
}

printf "==================================\n"
printf "= EduSoho SaaS云主机一键安装脚本 =\n"
printf "==================================\n"

upgrade_system
install_nginx
install_mysql
install_php
install_edusoho
clean_system


