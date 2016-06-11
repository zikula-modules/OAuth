OAuth
=====

OAuth Authentication for Zikula 1.4.3+

alpha status - do not use.

Setting up the providers
========================

Each provider (or authentication method) requires you to set up an 'application' for each website that employs this module.
Typically you create the application at their website and are provided with a `clientId` and a `clientSecret`.
In order to function, this module must have those two strings entered in the settings for each method.

In the creation of the application you will be asked for a "callback Url". Set this to `http://www.yourwebsite.com/path/to/login`

Github
------

Application setup at https://github.com/settings/applications/new


Google
------

Application setup at https://console.developers.google.com/

You must also enable the Google+ Api