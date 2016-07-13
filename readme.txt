=== PDF & Print by BestWebSoft ===
Contributors: bestwebsoft
Donate link: http://bestwebsoft.com/donate/
Tags: generate pdf, add pdf button, add print button, pdf plugin, pdf pages, print pages, generate pdf content, generate post pdf, pdf print, pdf print plugin, pdf custom post type, pdf content
Requires at least: 3.8
Tested up to: 4.5.3
Stable tag: 1.8.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate PDF files and print WordPress posts/pages. Customize document header/footer styles and appearance.

== Description ==

PDF & Print allows you in the easiest and most flexible way to create PDF and Print page with adding appropriate buttons to the content. PDF & Print using mpdf library under GPLv2 license.

http://www.youtube.com/watch?v=EM6AEkD9M_s

<a href="http://www.youtube.com/watch?v=E3w9ID3p2-A" target="_blank">PDF & Print by BestWebSoft Video instruction on Installation</a>

<a href="http://wordpress.org/plugins/pdf-print/faq/" target="_blank">PDF & Print by BestWebSoft FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">PDF & Print by BestWebSoft Support</a>

<a href="http://bestwebsoft.com/products/pdf-print/?k=6a544b359e625de8281a635315d84a70" target="_blank">Upgrade to PDF & Print Pro by BestWebSoft</a>

= Features =

* Create PDF and Print page with adding appropriate buttons to the content.
* Create PDF and Print search results and pages of archives with adding appropriate buttons to the content.
* Create PDF and Print content from custom post type with adding appropriate buttons to the content.
* Use execution of shortcode in pdf and printing output.
* Add custom styles.
* Show title and featured image in the pdf/print document.
* Load additional fonts.
* Select the position of buttons in content (top left, top right, bottom left, bottom right).

If you have a feature, suggestion or idea you'd like to see in the plugin, we'd love to hear about it! <a href="http://support.bestwebsoft.com/hc/en-us/requests/new" target="_blank">Suggest a Feature</a>

= Recommended Plugins =

The author of the PDF & Print also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/products/updater/?k=d74ca3ffdf910e4ec8ee8774573e7b67">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* Russian (ru_RU)
* Ukrainian (uk)

If you would like to create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> for <a href="http://support.bestwebsoft.com/hc/en-us/requests/new" target="_blank">BestWebSoft</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files  <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in other languages won't be accepted.

If you notice any bugs in the plugin's work, you can notify us about it and we'll investigate and fix the issue then. Your request should contain URL of the website, issues description and WordPress admin panel credentials.

Moreover we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then.
We can fix some things for free for the users who provide translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation ==

1. Upload `pdf-print` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. Plugin settings are located in "BWS Panel" > "PDF & Print".

View a <a href="https://docs.google.com/document/d/1Wwins2PmrzAYiEgFZDtRzMNDq9Sr7XDeqjGNm7b-oi8/edit" target="_blank">Step-by-step Instruction on PDF & Print Installation</a>

http://www.youtube.com/watch?v=E3w9ID3p2-A

== Frequently Asked Questions ==

= How to change position buttons in content =

Go to the Settings page and change value for the 'Position of buttons in the content' field.

= How to change position buttons in search or archives pages =

Go to the Settings page and change necessary values for the 'Search and archive pages' column.

= Buttons for content do not appear on page =

Go to the Settings page and change value for the 'Show PDF button' or 'Show Print button' fields.

= Why are PDF and Print buttons not displayed in the custom post type ? =

1. Please make sure that the necessary custom post type is selected in the "Types of posts that will be used in the plugin" setting block. If necessary, select the post type and save changes.
2. Check displaying the buttons on the specified pages in the front end of your site.

If buttons are still not displayed, please complete the following:

1. Try activating a standard WordPress theme for a while (Twenty Fifteen, Twenty Sixteen) and check if the problem remains.
2. Try deactivating all of your plugins except PDF & Print by BestWebsoft and also check if the problem is still present. If it is not, activate the plugins one-by-one, meanwhile checking, after which plugin activation the problem appears again.

Also, if you are using custom template you should paste the following string to the custom post or page template in order to use PDF and Print buttons:

- in the top of the post or page template

`<?php do_action( 'bwsplgns_display_pdf_print_buttons', 'top' ); ?>`

- in the bottom of the post or page template

`<?php do_action( 'bwsplgns_display_pdf_print_buttons', 'bottom' ); ?>`

You can specify some query parameters for your post. For example:

`<?php do_action( 'bwsplgns_display_pdf_print_buttons', 'bottom', 'post_type=gallery&orderby=post_date' ); ?>`

or

`<?php do_action( 'bwsplgns_display_pdf_print_buttons', 'bottom', array( 'post_type'=>'gallery', 'orderby'=>'post_date' ) ); ?>`

