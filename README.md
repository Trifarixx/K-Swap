# 🎵 K-Swap

K-Swap est une plateforme communautaire et une base de données interactive dédiée à l'univers de la K-Pop et de la J-Pop. Retrouvez les discographies de vos artistes préférés, suivez l'actualité des groupes et idoles, et interagissez avec les autres fans via un fil d'actualité et un système de critiques.

![Badge Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white)
![Badge PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Badge MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## ✨ Fonctionnalités

- 🗂️ **Base de Données Musique** : Gestion complète des Artistes, Groupes, Idols, Albums et Morceaux.
- 📱 **Feed Social** : Fil d'actualité pour partager du contenu et interagir avec la communauté.
- ⭐ **Avis & Notes** : Laissez des critiques sur les albums et commenter les posts des autres utilisateurs.
- 🔍 **Recherche Avancée** : Moteur de recherche performant pour le catalogue musical.
- 🔗 **Intégrations API** : YouTube (clips vidéo).
- 👤 **Gestion de Profil** : Consulter vos posts et modifier vos informations de profil.

## 🛠 informations Techniques

**Frontend:** Twig, SASS/SCSS, Webpack Encore, Stimulus & Turbo (Symfony UX)

**Backend:** PHP 8.2+, Symfony 6.4+/7.x, Doctrine ORM

**Infrastructure:** MySQL/MariaDB

**Cahier des charges:** Présent à la racine du projet

**Dump SQL** Présent dans le fichier 'Jeu de données'

Clonez le projet

```bash
  git clone https://github.com/Trifarixx/K-Swap.git
```

Installer les dépendances 

```bash
  composer install
  npm install
```

## 🗄️ Déploiement & Migration BDD (VM Debian)

En utilisant le logiciel FileZilla, mettre le fichier .gz à la racine de la Machine Virtuel Debian

Ensuite exécuter les commandes suivantes : 

```bash
sudo apt update && sudo apt install unzip
```
Maintenant décomprésser le fichier .gz

```bash
gunzip Kswap.sql.gz
```

Ensuite il faut créer la base de donnée et l'utilisateur

```bash
sudo mysql
create databases KSwap;
create user 'yoasobi'@'ip_de_la_machine_virtuel' identified by 'mot_de_passe_à_modifier';
grant all privileges on *.* to 'yoasobi'@1'ip_de_la_machine_virtuel';
flush privileges;
exit
```
Maintenant l'Importation

```bash
sudo mysql KSwap --force < Kswap.sql
```
## ⚠️ informations Complémentaires

**Dans le cas ou 'composer install' ne fonctionne pas :**
Se rendre sur c:\tools\php83 puis dans le php.ini SI le 'php.ini' n'est pas présent dupliquer le 'php.ini- development' puis renommer le 'php.ini' Ensuite ouvrer le dans le bloc note en mode administrateur. Exécuter la commande ctrl + f puis écrire 'fileinfo' et enlever le point virgule devant. 

**Dans le cas ou vous choissisez de ne pas prendre les identifiants données :**  ne pas oublier de se rendre dans le .env pour changer les informations de connexion à la base de données. ex :

```php
DATABASE_URL="mysql://nom_utilisateur:mot_de_passe@ip_de_la_machine_virtuel:3306/nom_de_la_base_de_données?charset=utf8mb4"
```

## 🚀 Lancement de l'application 

Lancer le serveur

```bash
  symfony serve
```
et ctrl + clique sur : http://127.0.0.1:8000/
