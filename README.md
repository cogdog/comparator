# The Comparator Wordpress Theme
by Alan Levine http://cogdog.info/ or http://cogdogblog.com/

## What is this?
This Wordpress Theme allows you run a version of [The Comparator](http://splot.ca/comparator/) a most likely buggy implementation of a hosted service for creating "before/after" jQuery powered web things.

![](images/sketch-to-painting.jpg "Sketch to Painting")

It allows two images to be compared by using a slider. The above one can be seen at http://splot.ca/comparator/made/36/ which offers links to use directly or embed code that may or may not work (hey, it's all alpha, baby)

These allow people to create the functionality that can be linked to on your site or copy an embed code to use elsewhere.

## How to Install
I will make the big leap in that you have a self hosted Wordpress site and can install themes. The Comparator is a child theme based on [Wordpress Bootstrap](https://github.com/320press/wordpress-bootstrap) You can grab the most current version from there, I provide the one used to create this first version.

Very very crucial. Do not just use the zip of this repo as a theme upload. It will not work. If you are uploading in the wordpress admin, you will need to make separate zips of the two themes (comparator and wordpress-bootstrap, and upload each.

In addition the site uses the [Remove Dashboard Access plugin[(https://wordpress.org/plugins/remove-dashboard-access-for-non-admins/) which can be installed directly in your site, but a copy is provide just for the sake of completedness.

The following Wordpress Pages will need to be created on your site:

* **About** (slug name=about; I use this as a static front page on the site) This actually will embed a working copy below whatever introductory material you created, like http://splot.ca/comparator/about You should create at least one demo version first, and edit line 5 of **page-about.php** to reference this page by title.
* **Embed** (slug name=embed). No content needed, this will provide functionality to embed the things. 
* **Make** (slug name=make) This is the form used to make a Comparator, any content will appear at the top above the form. Example http://splot.ca/comparator/make
* **Random** (slug name=random). No content needed, this will provide functionality generate a link that displays a random Comparator. Exmaple http://splot.ca/comparator/random

Create a user account with Author capability. Edit functions.php near **function comparator_autologin()** to use these credentials for the auto login.

Much of this complexity can be simplified with making values as theme options. Maybe in the next version.

Maybe.





te
