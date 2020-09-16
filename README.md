# QUTCapstone2020-Payment-Plugin

* [What is this?](#what-is-this)
* [Why design a payment plugin?](#Why-design-a-payment-plugin)
* [Repositories](#Repositories)
* [Version requirement](#Version-requirement)
* [Installation](#installation)

## What is this?
This is a moodle plugin which adds a Paypal/credit card payment gateway to your courses.

## Why design a payment plugin
There are currently no reliable and easy to use payment plugins within the moodle plugin directory.
Althougth, there are definately payment plugins that exist for moodle currently for example:

(Default moodle intergrated Paypal payment gateway) https://github.com/moodle/moodle/tree/master/enrol/paypal.

(Moodle payment software run through https://stripe.com) https://moodle.org/plugins/enrol_stripepayment.

The major difference our software and the two examples listed above is the simplicity and flexibility of our plugin.
This plugin can be easily fitted to the needs of the user without the need of making an account and going through a process online.
This plugin is all moodle based.

## Repositories
There are currently two required repositories to make this plugin work.

The repository that is responsible for setting up the gate ways and managing all the data:

https://github.com/Peterburnett/QUTCapstone2020 

The other repository responsible for displaying the website hook:

https://github.com/Peterburnett/QUTCapstone2020-enrol

## Version requirement

To run this plugin with the enrolment plugin (https://github.com/Peterburnett/QUTCapstone2020-enrol), the enrolement plugin need to be atleast at version 2020082600.
The enrolment plugin  requires a payment plugin version to be 2020042101+.

## Installation

Step 1: Installing the plugin
-------------------------------
Download the .zip file the from the `master` https://github.com/Peterburnett/QUTCapstone2020.

Extract this into /yourmoodle/admin/tool/paymentplugin/

Step 2: Installing the sub-plugin
-------------------------------
Download the .zip file the from the `master` https://github.com/Peterburnett/QUTCapstone2020-enrol.

Extract this into /yourmoodle/admin/enrol/payment

Step 3: Upgrading moodle
-------------------------------
Then run the moodle upgrade as normal.

https://docs.moodle.org/en/Installing_plugins

## Support

This plugin was developed by QUT MAHQ Develeopers 

With the support of 
https://www.catalyst-au.net/

<img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_saml2/master/pix/catalyst-logo.svg" width="400">
