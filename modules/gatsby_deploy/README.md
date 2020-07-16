# Gatsby Deploy

This is a matching module for the [Gatsby plugin trigger deploy](https://www.npmjs.com/package/gatsby-plugin-trigger-deploy).
In case the plugin does not match you're work flow, then you skip on this one,
if it's does match your workflow then stick arround and have a look on how to
set it up.

## But, what supposed to be workflow?
The work flow should be the next one - you're gatsby development mode is
running, and the content authors can see how gatsby really look with the content
they created. Now, you want to provide a way to deploy the site and go live with
the new content without the need for a CLI.

## Setting up

After installing the module, you'll need to set up a front end environment of
the `build hook` module. Follow the next steps>?
1. Go to `Administration > Configuration > Build hooks`
(or `admin/config/build_hooks/frontend_environment`) and click on the
`Add a frontend environment` button.
2. Choose `Gatsby plugin trigger deploy`
3. Let's talk about the fields:
    * The `label` and `url` fields has no effect on the flow, but you need to
        choose something meaningful.
    * The `deployment stragety` should be `Manually` though you can pick other
        stuff as your organization flow.
    * The `Build hook url` should be the address in which your development
        gatsby server is serve along with the `/deploy` at the end. For example:
        `http://localhost:8000/deploy`.
    * The `secret key` should be the secret key you set when setting up the
        plugin in gatsby. **It's must be unique and there's cannot be another
        environment which use the same secret key!**

## How to use
In case you set the `deployment strategy` to `When content is updated` so you
don't need to worry about anything. If you set it to manual, then any time you
want to deploy the content you'll need to click on `developments` in the toolbar,
go to environment you create (which can be identified by the name you picked)
and click on `Start a new deployment to the <name>`. This will trigger the
deployment command and once it's finished you'll see a record on the
`recent deployments`.
