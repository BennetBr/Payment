# Payment - A reference project

Payment is a reference project based on a classical [LAMP stack](https://en.wikipedia.org/wiki/LAMP_%28software_bundle%29) featuring PHP 8.

It is in essence a mockup of a banking webapp which keeps track of banking accounts and transactions between them.

![Screenshot](https://raw.githubusercontent.com/BennetBr/Payment/main/doc/payment.png)

## Intent

Its main intent is to show off some of the web development skills I currently possess. It is developed inside a [Docker](https://www.docker.com) environment and features unit testing with [PHPUnit](https://phpunit.de/) as well as a frontend that gets bundled CSS and JavaScript using [Webpack](https://webpack.js.org/).

It was made in about 25 hours of work time, so expect some roughness around the edges and a charmful lack of polishment.

## How to start it

The easiest way to try Payment out is by using `docker-compose`, although it is not necessary to use Docker at all. If you intend to test the Project without it, consider taking a peek through the `.docker` folder - it contains all the server side settings in their respective formats. It should be fairly standard, but take a look if something isn't behaving as planned.

Unfortunately a known limitation is that the document root cannot be set from the command line options, so you will have to edit that line in the `phpunit.xml` configuration file since it is written with the Docker environment in mind.

This guide however will focus on using `docker-compose`, since it is by far the easiest way.

## Running with docker-compose

All you need to do is clone this repository and then run `docker-compose up mysql php httpd`. After it is done starting, you can see the result at [localhost:80](http://localhost:80), If you want to use a different port, refer to the httpd section of `docker-compose.yml` and change the port binding on the left hand side.

If you wish to run the unit tests, ensure that the mysql server is running (using the previously mentioned `docker-compose up` statement) and simply execute `docker-compose run tests`.