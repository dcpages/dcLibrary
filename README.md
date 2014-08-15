# API Template

[![Build Status](https://api.shippable.com/projects/53e54a66bec73bdc0222ec30/badge/master)](https://www.shippable.com/projects/53e54a66bec73bdc0222ec30)

### Customizing the Template for a Project
1. Update the Build Status widget in this README.
1. Update all `@todo`'s in the repository
- Change `shippable.yml` to include your slack org name, slack channel to report to and [generate a secure value for the slack token](http://blog.shippable.com/devops-chat-a-simple-way-to-use-slack-notifications-with-shippable) and then uncomment the `env` and `after_failure` sections.
- Change the QA VM box name
- Change the QA APP_ENV variable
- Change the QA deploy domain
- Change the static IP to one specific to this project and update in the Synapse wiki
1. As you build out the API, document it in `config.project.json`. See the [Lively README](https://github.com/synapsestudios/lively) for more details.
1. Personalize the templates in `templates/Email/` according to the needs of your project.

### Initializing the Development Environment
1. Create a cookbooks repository for this project and add as a submodule called `cookbooks`. See the [cookbook-template README](https://github.com/synapsestudios/cookbook-template) for details.
1. `composer install`
1. `vagrant up`

### Viewing the Project
1. Create an alias for project.vm at the static IP from `Vagrantfile` in your computer's hosts file.
1. Point browser to http://project.vm (adjust domain to whateer points to the project)

### Viewing Lively API Documentation
1. Create an alias for `lively.project.vm` at the static IP from `Vagrantfile` in your computer's hosts file.
1. Point browser to http://lively.project.vm (Adjust domain to whatever points to the project.)

### Running Tests
1. To run PHPCS, run `vendor/bin/phpcs --standard=PSR2 src/*` in the root directory
1. To run unit tests, run `vendor/bin/phpunit` in the root directory
