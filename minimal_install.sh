#!/bin/bash

echo "Doing minimal initial provisioning: ansible, git, curl, python and node"
sudo add-apt-repository -y ppa:ansible/ansible # Repository for latest ansible
sudo apt-get update
sudo apt-get install -y git-core
sudo apt-get install -y curl
sudo apt-get install -y python-psycopg2
sudo apt-get install -y ansible

curl --silent --location https://deb.nodesource.com/setup_4.x | sudo bash -
sudo apt-get install -y nodejs

