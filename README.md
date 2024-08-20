# Getting Started with this test drupal site

This project requires you to have the following installed on your computer
- Docker & DDEV
- PHP 8.1
- Composer

## Installation & Set up

```bash
# Set up the ddev config for the site.
ddev start

# Bring in composer modules and dependencies as well as vendor directory.
ddev composer install

# Install a fresh site from the existing config on the site
ddev drush site:install --existing-config -y

# Create Content editor user and add role
ddev drush user:create ContentEditor --mail='example@example.com' --password='P@ssw0rd' && ddev drush user:role:add 'content_editor' ContentEditor

# Create one time login link to log into the site as an admin.
ddev drush uli
```

## Running the migration
```bash
ddev drush mim csv_source_node_content --execute-dependencies && ddev drush cr
```

## Other Details
To log into the site as a content editor use `ddev drush uli --uid=2` to generate a one time login link or
go to [https://drupal-test-site.ddev.site/user/login](https://drupal-test-site.ddev.site/user/login) The login details
are as follows:

```bash
# username
example@example.com

# Password:
P@ssw0rd
```
