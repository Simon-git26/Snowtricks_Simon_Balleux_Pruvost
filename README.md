# Snowtricks_Simon_Balleux_Pruvost
Projet 6 de la Formation Developpeur PHP / Symfony de OC


### Installation:

## PHP
- -php -v pour verifier la version de php, version 8 minimum requis

## Composer
- php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
- php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
- php composer-setup.php
- php -r "unlink('composer-setup.php');"


Tester si l'installation de composer a marché : composer --version


## Scoop
- Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
- irm get.scoop.sh | iex

## Symfony CLI
- scoop install symfony-cli

# Verification installation
Une fois toutes les installations effectué rentrer en ligne de commande:

symfony check:requirements ( permet de voir si les les technologies requises sont bien ok)

## Creation de l'application Symfony avec Composer
- composer create-project symfony/skeleton:"6.2.*" my_project_directory
- cd my_project_directory

# Information sur le projet
Executer la commande:
- php bin/console about ( Affiche des informations sur le projet que vous venez de créer )