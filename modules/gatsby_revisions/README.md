# Gatsby revisions

The Gatsby revision module provide support for the
[Gatsby revision plugin](https://www.npmjs.com/package/gatsby-plugin-revisions)
for the [Gatsby JS](https://gatsbyjs.org/) static site generator.

## Requirements

### Drupal
No need for fancy stuff. You'll need to enable the `Gatsby orchestrator` module
and follow the steps there.

### GatsbyJS
In the GatsbyJS project, install the plugin and set the `eventsAddressBroadcast`
settings, by the plugin instructions, to an address that goes by
`http://DRUPAL-ADDRESS/gatsby-revisions/event-listener`.

## Workflow
Imagine the next flow: you have a big Drupal site which updates once a while or
very often. Gtasby with a huge amount of content might take a while to compile.
In one time, you removed important content or made a mistake in a piece of
content. Recompile the page might tak a while and in the mean while there's a
bad content in your site.

Gatsby revision comes to solve this one. Before posting a new content you can
make a snapshot and then update your content. In case you published bad content
you can revert the site to the snapshot you made and fix the content of your
site.

In order to create a snapshot, go to `admin/content/gatsby-revision` and click
on the `Add gatsby revision`. Add a simple title and short description, not a
mandatory field, and you're good to go.

Now, you can start and create new content and publish it.

If you desire to rollback you can click on the drop down menu of the revision
and click on `revert to this revision`. This will switch the compiled folder
with the snapshot you made and then you can trigger the deploy command.