For more information on the syntax for assigning parameters to function see <a target="_blank" href="http://codex.wordpress.org/Class_Reference/WP_Query#Parameters">here</a>.

= Why in pdf/print-document displayed not all information from page =

For generating a pdf/print page version, PDF & Print plugin uses the content that is featured in the body of post/page before it is displayed by the browser (i.e. the data featured in the main block on this post/page in the edit mode).

= If I have shortcode on the page, but I don't want them to be printed (add to pdf) =

Go to the Settings page and unmark checkbox 'Settings for shortcodes'.

= How can I change the content pdf/print document? =

In order to change main content of pdf/print document you can use following filter:

`add_filter( 'bwsplgns_get_pdf_print_content', {your_function} );`

For example, add the following code to the 'functions.php' file of your theme:

`add_filter(
	'bwsplgns_get_pdf_print_content',
	function( $content ) {
		$my_content   = '<p>Lorem ipsum dolor sit amet</p>';
		$more_content = '<p>Donec fringilla libero ac sapien</p>';

		/* if you want add some data before to the main content */
		return $my_content . $content;

		/* if you want add some data after the main content */
		return $content . $my_content;

		/* if you want add some data both sides the main content */
		return $my_content . $content . $more_content;

		/* if you want add some data instead of the main content */
		return $my_content;
	}
);`


For more information about WordPress filters see <a target="_blank" href="https://codex.wordpress.org/Function_Reference/add_filter">here</a>.

= How can I add different styles to PDF and Print pages? =

To do that, enter the required styles in the "edit styles" field on the Settings page. You can use the class 'pdfprnt_print' in order to add some styles only for Print pages.

( To display "edit styles" field go Dashboard->BWS Panel->PDF & Print and mark "Add custom styles" checkbox )

For example:

`body p {
	color: green;
}
.pdfprnt_print p {
	color: red;
}`

Also you can use filter 'bwsplgns_add_pdf_print_styles' to include additional css-files.

For example:

Let's imagine that you have files style.css, style_print.css and style_pdf.css and you want include them to your PDF or Print pages.

1. upload these files to the folder 'wp-content/uploads' via FTP.
2. add the following code to the 'functions.php' file of your theme:

`add_filter(
	'bwsplgns_add_pdf_print_styles',
	function( $styles ) {
		$styles[] = array( 'wp-content/uploads/style_pdf.css', 'pdf' ); /* file will be included to PDF pages */
		$styles[] = array( 'wp-content/uploads/style_print.css', 'print' ); /* file will be included only to Print pages */
		$styles[] = array( 'wp-content/uploads/style.css' ); /* file will be included to PDF and Print pages */
		return $styles;
	}
);`


= I get an error "Warning: file_put_contents(/public_html/wp-content/plugins/pdf-print/mpdf/ttfontdata/dejavusanscondensed.GSUBGPOStables.dat): failed to open stream: Permission denied in /***/wp-content/plugins/pdf-print/mpdf/classes/ttfontsuni.php on line 1145". What can I do? =

Probably, you don't have sufficient access permissions to files and folders.

To solve this, please try the following:

1. using FTP, please go to the folder "/public_html/wp-content/plugins/pdf-print"
2. please check what permissions are set on the "mpdf" folder (755 must be set for the folder and 644 for files)
3. if there are another permissions set, please change them

For more info see <a target="_blank" href="https://codex.wordpress.org/Changing_File_Permissions">Changing File Permissions</a>.

= How can I load additional fonts for MPDF library? =

Please follow the next steps:

1. go to the plugin`s settings page
2. click "Load Fonts" button

or

1. download MPDF library by link http://www.mpdfonline.com/repos/MPDF_6_0.zip
2. using FTP, load the file MPDF_6_0.zip to the folder "{your_site_home_folder}/wp-content/uploads"
3. go to the plugin`s settings page
4. click "Load Fonts" button

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If no, please provide the following data along with your problem's description:

1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instruction on System Status</a>

== Screenshots ==

1. Displaying buttons pdf and print in the post on your WordPress website.
2. Displaying buttons pdf and print on archive page of your WordPress website.
3. Printing output page example.
4. PDF output page example.
5. Page settings for the PDF & Print in admin panel.

== Changelog ==

= V1.8.9 - 13.07.2016 =
* Bugfix : Custom styles saving is updated.
* Update : BWS Panel section is updated. 

= V1.8.8 - 26.04.2016 =
* Bugfix : We fixed the bug with instalation of the additional fonts.

= V1.8.7 - 21.03.2016 =
* Update : Styles for correct displaying of PDF and Print buttons with theme 2016 have been added.
* Bugfix : The bug with displaying of PDF and Print buttons in site RSS has been fixed.

= V1.8.6 - 30.11.2015 =
* Bugfix : The bug with plugin menu duplicating was fixed.
* Bugfix : The bug with the function which is outputted using 'the_content' was fixed.

