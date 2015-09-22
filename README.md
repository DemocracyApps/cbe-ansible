# cbe-ansible
This project consists of Ansible scripts to install and configure infrastructure and
application code for the Community Budget Explorer.
 
The CBE consists of 2 web applications:

- A main CBE application that interacts with both administrative users and budget site users
- A data service that delivers budget and other data to the application and processes uploaded files and API data

This project can be used to install either of the two or both of them at once in a development or production
environment. It is set up to work with Vagrant on a local machine or to set up on a remote 
host. 

## Get started

Setting up an environment is straightforward, but may take some time, particularly installation steps via composer and npm.

### Getting started on Vagrant

To get started on Vagrant, simply check out this repository, change the IP address in Vagrantfile if desired and run

    vagrant up

from inside the main repository directory. Then _vagrant ssh_ into the virtual server and run the following:

    cd /var/www
    ansible-playbook -i "localhost," -c local playbooks/all.yml

Note that the comma following _localhost_ is required.


### Getting started on a remote server

The first step is to set up a server running Ubuntu 14.04 LTS - no guarantees that anything here will work on other systems.

Log in to the server, install git, clone this repository, change to the main repository directory and run:

    /bin/bash bootstrap.sh
    ansible-playbook -i "localhost," -c local playbooks/all.yml

Note that the comma following _localhost_ is required.

