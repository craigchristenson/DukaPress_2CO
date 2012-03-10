=== DukaPress ===
Contributors: NetMadeEz, moshthepitt
Donate link: http://dukapress.org/about/
Tags: shopping cart, web shop, cart, shop, Worldpay, Paypal, Alertpay, paypal, e-commerce, ecommerce, MPESA, ZAP, yuCash, Mobile Payments,online duka, duka, online shop, JQZoom, Multiple Currencies

Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 2.3.5

DukaPress is an open source e-commerce solution built for Wordpress.

== Description ==

DukaPress is open source software that can be used to build online shops quickly and easily. DukaPress is built on top of Wordpress, a world class content management system. DukaPress is built to be both simple and elegant yet powerful and scalable.

Main Features:

* You can sell tangible regular products;
* You can sell tangible products with selectable options (size, colour, etc);
* You can sell digital products;
* Choose between a normal shop mode and a catalogue mode;
* Numerous payment processing options including Paypal, Alertpay and Mobile Phone payments;
* You can set up 'affiliate' products which redirect to othr products you want to promte (affiliate marketing)
* Ability to work with multiple currencies
* Printable invoices;
* One-page checkout;
* Elegant discount coupon management;
* A myriad of shipping processing options;
* Simple user management and customer order logs;
* Custom GUI (Graphical User Interface) for product management;
* Easy to translate into your own language

