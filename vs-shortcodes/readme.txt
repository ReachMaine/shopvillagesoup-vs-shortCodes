=== VS Shortcodes ===
Contributors:  Ray St. Onge
Tags: shortCodes
Requires at least: 4.5
Tested up to: 6.0
Requires PHP: 5.6
Stable tag: 1.7.1

License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Controls and handles various shortcodes to be used by other plugins.

== Description ==

The basic shortcodes inside of vs-shortcodes in order are:

register_scriptShortCodes() : Registers the shortcode_style with css
enqueue_styleShortCodes()   : Enqueues the shortcide_style
getMyBriefs()   : Get the user's briefs
getMyOffers()   : Get the user's offers
getMyRealEstates()   : Get the user's real estates
getAllBriefs()  : Retrieves ALL briefs on the site
getAllOffers()  : Retrieves ALL offers on the site
getAllOffersPretty() : Retrieves ALL offers on the site, returns specially to do in a pretty style
getAllBriefsPretty() : Retrieves ALL briefs on the site, returns specially to do in a pretty style
getAllBriefsKnox()   : Retrieves all briefs from the Knox site
getAllBriefsWaldo()  : Retrieves all briefs from the Waldo site
getAllOffersKnox()   : Retrieves all offers from the Knox site
getAllOffersWaldo()  : Retrieves all offers from the Waldo site

{ dining offers do not exist.}

getAllBriefsIslander()  : Retrieves all briefs from Islander
getAllOffersIslander()  : Retrieves all offers from Islander
getAllBriefsEA()        : Retrieves all briefs from EA
getAllOffersEA()        : Retrieves all offers from EA


The following shortcodes will retrieve data based on the parameters passed to it:
retrievePosts([post_type]) : Retrieves posts of the post_type
getPosts()    : Retrieves posts of a certain type and displays them
retrievePostsPretty()      : Retrieves posts of a certain type and displays them, specially in a pretty style


== Changelog ==
<<<<<<<

= 1.7.0
* Added header info to try and force the pages not to cached

= 1.6.2
* The check for the cached file was occurring and it is possible the file would not exist if caching was never turned on.
  Added a check to see if caching is enabled before testing for the cached file

=======
= 1.7.0
* Added header info to try and force the pages not to cached

= 1.6.2
* The check for the cached file was occurring and it is possible the file would not exist if caching was never turned on.
  Added a check to see if caching is enabled before testing for the cached file

>>>>>>>
= 1.6.1
* fixed typo when checking for post_status, had published instead of publish

= 1.6.0
* added an option of debugCSSCaching. If this is enabled, the cacheKey is ignored and the current timestamp is used as the cacheKey

= 1.5.1
* added an unlink call to remove the cached file

= 1.5.0
* added a settings section so the cachingKey can be set. That value is set after the call to the css.
  This value is changed when there is a change to the css. This will cause the css to be reloaded

= 1.4.0
* modified vsPosts to create a complete cached page which includes calls to the css so it can properly be displayed

= 1.3.0
* add the shortCode vsPosts. This shortcode take paramaters so it will be easier to when new codes are needed

= 1.2.0
* added caching to results of shortcodes

= 1.1.0
* Added a shortcode to do real estate on the home page

= 1.0 =
* A change since the previous version.
* Another change.



== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.
