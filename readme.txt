=== Altus Website Grader ===
Contributors: altuswebsolutions-official
Tags: website grader, seo checker, speed test, website audit, small business
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free website grader for small businesses. Grade any public website on 15 speed, SEO, mobile and trust signals and show the full report on screen.

== Description ==

Enter any website address and get a score out of 100, a grade from A to F, and the full 15-point report — speed, SEO, mobile and trust — in under a minute, shown on screen.

Use the shortcode `[altus_website_grader]` on any page.

This open-source version contains NO lead capture, NO email sending, and NO data storage — results are shown on screen only. The version hosted on altuswebsolutions.com may offer email delivery; this open-source version does not.

The grader fetches only the public homepage (plus public robots.txt / sitemap / favicon) of the URL entered. Private, reserved, loopback and link-local addresses are blocked, including after redirects. Checks are rate-limited to 5 per IP per hour.

== Installation ==

1. Upload the plugin zip via Plugins > Add New > Upload Plugin, or extract it into `/wp-content/plugins/`.
2. Activate the plugin.
3. Add `[altus_website_grader]` to any page and publish it.

== Frequently Asked Questions ==

= Is it really free? =

Yes. The score, the top 3 issues and the full 15-point report are all free, with no account and no email address required.

= Is this the same as Google PageSpeed Insights? =

No. This is a fast homepage health check of the fundamentals; PageSpeed is a deeper lab audit.

= Does it store or email anything? =

No. No database table, no saved files with user input, no email. The only server-side state is a rate-limit counter.

== Changelog ==

= 1.0.0 =

First public release (stripped open-source version). 15 homepage checks, score and grade, full report on screen. Email gate, lead storage and email sending removed.
