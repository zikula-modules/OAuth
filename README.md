## OAuth

OAuth Authentication for Zikula 1.4.3+

This module is included with Core >= 1.5.0.

If you would like to run it with Zikula Core 1.4.3 - 1.4.99 you must install the vendors using
`composer install`. This is _not required_ for Core >= 1.5.0.

Currently implemented providers:

 - Facebook
 - GitHub
 - Google
 - Instagram
 - LinkedIn

## Setting up the providers

Each provider (or authentication method) requires you to set up an 'application' for each website that employs this module.
Typically you create the application at their website and are provided with a `clientId` and a `clientSecret`.
In order to function, this module must have those two strings entered in the settings for each method.

Further instructions for setting up each provider is on the settings page for that provider.
