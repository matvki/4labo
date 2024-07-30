# Projet Symfony 7.1

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- [PHP 8.1](https://www.php.net/releases/8.1/en.php) ou plus récent
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) et [npm](https://www.npmjs.com/)
- [MySQL](https://dev.mysql.com/downloads/mysql/) ou [MariaDB](https://mariadb.org/)

## Installation des packages nécessaires via `apt` ou `apt-get`
```sh
sudo apt update
sudo apt install -y php php-cli php-mbstring php-xml php-zip php-curl php-mysql mysql-server git unzip
```
### Installation du projet

#### Clone du repos Git
```shell
git clone https://github.com/votre-utilisateur/votre-projet.git
cd votre-projet
```
#### Installation des dépendances PHP avec Composer:
```Shell
composer install
```
#### Installation des dépendances Javascript avec npm:
```Shell
npm install
```
#### Configuration de la base de données

- Connectez-vous a la base de données MySQL ou MariaDB avec l'utilisateur root, dans l'exemple nous utiliserons MySQL,
```Shell
sudo mysql -u root -p
```
- Création de la base de donnée du site ainsi que d'un utilisateur avec le bon niveau de droit pour le site,
```SQL
CREATE DATABASE 4labo;
CREATE USER '4labo_user'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
GRANT ALL PRIVILEGES ON 4labo.* TO '4labo_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```
- Ajout des logs de connexion dans Symfony, pour cela nous allons créer un `.env.local` qui ne sera pas sauvegarder sur le repo git,
```Shell
cp .env .env.local
```
- Modifier le fichier `.env.local` avec les logs de connexion a la bonne base de donnée,
```Makefile
DATABASE_URL="mysql://4labo_user:mot_de_passe_securise@127.0.0.1:3306/4labo"
```
#### Initialisation de la base de données
```Shell
php bin/console doctrine:migrations:migrate
```
#### Compilation des assets
```Shell
npm run build
```
## Accéder à l'application

Ouvrez votre navigateur et accédez à http://{ip_de_votre_serveur}