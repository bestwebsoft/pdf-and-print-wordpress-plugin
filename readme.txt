=== PDF & Print ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=1&product_id=94
Tags: archive pdf, generate pdf, generate pdf content, generate post pdf, pdf, pdf and print, pdf button, pdf content, pdf custom post type, pdf page, pdf post, pdf print, pdf print button, pdf print content, pdf print portfolio, pdf print plugin, pdf search results, print, print button, print content, print custom post type, print page, print post, print post content, printing output, shortcode
Requires at least: 3.0
Tested up to: 4.0-beta3
Stable tag: 1.7.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add PDF and Print button to your WordPress website.

== Description ==

PDF & Print allows you in the easiest and most flexible way to create PDF and Print page with adding appropriate buttons to the content. PDF & Print using mpdf library under GPLv2 license.

http://www.youtube.com/watch?v=EM6AEkD9M_s

<a href="http://wordpress.org/plugins/pdf-print/faq/" target="_blank">FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

<a href="http://bestwebsoft.com/plugin/pdf-print-pro/?k=6a544b359e625de8281a635315d84a70" target="_blank">Upgrade to Pro Version</a>

= Features =

* Actions: Ability to create PDF and Print page with adding appropriate buttons to the content.
* Actions: Ability to create PDF and Print search results and pages of archives with adding appropriate buttons to the content.
* Actions: Ability to create PDF and Print content from custom post type with adding appropriate buttons to the content.
* Actions: Ability to create PDF and Print portfolio or single portfolio for Portfolio plugin (powered by bestwebsoft.com) with adding appropriate buttons to the content.
* Actions: Ability to use execution of shortcode in pdf and printing output.
* Display: This plugin allows you to select the position of buttons in content (top left, top right, bottom left, bottom right), search or archives pages or custom post type (align left, align right).

= Recommended Plugins =

The author of the PDF & Print also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/plugin/updater-pro/?k=d74ca3ffdf910e4ec8ee8774573e7b67">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

If you would like to create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> for <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files  <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in another languages won't be accepted.

If you notice any bugs in the plugin's work, you can notify us about it and we'll investigate and fix the issue then. Your request should contain URL of the website, issues description and WordPress admin panel credentials.

Moreover we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then.
We can fix some things for free for the users who provide translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation ==

1. Upload `pdf-print` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Plugin settings are located in "BWS Plugins" > "PDF & Print".

View a <a href="https://docs.google.com/document/d/1Wwins2PmrzAYiEgFZDtRzMNDq9Sr7XDeqjGNm7b-oi8/edit" target="_blank">Step-by-step Instruction on PDF & Print Installation</a>

== Frequently Asked Questions ==

= How to change position buttons in content =

Go to the Settings page and change value for the 'Position of buttons in content' field.

= How to change position buttons in search or archives pages =

Go to the Settings page and change value for the 'Position of buttons on search or archive page' field.

= Buttons for content do not appear on page =

Go to the Settings page and change value for the 'Show PDF button' or 'Show Print button' fields.

= Buttons do not appear on search and archives pages =

Go to the Settings page and change value for the 'Show PDF button for search or archive page' or 'Show Print button for search or archive page' fields.
And you need to add the source code <?php if ( function_exists ( 'pdfprnt_show_buttons_search_archive' ) ) echo pdfprnt_show_buttons_search_archive (); ?> In the files search.php or archives.php.

= Menu to generate pdf file is not displayed in the adminbar for administrator =

Menu to generate pdf documents is displayed if you are an administrator only in the pages of the archives and if there is at least one search result.

= Buttons do not appear on portfolio and portfolio page =

In order to display PDF and Print Buttons of Portfolio plugin for the post it is necessary to insert the source code <?php if ( function_exists( 'pdfprnt_show_buttons_for_bws_portfolio_post' ) ) echo pdfprnt_show_buttons_for_bws_portfolio_post(); ?> in files portfolio.php or portfolio-post.php which are located in the template.

In order to display PDF and Print Buttons of Portfolio plugin for all posts on a page it is neessary to insert the source code <?php if ( function_exists( 'pdfprnt_show_buttons_for_bws_portfolio' ) ) echo pdfprnt_show_buttons_for_bws_portfolio(); ?> in file portfolio.php which is located in the template.

= Why are PDF and Print buttons not displayed in the custom post type =

