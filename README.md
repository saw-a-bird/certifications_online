# projectSymfony


After installing the project with

```
git clone https://github.com/pokerfce/projectSymfony.git
```

You'll have to run the command to ensure that all of the needed vendor libraries are downloaded

```
composer install 
```

To generate the manifest.json file, execute the below commands:

```
yarn add --dev @symfony/webpack-encore

yarn add webpack-notifier --dev

yarn encore dev
```

Then, update your database with 

```
php bin/console doctrine:migrations:migrate
```

Start the project with

```
symfony server:start
```


### Todo list (To be updated): 

- [X] Users (Roles, Authentification, Register, Edit Information)
- [X] Fournisseurs (List / Creation / Modify / Delete)
- [X] Certifications (Creation / Modify / Delete)
- [X] Certifications List (Pagination, Search by Title / Provider)

- [ ] Certif-User Enroll
- [ ] Certif-User Comment
- [ ] Certif-User Stars
