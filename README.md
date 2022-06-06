# Hello Elementor Child Theme for OaksMinistries.com #
**Contributors:** [thewebist](https://profiles.wordpress.org/thewebist/)  
**Requires at least:** 5.7  
**Tested up to:** 5.9.1  
**Requires PHP:** 7.2  
**Stable Tag:** 1.2.2  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

Hello Elementor Child theme developed for [Oaks Ministries](https://oaksministries.com).

## Description ##

Provides additional functionality for the Oaks Ministries website.

## Changelog ##

### 1.2.2 ###
* Turning off Jetpack nags.

### 1.2.1 ###
* BUGFIX: Initializing `$rental_products` in `woocommerce.new_order.php::update_user_rentals()` if empty.

### 1.2.0 ###
* Adding `[hideyt]` shortcode for embedding YouTube videos without "Related Content" videos at the end.

### 1.1.4 ###
* Adding theme screenshot.

### 1.1.3 ###
* BUGFIX: Checking for `$purchased_products` array in `lib/fns/woocommerce.php::custom_video_access_content()`.

### 1.1.2 ###
* Updating user's rentals via `woocommerce_order_status_processing` and `woocommerce_order_status_completed` hooks. Therefore, users who rent a video will have access as soon as their order enters the system under the default "Processing" order status.

### 1.1.1 ###
* Saving ACF JSON to `lib/acf-json`.

### 1.1.0 ###
* Handlebars template processing with `get_alert()` for displaying Elementor alert HTML.
* WooCommerce "Rentals"
  * Rentals REST EP for saving a user's rental product first access (see `lib/fns/rest.rentals.php`).
  * `woocommerce_new_order` hook that clears out a user's previous "first accessed" timestamps for any previous purchases. This allows for "re-rentals".

### 1.0.1 ###
* Updating email image header with image in the body of the email.

### 1.0.0 ###
* Initial release