View more features: [DukaPress](http://dukapress.org/ "Your favorite e-commerce software")

DukaPress Documentation: [DukaPress Documentation](http://dukapress.org/docs/ "Your favorite e-commerce software documentation")

View a DukaPress Demo Shop: [DukaPress Demo](http://dukapress.org/demo/ "Your favorite e-commerce software")

Premium Addons:

1. [Simple Product SlideShow](http://dukapress.com/products/simple-slideshow/ "Simple Product SlideShow")
1. [DukaPress Styles](http://dukapress.com/products/styles/ "DukaPress Styles - make DukaPress look good without getting a new theme")
1. [Shipping Pro](http://dukapress.com/products/shipping-pro/ "Shipping Pro - location-based shipping")
1. [List View](http://dukapress.com/products/list-view/ "DukaPress List View - display your products in a handy list")

Premium Themes:

1. [The Duka Theme](http://madoido.com/products/duka-theme/ "The Duka Theme")

== Installation ==

1. Upload the DukaPress folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Make sure your web host allows scripts like timthumb.php to run on your site.  If they don't, your DukaPress images will be broken.
1. After this, visit the [DukaPress documentation](http://dukapress.org/docs/ "DukaPress documentation")


== Frequently Asked Questions ==

= What does DukaPress mean? =

'Duka' is the Swahili word for shop.  Loosely, DukaPress means "shoppress".  We like to think it means the most complete and usable e-commerce solution for wordpress, though.

= What don't the images work? =

Please read this first: [We live in a bad world](http://dukapress.org/blog/2010/09/23/we-live-in-a-bad-world/ "Why images don't work").

Unfortunately, for security reasons, web hosts sometimes disable timthumb from working.  This is the script that handles images in DukaPress.  To fix this, kindly ask your webhost to allow timthumb to work.  All the good webhosts will do this for you in two minutes!

[Look here for hosts that we know work with DukaPress.](http://dukapress.org/download/)

= Why is the make payment button not working? =

Nine out of ten times, this is because there is a javascript error somewhere on your site.  The first place to look is your theme - try and run DukaPress using the default WordPress theme to confirm if it is your theme that is failing you.

Another reason is usually the pdf folder inside of DukaPress. Please try make it writable or disable the PDF invoice option from DukaPress basic settings.

= Why doesn't DukaPress work for me?  It seems to work for everyone else =

No.  Nothing is wrong with you. :)

We test DukaPress on a large number of different server set-ups and envrionments and we are satisfied that it does work in these environments.  However, the number of different environments 'out there' is infinite and we cannot possibly test on every single environment.  If everything that you try fails to work, perharps you should move your site to one of the more common web hosts?  [Look here for hosts that we know work with DukaPress.](http://dukapress.org/download/)

= Why isn't the Grid Display working? =

You currently HAVE to have at least one custom field per prodct in order for those products to show up in the grid display properly.

= Why am I getting multiple/many emails when an order is placed? =

If you are getting many emails with the subject “Receipt of Order No: xxxxx“, then it may be because you have PayPal IPN settings turned on. Please turn them off.

= Why isn't the "Inquiry"/"Catalogue mode" working properly? =

Right now, it works very similarly to the regular shop mode - i.e. when people click on the "Inquire" button, it adds the product(s) to the shopping cart.  When the site vistors go to checkout, they will then be presented with a form which they fill to inquire about products in the cart.  You therefore HAVE to have your shopping cart widget displaying somewhere for the "Inquire" button to work.

= Why is nothing happening when I press "Add to cart"?  Why is the AJAX not working? =

The cart should be inside DIV with class="dpsc-shopping-cart".

Just put the cart code inside DIV tags to look like:
div class="dpsc-shopping-cart">cart code</div

= Where can I get more help? =

Please visit: [Our Forums](http://dukapress.org/forums/ "Our Forums")


== Screenshots ==

[View Screenshots](http://www.flickr.com/photos/moshthepitt/sets/72157624534741496/ "DukaPress screenshots")

== Changelog ==

= 2.3.5 =
* Added "affiliate mode".
* Introduced a real "out of stock" button. 
* Improved product variations. 
* Added an order summary to the "thank you" page. 
* Improved order log. 
* Crushed a lot of bugs

= 2.3.4 =
Some small, but annoying, bugs have been fixed.  Timthumb updated for continued security. 

= 2.3.3 =
Fixing timthumb security vulnerability (just replaced timthumb.php with version 2.5) 

= 2.3.2 =
Fixing timthumb security vulnerability (just one line of code) 

= 2.3.1 =
Fixing extremely small bug (just one line of code)

= 2.3 =
* Enhanced DukaPress security greatly by making it impossible for people to order items that are out of stock or to order more items than the inventory quantity.
* Introduced a “Buy Now” button that redirects directly to PayPal instead of taking you to the checkout page. 
* Introduced a “Buy Now” button that redirects directly to PayPal instead of taking you to the checkout page. 
* Added new shortcodes. 
* Added the ability for shop admins to edit the content of emails sent out to customers from within DukaPress settings. 
* Updated timthumb.php to fix numerous security vulnerabilities associated with it.
* Crushed a lot of bugs

= 2.2 =
* Fixed a serious security issue that allowed people to use firebug to change prices of goods
* Added description field to Grid View Shortcode
* Added DukaPress search widget
* Crushed a lot of bugs
* Fixed some typos

= 2.1 =
* Crushed many bugs
* Added customer order logs
* Made it possible to delete order logs
* Made the PDF invoice optional
* Paginated all order logs
* Fixed some typos

= 2.0 =
* Crushed a lot of puny (and not so puny) bugs
* Added internationalization support
* Added new product display widgets
* Added customer managament
* Improved Grid display widget
* Added checkout field validation
* created customer order logs area
* Improved PDF invoice
* Paginated admin order log

= 1.3.2.1 =
* A quick bug fix release for a bug that affected the WordPress post edit screen.

= 1.3.2 =
* A quick bug fix release. This fix removes the hard coded image dimensions in the dukapress.js file.

= 1.3.1 =
* A quick bug fix release to fix a JQuery bug in 1.3.0

= 1.3.0 =
* Crushed even more annoying little bugs
* Improved DukaPress UI
* Made it possible to define the sizes of images displayed by DukaPress
* Improved the mobile payment processor to be able to handle any system in the world
* Enabled DukaPress to work with multiple currencies
* Added JQZoom suuport
* Added a simple way to notify customers that something was added to the cart

= 1.2.1 =
* Made the currency symbol viewable on grid view and single product pages
* Crushed a lot of annoying little bugs

= 1.2.0 =
* Added support for custom post types
* Added GUI to product management so that one does not have to use custom fields
* Fixed a bug whereby one could not update the number of itens in the cart on the checkout page
* Fixed some wordings on the emails that DukaPress sends out

= 1.0.1 =
* Added pagination to Grid View
* Fixed a bug affecting stock/inventory management- buyers now cannot buy out of stock items
* A bit of the code is now modularised

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 2.3.5 =
Major release.  Added many new features as well as fixed bugs.

= 2.3.4 =
Bugfix release.

= 2.3.3 =
Security update.

= 2.3.2 =
Security update.

= 2.3.1 =
Very minor bug fix

= 2.3 =
Major release containing many new features, security updates and bug fixes.

= 2.2 =
This is an important update containing a major security fix as well as numerous bug fixes.

= 2.1 =
This is primarily a bug fix release meant to make DukaPress run better and without any issues.  The ability to turn off PDF invoices, in particular, will solve the issues of the "make payment" button not working for many people.

= 2.0 =
Our most major release to date, featuring tons of bugfixes and many new features.

= 1.3.2.1 =
Nothin' to see here, bug crushing.

= 1.3.2 =
Some bugs crushed.  Aren't you glad we're taking care of you?  Keep 'em bugs away, I say.

= 1.3.1 =
A quick bug fix release to fix a JQuery bug in 1.3.0

= 1.3.0 =
Moving along swiftly on our quest to be the best WordPress e-commerce tool.  This version not only fixes pesky bugs, it adds a ton of new features and improvements!

= 1.2.1 =
Just a nice little bug fix release!

= 1.2.0 =
If you did not love us before today, then you simply must have a look at our offerings!  Intorducing a custom product management GUI and support for custom post types.

= 1.0.1 =
DukaPress just got better!  We fixed some bugs and added one new feature. Yay!

= 1.0 =
DukaPress is brand new.  As such you won't be upgrading but joining our handsomely awesome family.