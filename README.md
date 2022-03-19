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


Terminate the last batch job with "CTRL + C", then simply start the project with

```
symfony server:start
```

Generate database schema

```
php bin/console doctrine:migrations:migrate
```

### Todo list (To be updated): 

- [ ] Modify main page (CSS)
- [X] Users (Admin / User)
- [X] Authentification
- [X] Register
- [ ] Certifications Creation / Modify / Delete
- [ ] Certifications Enroll
