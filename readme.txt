=== Optical Shop - Shapes & Trending Cards ===
Contributors: starter-dev
Tags: optical, eyeglasses, sunglasses, shapes, trending, video, woocommerce
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display eyeglass/sunglass shape tiles and trending product cards with video hover-autoplay on any WordPress page.

== Description ==

**Optical Shop UI** adds two beautiful sections to your optical store:

1. **Shape Tiles** — Round icon tiles (Rectangle, Cateye, Aviator, etc.) that link to product categories.
2. **Trending Cards** — Image or video cards with hover-to-play, gradient overlays, and "Shop now" CTAs.

Both sections support **Eyeglasses** and **Sunglasses** groups, Gutenberg blocks, and classic shortcodes.

= Features =

* Custom Post Type-based admin for easy management.
* WP Media uploader for images, posters, and MP4 videos.
* Desktop: hover-to-play / leave-to-pause videos.
* Mobile: IntersectionObserver auto-play when 60%+ visible.
* Responsive layout with horizontal scroll + snap on mobile.
* Works with or without WooCommerce.
* Clean uninstall removes all data.

== Installation ==

1. Upload the `optical-shop-ui` folder to `/wp-content/plugins/`.
2. Activate through **Plugins → Installed Plugins**.
3. Go to **Optical Shop UI → Shapes Manager** to add shape tiles.
4. Go to **Optical Shop UI → Trending Manager** to add trending cards.
5. Use shortcodes or Gutenberg blocks on any page.

== Shortcodes ==

`[optical_shapes group="eyeglasses"]`
`[optical_shapes group="sunglasses"]`
`[optical_trending group="eyeglasses"]`
`[optical_trending group="sunglasses"]`
`[optical_trending group="eyeglasses" brand="YourBrand"]`

== Gutenberg Blocks ==

Search for **Optical Shapes** or **Optical Trending** in the block inserter.

== Changelog ==

= 1.0.0 =
* Initial release.
