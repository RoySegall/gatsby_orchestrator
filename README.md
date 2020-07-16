# Gatsby Orchestrator

The module holds a set of API for managing orchestration events, such as deploy,
in your GatsbyJS developing site and more.

## Installation
You need to have the [GatsbyJS](https://www.drupal.org/project/gatsby) module
enabled. Then, set the GatsbyJS address in the `Gatsby Integration Settings`
page (which serves in the path `admin/config/services/gatsby/settings` of your
Drupal installation).

## Anatomy

There are two submodules:
* Gatsby Revisions - provide a way to maintain a list of revision thus having
backup and rolling back content creation.

* Gatsby deploy - Allows you to trigger the `deploy` command. Best fit for a
self hosted gatsby.
