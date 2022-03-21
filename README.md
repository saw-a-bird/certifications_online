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

### Todo list (To be updated): 

- [X] Users (Roles, Authentification, Register, Edit Information)
- [ ] Categories (List / Creation / Modify / Delete)
- [ ] Certifications (List / Creation / Modify / Delete)

- [ ] Certif-User Enroll
- [ ] Certif-User Comment
- [ ] Certif-User Stars
