Symfony 2 User Management RESTful API
=====================================

I used a custom dockerize enviroment of symfony 2 using mysql and ngix if 
you want you can take advantage of that follow this link:

[ces0712/dockerize-symfony](https://github.com/ces0712/dockerize-symfony)

The project involves the following bundles:
 
* [FosRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle) very useful bundle to help build endpoints
  and create a restful API

* [FosUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle) very helpful bundle that gives 
  built-in functionality to manage users-groups-roles

* [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle) allow authentication via token JWT

* [Behat 3](https://github.com/Behat/Behat) For testing the endpoints this test are available 
  in the folder /src/AppBundle/Features the context for the test are in /features/AppBundle/Features/Context

To run this project i used:

* PHP-FPM version 7.1.6
* NGIX latest version at the moment
* MYSQL version 5.6

Over Debian Jessie the details of the configuration according to the framework are in the link to dockerize-symfony above
To run this project you will need to [generate the ssh keys](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#prerequisites) files in the path: /app/var/jwt or where you choose 
just changing the parameters.yml file

The environment setup for testing is http://symfony.dev/app_acceptance.php

To Run the test just execute bin/behat in the root of the project

The test of interest are:

* group.feature
* user.feature
* register.feature

The UML diagram with the domain model with the process require are in the Uml.pdf and uml.png files in the root folder
The database model you can find it in the databaseModel.pdf and databaseModel.png files also in the root folder
