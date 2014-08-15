# API Template

[![Build Status](https://api.shippable.com/projects/53e54a66bec73bdc0222ec30/badge/master)](https://www.shippable.com/projects/53e54a66bec73bdc0222ec30)

### Initializing the Development Environment
1. Clone this repository `git clone git@github.com:synapsestudios/api-template.git <project-name>`.
1. Run the initialization script `chmod +x initialize.sh; ./initialize.sh;` from the project directory.
1. Create a cookbooks repository for this project (see the [cookbook-template README](https://github.com/synapsestudios/cookbook-template) for details).
1. Add the project cookbooks as a submodule called 'cookbooks' with `git submodule add <git URL> cookbooks`.
1. Change `shippable.yml` to include your slack org name, slack channel to report to and [generate a secure value for the slack token](http://blog.shippable.com/devops-chat-a-simple-way-to-use-slack-notifications-with-shippable) and then uncomment the `env` and `after_failure` sections.
1. Update the Build Status widget in this README.
1. Run composer `composer install`.
1. Provision VM `vagrant up`.

### Viewing the Project
1. Get the project static IP address from the Vagrantfile.
1. Create aliases for 'project.vm' and 'lively.project.vm' at that static IP in your computer's hosts file.
1. Access the application at http://project.vm (adjust domain to this project).
1. Access Lively API Documentation at http://lively.project.vm (adjust domain to this project).

### Other Stuff
1. As you build out the API, document it in `config.project.json`. See the [Lively README](https://github.com/synapsestudios/lively) for more details.
1. Personalize the templates in `templates/Email/` according to the needs of your project.
1. To run PHPCS, run `vendor/bin/phpcs --standard=PSR2 src/*` in the root directory.
1. To run unit tests, run `vendor/bin/phpunit` in the root directory.
