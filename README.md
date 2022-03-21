<h1>Création d'une API en PHP/Symfony</h1>
<p>Projet perso d'une API respectant les standards REST (stateless, cache, CRUD...).</p>

<h2>Instalation du projet</h2>
<p>1. Clonez ou téléchargez le repository GitHub dans le dossier voulu :</p>

   ```
   git clone https://github.com/benjaminroche4/api.git
   ```

<p>2. Configurez la connexion à la base de données dans le fichier ".env" à la racine du projet :</p>

   ```
   DATABASE_URL="mysql://root:root@127.0.0.1:8889/api?serverVersion=13&charset=utf8"
   ```

<p>3. Téléchargez les dépendances nécessaires grace à composer :</p>

   ```
   composer install
   ```

<p>4. Installez la base de données à l'aide des commandes suivantes dans votre terminal :</p>

   ```
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update
   ```

<p>5. Lancez le serveur à l'aide du terminal avec la commande suivante :</p>

   ```
   symfony server:start
   ```

<p>6. Le projet est maintenant installé. Vous pouvez consulter la documentation de l'API à cette adresse :</p>

   ```
   http://127.0.0.1:8000/api/doc
   ```