= V1.8.5 - 18.11.2015 =
* NEW : An ability to add custom styles was added.
* NEW : An ability to show/hide title and featured image in the pdf/print document was added.
* Update : "Bwsplgns_get_pdf_print_content"-hook`s call was changed.
* Update : All functionality for wordpress 4.4-beta3 was updated.

= V1.8.4 - 01.09.2015 =
* NEW : Added hooks for the possibility of changing the content of the pdf/print document.
* Update : We updated functionality for displaying pdf/print buttons via functions.
* Update : We updated all functionality for wordpress 4.3.

= V1.8.3 - 15.07.2015 =
* NEW : We added Top & Bottom Position of buttons in the content.

= V1.8.2 - 19.06.2015 =
* Bugfix : We fixed the bug with instalation of the additional fonts.

= V1.8.1 - 17.06.2015 =
* Attention : We changed plugin settings structure. If you are experiencing problems with the plugin work, please contact us via <a href="http://support.bestwebsoft.com" target="_blank">support</a>.
* Bugfix : We fixed the bug with displaying images in pdf-document.
* Bugfix : We fixed the bug with creation of rtl-oriented documents.
* NEW : Added ability to load additional fonts.
* Update : We updated styles for generate pdf/print page version with default stylesheet.
* Update : We updated functionality for displaying pdf/print buttons in any place of your site.
* Update : We updated MPDF library to version 6.0.

= V1.8.0 - 18.05.2015 =
* Update : We updated all functionality for wordpress 4.2.2.

= V1.7.9 - 24.04.2015 =
* Bugfix : We fixed the bug with placing buttons on custom post pages, search pages and archives.
* Update : We updated all functionality for wordpress 4.2.

= V1.7.8 - 11.02.2015 =
* Update : We updated mPDF to 5.7.4 version.

= V1.7.7 - 09.01.2015 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 4.1.

= V1.7.6 - 16.10.2014 =
* Bugfix : We fixed js errors.

= V1.7.5 - 07.09.2014 =
* Bugfix : Security Exploit was fixed.

= V1.7.4 - 06.08.2014 =
* Update : We updated all functionality for wordpress 4.0-beta2.
* Bugfix : Bug with Warning output in Dashboard was fixed.

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
* NEW : We added posibility to turn on showing of Printer choosing window.
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
* NEW: Added ability to switch on/off execution of shortcodes in pdf and printing output.
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

= V1.8.9 =
Custom styles saving is updated. BWS Panel section is updated.

= V1.8.8 =
We fixed the bug with instalation of the additional fonts.

= V1.8.7 =
Styles for correct displaying of PDF and Print buttons with theme 2016 have been added. The bug with displaying of PDF and Print buttons in site RSS has been fixed.

= V1.8.6 =
The bug with plugin menu duplicating was fixed. The bug with the function which is outputted using 'the_content' was fixed.

= V1.8.5 =
An ability to add custom styles was added. An ability to reload additional fonts was added. An ability to show/hide title and featured image in the pdf/print document was added. "Bwsplgns_get_pdf_print_content"-hook`s call was changed. All functionality for wordpress 4.4-beta3 was updated.

= V1.8.4 =
Added hooks for the possibility of changing the content of the pdf/print document. We updated functionality for displaying pdf/print buttons via functions. We updated all functionality for wordpress 4.3.

= V1.8.3 =
We added Top & Bottom Position of buttons in the content.

= V1.8.2 =
We fixed the bug with instalation of the additional fonts.

= V1.8.1 =
We changed plugin settings structure. We fixed the bug with displaying images in pdf-document. We fixed the bug with creation of rtl-oriented documents. Added ability to load additional fonts. We updated styles for generate pdf/print page version with default stylesheet. We updated functionality for displaying pdf/print buttons in any place of your site. We updated MPDF library to version 6.0.

= V1.8.0 =
We updated all functionality for wordpress 4.2.2.

= V1.7.9 =
We fixed the bug with placing buttons on custom post pages, search pages and archives. We updated all functionality for wordpress 4.2.

= V1.7.8 =
 We updated mPDF to 5.7.4 version.

= V1.7.7 =
BWS plugins section is updated. We updated all functionality for wordpress 4.1.

= V1.7.6 =
We fixed js errors.

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
Added ability to switch on/off execution of shortcodes in pdf and printing output. Added new screenshots. Updated code, changed some styles. Content in on PDF preview now is showned. Fixed problems with styles of choosed template in admin bar.

= V1.3 =
Added functionality for use with custom post type.

= V1.2 =
Added functionality for use with Portfolio plugin for all page.


= V1.1 =
Added functionality for use with Portfolio plugin for post.

= V1.0 =
Added the ability to output PDF and Print buttons on the type of page.
