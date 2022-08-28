# BarcodeFinder

![BarcodeFinder](https://media.discordapp.net/attachments/1010311719647707226/1010312885496463470/BarcodeFinder.png?width=1000&height=700 "BarcodeFinder")

# Description 
BarcodeFinder est une plateforme web développé sous Symfony 6 permettant d'obtenir les détails d'un produit physique à l'aide de son numéro de code-barres (appelé aussi ISBN/EAN). La plateforme intègre un système d'API, accompagné de sa documentation auto-généré par la bibliothèque API Platform afin de pouvoir réutiliser les différentes données générées depuis une autre application (par exemple pour un scanner de code-barres mobile).

## Fonctionnalités 

BarcodeFinder offre plusieurs fonctionnalités intéressantes aussi bien aux utilisateurs qu'aux développeurs dont voici :

### Description du produit

Fonctionnalité principale de la plateforme, vous trouvez toutes les informations relatives au produit recherché depuis son code-barres !

![Produit](https://media.discordapp.net/attachments/1010311719647707226/1010313836580065290/BarcodeFinder_product.png?width=800&height=500 "Produit")

### API 

Afin de faciliter l'exportation des données d'une application à l'autre, BarcodeFinder intègre un système d'API muni d'une documentation où vous pourrez tester les différentes requêtes disponibles. Un système d'authentification de compte par token JWT est également intégré afin de réaliser vos tests en toutes sécurités.

![API](https://media.discordapp.net/attachments/1010311719647707226/1010311913147748412/BarcodeFile-API-API-Platform.png?width=800&height=500 "API")

### Bibliothèque

Chaque utilisateur dispose d'une bibliothèque personnelle à laquelle il est possible d'enregistrer n'importe quel produit recherché depuis la barre de recherche de la plateforme. 
Cela permet de retrouver plus facilement lors des prochaines visites les produits trouvés qui semble être intéressants.

![Bibliotheque](https://media.discordapp.net/attachments/1010311719647707226/1010311913772679188/BarcodeFinder-Mes-elements-enregistrees.png?width=800&height=500 "Bibliotheque")

### Système tarifaire

> A venir

# Outils requis

Avant de procéder à l'étape d'installation du projet, veuillez vous prémunir au minimum des éléments suivants :

- Un serveur web PHP 8.1 minimum
    - Les modules PHP [Imagick](https://www.php.net/manual/fr/book.imagick.php) et [gd-lib](https://www.php.net/manual/fr/book.image.php) doivent être installées et activer depuis votre fichier php.ini  
- Une base de données SQL (PostgresSQL ou MySQL)
- La bibliothèque de dépendances PHP [Composer](https://getcomposer.org/)
- Un serveur de mail SMTP (pour l'envoi du mail de confirmation de compte et réinitialisation de mot de passe)

# Installation

Pour commencer déplacez l'ensemble des fichiers du projet vers votre le FTP de votre serveur.

## 1. Configuration du serveur

### Sous Apache2

1. Installer la dépendance PHP suivante : `composer require symfony/apache-pack`

2. Modifier le fichier .htaccess depuis la racine du projet, précédemment généré par la dépendance, de la manière suivante :

```conf
<VirtualHost *:80>
    ServerName domain.tld
    ServerAlias www.domain.tld

    DocumentRoot /var/www/barcodefinder/public
    <Directory /var/www/barcodefinder/public>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>

    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined
</VirtualHost>
```

3. Une fois terminé, sauvegardé le fichier (relancer apache2 si besoin)

### Sous Nginx

En utilisant Nginx l'avantage sera que vous n'aurez pas besoin spécialement de .htaccess pour pointer le projet vers le répertoire /public. Il suffira juste de modifier le bloc serveur du domaine de la manière suivante :

1. Rendez-vous depuis le répertoire de configuration de votre site sous Nginx (ex : /etc/nginx/sites-available)

2. Modifier le fichier .conf qui correspond au nom de domaine où BarcodeFinder sera installé et et pointer votre nom de domaine vers le répertoire /public (root).

```conf
server {
    server_name domain.tld www.domain.tld;
    root /var/www/project/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
}
```

3. Sauvegarder et rédémarrer nginx (`systemctl restart nginx`)

## 2. Installation des dépendances PHP

Dirigez-vous vers la racine du projet et tapez la commande composer suivante : `composer install`

Cela installera l'ensemble des dépendances PHP (via le répertoire /vendor) nécessaire au bon fonctionnement de la plateforme. Cette opération peut prendre de quelques secondes à quelques minutes selon votre débit internet.

## Configuration de la base de données

> Avant de commencer, assurez-vous d'avoir un serveur PostgresSQL ou MySQL fonctionnel avec des identifiants valides et des droits suffisants pour la création de la base de données (via Doctrine).

1. Editer le fichier ".env" situé à la racine du projet et décommenter l'une des variable DATABASE_URL suivantes en mettant les bons identifiants de votre base :

```conf
# Pour un serveur MySQL
# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"
# Pour une serveur PostgresSQL
# DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8"
```

2. Lancer la commande de création de la base de données : `php bin/console doctrine:database:create`

3. Lancer la commande de création des entités (tables) SQL : `php bin/console make:migration`

4. Lancer la commande d'exécution des migrations (voir les requêtes effectuées depuis le fichier de migrations créer depuis le répertoire "migrations" : `php bin/console doctrine:migrations:migrate`

## Configuration du serveur de mail

> Avant de commencer, assurez-vous d'avoir un serveur de mail SMTP fonctionnel avec des identifiants valides et avec des droits d'accès suffisants 

1. Editer le fichier ".env" situé à la racine du projet et modifier la ligne suivante en remplaçant les identifiants par les votres (user, password, hostname, port) :

```conf
MAILER_DSN=smtp://user:password@hostname:port
```

2. Sauvegarder le fichier

## Génération des clés SSL

Afin de gébérer vos clés SSL, tapez la commande suivante : `php bin/console lexik:jwt:generate-keypair`
Cela devrait vous créer deux certificats SSL depuis le répertoire "config/jwt". 

🎉 Félicitations ! Désormais le projet devrait être fonctionnel et visualisable depuis votre site web ! 🎉
