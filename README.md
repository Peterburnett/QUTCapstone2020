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
Plugins that lock a course behind a payment already exist:

(Default moodle intergrated Paypal payment gateway) https://github.com/moodle/moodle/tree/master/enrol/paypal.

(Moodle payment software run through https://stripe.com) https://moodle.org/plugins/enrol_stripepayment.

However, the major difference between our software and the two examples listed above is the flexibility of our plugin.
This plugin can accomodate for multiple payment gateways through the use of subplugins.

## Repositories
There are currently two plugins required to function properly.

The admin tool plugin responsible for handling the payment gateways and payment:

https://github.com/Peterburnett/QUTCapstone2020 

The enrolment plugin responsible for enrolment:

https://github.com/Peterburnett/QUTCapstone2020-enrol

The enrolment plugin is dependent on the admin tool plugin.

## Version requirement

The admin tool plugin requires Moodle 3.5.13 at the very least.

## Installation

Step 1: Installing the main plugin
-------------------------------
Download the .zip file the from the `master` https://github.com/Peterburnett/QUTCapstone2020.

Extract this into /yourmoodle/admin/tool/paymentplugin/

Step 2: Installing the enrolment plugin
-------------------------------
Download the .zip file from the `master` https://github.com/Peterburnett/QUTCapstone2020-enrol.

Extract this into /yourmoodle/admin/enrol/payment

Step 3: Upgrading moodle
-------------------------------
Then run the moodle upgrade as normal.

https://docs.moodle.org/en/Installing_plugins

## How to use these plugins

1. Enable the enrolment plugin.
2. Add an instance of the enrolment plugin to a course that will require payment.
3. Set the course price from Course Administration -> Payment Settings.
4. In the admin tool plugin settings, enable payment gateways that the students will be able to use.
5. Change admin tool plugin settings and payment gateway subplugin settings as necessary.
6. The course will now display a button to unenrolled students, redirecting them to a page where the course can be purchased through the payment gateways.

## PayPal payment gateway subplugin

The admin tool plugin comes with a PayPal subplugin pre-installed. This subplugin requires the admin to have a PayPal business account.

## Support

This plugin was developed by QUT MAHQ Developers 

With the support of Catalyst IT Australia:
https://www.catalyst-au.net/

<img alt="Catalyst IT" src="https://cdn.rawgit.com/CatalystIT-AU/moodle-auth_saml2/master/pix/catalyst-logo.svg" width="400">