In order to display PDF and Print Buttons of Portfolio plugin for custom post type it is necessary to insert the source code <?php if ( function_exists( 'pdfprnt_show_buttons_for_custom_post_type' ) ) echo pdfprnt_show_buttons_for_custom_post_type( $query_posts ); ?> where you need to specify query parameters for your custom post. For example: <?php if ( function_exists( 'pdfprnt_show_buttons_for_custom_post_type' ) ) echo pdfprnt_show_buttons_for_custom_post_type( 'post_type=gallery&orderby=post_date' ); ?> or <?php if ( function_exists( 'pdfprnt_show_buttons_for_custom_post_type' ) ) echo pdfprnt_show_buttons_for_custom_post_type( array( 'post_type' => 'gallery', 'orderby' => 'post_date' ) ); ?>. To find more information on syntax for assigning parameters to function see <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Parameters">here</a>.

= If I have shortcode on the page, but I don't want them to be printed (add to pdf) =

Go to the Settings page and unmark checkbox 'Settings for shortcode'.

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If no, please provide the following data along with your problem's description:
1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instuction on System Status</a>

== Screenshots ==

1. Page settings for the PDF & Print in admin panel.
2. Displaying buttons pdf and print in the post on your WordPress website.
3. Displaying buttons pdf and print on archive page of your WordPress website.
4. Generating PDF document in search results and pages archive with the choice of templates only for administrator.
5. Printing output page example.
6. PDF output page example.

== Changelog ==

= V1.7.5 - 07.08.2014 =
* Budfix : Security Exploit was fixed.

= V1.7.4 - 06.08.2014 =
* Update : We updated all functionality for wordpress 4.0-beta2.
* Budfix : Bug with Warning output in Dashboard was fixed.

= V1.7.3 - 28.05.2014 =
* Update : We updated all functionality for wordpress 3.9.1.
* Update : The Ukrainian language is updated in the plugin.
* Bugfix : Bug with dispalying error while searching in admin area was fixed.

= V1.7.2 - 14.04.2014 =
* Update : We updated all functionality for wordpress 3.8.2. 

= V1.7.1 - 05.03.2014 =
* Bugfix : Plugin optimization is done.
* Update : Plugin tabs is added.

= V1.7 - 21.02.2014 = 
* New : We added posibility to turn on showing of Printer choosing window.
* Update : Screenshots are updated.
* Update : We updated all functionality for wordpress 3.8.1.

= V1.6 - 16.01.2014 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.8.
* Bugfix : Problem with PDF and Print buttons on static homepage is fixed.
* Bugfix : Problem with Chinese, Japanese and rtl languages is fixed.

= V1.5 - 01.11.2013 =
* Update : We updated all functionality for wordpress 3.7.1.
* Update : Activation of radio button or checkbox by clicking on its label.
* NEW : Add checking installed wordpress version.

= V1.4 - 11.10.2013 =
* NEW: Added ability to switch on/off execution of shorcodes in pdf and printing output.
* NEW: Added new screenshots.
* Update : Updated code, changed some styles.
* Bugfix : Content on PDF preview now is shown.
* Bugfix : Fixed problems with styles of choosed template in admin bar.

= V1.3 - 15.03.2012 =
* NEW : Added functionality for use with custom post type.

= V1.2 - 12.03.2012 =
* NEW : Added functionality for use with Portfolio plugin for portfolio.

= V1.1 - 10.03.2012 =
* NEW : Added functionality for use with Portfolio plugin for single portfolio.

= V1.0 - 05.03.2012 =
* NEW : Added the ability to output PDF and Print buttons on the type of page.

== Upgrade Notice ==

= V1.7.5 =
Security Exploit was fixed.

= V1.7.4 =
We updated all functionality for wordpress 4.0-beta2. Bug with Warning output in Dashboard was fixed.

= V1.7.3 =
We updated all functionality for wordpress 3.9.1. The Ukrainian language is updated in the plugin. Bug with dispalying error while searching in admin area was fixed.

= V1.7.2 =
We updated all functionality for wordpress 3.8.2.

= V1.7.1 =
Plugin optimization is done. Plugin tabs is added.

= V1.7 =
We added posibility to turn on showing of Printer choosing window. Screenshots are updated. We updated all functionality for wordpress 3.8.1.

= V1.6 =
BWS plugins section is updated. We updated all functionality for wordpress 3.8. Problem with PDF and Print buttons on static homepage is fixed. Problem with Chinese, Japanese and rtl languages is fixed.

= V1.5 =
We updated all functionality for wordpress 3.7.1. Activation of radio button or checkbox by clicking on its label. Add checking installed wordpress version.

= V1.4 =
Added ability to switch on/off execution of shorcodes in pdf and printing output. Added new screenshots. Updated code, changed some styles. Content in on PDF preview now is showned. Fixed problems with styles of choosed template in admin bar.

= V1.3 =
Added functionality for use with custom post type.

= V1.2 =
Added functionality for use with Portfolio plugin for all page.


= V1.1 =
Added functionality for use with Portfolio plugin for post.

= V1.0 =
Added the ability to output PDF and Print buttons on the type of page.
