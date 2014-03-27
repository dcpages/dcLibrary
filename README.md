# API Template

[![Build Status](https://travis-ci.org/synapsestudios/api-template.svg?branch=master)](https://travis-ci.org/synapsestudios/api-template)

### Initializing the Development Environment
1. `git submodule update --init --recursive`
1. `composer install`
1. `vagrant up`
1. To run PHPCS, run `vendor/bin/phpcs --standard=PSR2 src/*` in the root directory
1. To run unit tests, run `vendor/bin/phpunit` in the root directory

### Customizing the Template for a Project
1. Set the static IP to be used for your project in `Vagrantfile`. Set an alias for this in your hosts file.
1. Modify any applicable config files in `config/`.
1. Modify the *I/O Docs* files in `docs/`.
 - Rename `project.json` to your project's name + `.json`. (Must be lowercase.)
 - Modify `apiconfig.json` so that the key of the JavaScript object is your project's name, exactly as used above.
 - Modify both URLs in `apiconfig.json` to be your project's API URL.
 - Edit `name` in `apiconfig.json` to your project's name.
1. Rename the `Application` namespace to a namespace more appropriate for your application.
 - Rename the `src/Application` folder.
 - Change the namespace in `Application/Routes.php`, `Application/Services.php`, and `Application/Upgrades/Install.php` to match the new namespace.
 - In `bootstrap.php` modify the namespace of `Application\Routes` and `Application\Services`.
1. Personalize the templates in `templates/Email/` according to the needs of your project.

### Viewing I/O Docs API Documentation
1. `vagrant ssh`
1. `cd ~/iodocs`
1. `npm start`
1. Point browser to http://project.vm:3000 (Adjust domain to whatever points to the project.)

See the [I/O Docs README](https://github.com/mashery/iodocs) for more details.
