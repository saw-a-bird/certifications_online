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

### Todo list: 

- [ ] Modify main page (CSS)
- [ ] Users
- [ ] Login
- [ ] Register
- [ ] Certifications Creation / Modify / Delete
- [ ] Certifications Enroll
