# API Template

[![Build Status](https://travis-ci.org/synapsestudios/api-template.png?branch=master)](https://travis-ci.org/synapsestudios/api-template)

### Initializing the Development Environment
1. `git submodule update --init --recursive`
1. `composer install`
1. `vagrant up`
1. To run PHPCS, run `vendor/bin/phpcs --standard=PSR2 src/*` in the root directory
1. To run unit tests, run `vendor/bin/phpunit` in the root directory
