=== Hide featured image on all single page/post ===

Contributors:      TylerTork
Plugin Name:       Hide featured image on all single page/post
Plugin URI:        https://torknado.com/hide-featured-single
Tags:              featured images
Author URI:        https://torknado.com/
Author:            Tyler Tork
Donate link:       https://torknado.com/donate
Requires at least: 4.6
Tested up to:      5.9
Requires PHP:      7.2
Stable tag:        1.1
Version:           1.1
License:           GPLv2 (or later)

== Description ==

This lightweight plugin hides _all_ featured images on pages and posts when they are viewed in their own tab. The posts are not modified -- they still have a featured image if you selected one. The plugin only disables the theme's ability to access the image when rendering the page. If your theme displays featured images in search results, Posts page, category lists, and so on, it still can do that. Social media platforms and search engines that look for an image to accompany the excerpt, can also still find the featured image URL in the post metadata.

There are no settings; the plugin just does the one thing.

**The change applies to existing posts as well as anything you create after activation.** You would need to edit any old posts that are adversely affected by this change (if you want the featured image to display, you would have to insert it into the content). If you have a lot of old content, you might want to use a different plugin such as [Conditionally display featured image on singular pages and posts](https://wordpress.org/plugins/conditionally-display-featured-image-on-singular-pages/), which by default only affects new posts.

# TECHNICAL NOTE #

Themes that find the featured image by looking up data in the database directly as opposed to calling `get_the_post_thumbnail()` or `wp_get_attachment_image()`, may still emit HTML for the featured image despite this plugin. As a second line of defense, the plugin loads a stylesheet that tries to hide the image if it is present in the HTML. This also might fail if the theme has used unusual entities and class names. Custom CSS rules should always be possible as a fallback (in which case you don't need this plugin).

This plugin also (not on purpose) blocks the featured image from appearing in the content where you've inserted a "Post featured image" block. I'm looking into whether it might be possible to detect the difference between a request for the featured image for default display versus a deliberate insertion later. In the meantime, as a workaround, don't use the "Post featured image" block, but instead insert an "Image" block and re-select the image from your media library (I prefer that anyway since it gives more control over size, alt text, etc).

If you find a theme this doesn't work with, please let me know.

== Installation ==

- Visit Plugins > Add New.
- Search for "Hide featured image on all single page/post".
- Click Install, then Activate.
- There is nothing to configure. If you have existing content, you'll have to edit old posts to include the featured image where you want it.

== Upgrade Notice ==

= 1.0 =
Initial release.

== Screenshots ==

== Changelog ==

= 1.0 =
Initial release.

= 1.1 =
Added support for Squarex theme.

== Frequently Asked Questions ==

= Why wouldn't I want to display featured images on the page that features them? =

You probably _do_ want to display them somewhere in the post. You just might not always like the way your theme does it. If using this plugin, add the same image twice in your post -- once as the featured image, and again somewhere in the content. There, you can use the position, size, wrapping, caption, and cropping you prefer.

= Will this affect opengraph or other metadata? =

No. Facebook or whoever will still find your featured image.

= I'm mostly okay with where the featured image displays. There are just a few pages where I want to do something different. =

This is not the right plugin for that situation. Instead, use [Conditionally display featured image on singular pages and posts](https://wordpress.org/plugins/conditionally-display-featured-image-on-singular-pages/).

= Why do I need a plugin for this? I can just write a custom CSS rule =

This is true -- you can probably hide the featured image yourself using the "additional CSS" feature of your theme, if you know how. The drawbacks to this technique are, first, the featured image is then still part of the HTML and browsers might request it from your server, which is a waste of bandwidth, and second, that the theme still _thinks_ there's a featured image, and may make space for it on the screen or otherwise change the styling or placement of other entities to account for its presence. So the CSS rule might not work. Be sure to test it using the emulation mode of your browser to see whether it works on a variety of devices.

= It doesn't work on my site! The image is still visible. =

Some themes do things in what I will call "the wrong" order, such that no plugin can affect the inclusion of featured images in the HTML because none of the plugin's code has yet been activated when the theme retrieves this information. There's nothing I can do about that in my code, so I'd appreciate you reporting this as a bug to the theme developers. Any other plugin that does something similar will also be affected.

As a second line of defense, this plugin uses a CSS stylesheet to set to "display: none" some style classes commonly associated with featured images. You can emulate this solution by using custom CSS (as described in the previous section). Nearly all themes have a place to insert custom CSS in the Customize screen. Use the browser's developer mode to see how the part you want to hide is styled and to test your new rule. This is not an ideal solution but it does give you the desired appearance, usually.

If you solve the problem with custom CSS, this plugin isn't helping you and you might as well remove it. Please drop me a line with the theme name (and the CSS rule you ended up using, if you're willing).

== Donations ==

The author is not soliciting donations for himself. If you find this useful and want to pay it forward, please consider donating to an organization that directly helps people in need.