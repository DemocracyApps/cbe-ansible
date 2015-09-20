# cbe-ansible
This project consists of Ansible scripts to install and configure infrastructure and
application code for the Community Budget Explorer.
 
The CBE consists of 3 web applications:

- A main CBE application that interacts with both administrative users and budget site users
- A data service that delivers budget and other data to the application
- A data fetcher service that processes uploaded files and API data to create datasets stored and
served by the data service.

This project can be used to install any one of the 3 or all of them at once in a development or production
environment. It is set up to work with Vagrant on a local machine or to set up on a remote 
host. If you do not have a machine from which you can run ansible (i.e., you are on Windows), 
you can use these on the server itself after running a simple bootstrapping script.

## Setting up the initial server state

The initial step is to get the server set up with some basic software. The application has been
optimized for Ubuntu 14.04 LTS - no guarantees that anything here will work on anything else.

You may either stand up a server running Ubuntu 14.04 yourself (e.g., on AWS) or you may use Vagrant
on your local development machine. 

### Getting started on Vagrant

To get started on Vagrant, simply check out this repository, change into the top repository directory,
change the IP address in Vagrantfile if desired and run

    vagrant up

If you can run ansible on your local machine, the remainder of the setup can be run either from there or
by logging into the virtual server and running the commands as described in the next section. If you run
locally and are using Vagrant, the commands will be something like the following:

    ansible-playbook -i "192.168.33.27," --user=vagrant --private-key=~/.vagrant.d/insecure_private_key playbooks/playbook-name.yml

Note that the comma following the IP address is required.

Obviously you'll need to replace the IP address with the one set in Vagrantfile and the appropriate playbook
name. Also, for versions of Vagrant after 1.7, the private key will be in a different location, something
like _.vagrant/machines/[machine name]/[provider]/private_key_. 


### Getting started on a remote server without ansible locally

If you cannot install ansible locally, you'll need to run commands directly on the remote server. To 
bootstrap this, log into the machine, install git, clone this repository, then run:

    /bin/bash bootstrap.sh
    
This will install everything included in the Vagrant initialize.yml script.

You will need to run all the ansible commands on the remote server. To do that, simply use the following

    ansible-playbook -i "localhost," -c local [playbook-name].yml


### Getting started on a remote server using local ansible

All you need to do is run the 'initialize.yml' playbook. For example, on an AWS EC2 instance:

    ansible-playbook -i '[ec2-ip-address],' --private-key [ssh-key-file] --user=ubuntu playbooks/initialize.yml
    
Note that the comma following the IP address is required.

## Application Installation

From this point, instructions for the different environments are identical. We assume that
you know how to run ansible either from your development machine or locally on the application server,
as described above.
 