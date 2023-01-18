# Geo targeting on the WordPress VIP Platform

Tailor the content you serve to your visitors on a country by country basis.

## Contents

 - [Overview](#overview)
 - [Loading the VIP Go Geo Uniques plugin](#loading-the-vip-go-geo-uniques-plugin)
 - [Configure the plugin](#configure-the-plugin)
 - [Serving geo-targeted content](#serving-geo-targeted-content)
 - [Caching for sites using geo targeting](#caching-for-sites-using-geo-targeting)
 - [Geo targeting and reverse proxies](#geo-targeting-and-reverse-proxies)
 - [Unexpected country codes](#unexpected-country-codes)

## Overview

The features of the VIP Go Geo Uniques plugin are designed to work on a [VIP Platform environment](https://docs.wpvip.com/technical-references/vip-platform/environments/) and will not work as expected on a local development environment.

This plugin can be used on a non-VIP platform host infrastructure, but only if the host infrastructure has the `$_SERVER['GEOIP_COUNTRY_CODE']` superglobal.

Sites running on the WordPress VIP Platform with the  VIP Go Geo Uniques plugin can differentiate visitors from different countries. This plugin makes it possible to tailor the content you serve to your visitors on a country by country basis, while still keeping the benefits of VIP's fast caching infrastructure.

Adding geo targeting to your VIP code takes two parts:

1. Loading and configuring the VIP Go Geo Uniques plugin
2. Serving geo targeted content

## Loading the VIP Go Geo Uniques plugin

Because the VIP Go Geo Uniques plugin requires configuration in code, we recommend [loading this plugin with the function](https://docs.wpvip.com/how-tos/activate-plugins-through-code/#plugins-in-the-plugins-directory) `wpcom_vip_load_plugin` function.

Download the plugin from [the GitHub repo](https://github.com/Automattic/vip-go-geo-uniques), add it to your `/plugins` directory, and then activate it for your site:
	
```
// Load the VIP Go Geo Uniques plugin
wpcom_vip_load_plugin( 'vip-go-geo-uniques' );
```

## Configure the plugin

To configure the plugin, you must set a default location using the `VIP_Go_Geo_Uniques::set_default_location` method. For example, to set the United States are the default:

```
VIP_Go_Geo_Uniques::set_default_location( 'US' );
```

Next, configure each region using the `VIP_Go_Geo_Uniques::add_location` method. If a region is not set, visits from that region will use the default location. As an example, to create a geo unique region of the United Kingdom you would call:

```
VIP_Go_Geo_Uniques::add_location( 'GB' );
```

The complete example below sets the default location as the US (`US`), and a further location as the UK (`GB`).

```
// Configure the VIP Go Geo Uniques plugin
if ( class_exists( 'VIP_Go_Geo_Uniques' ) ) {
    VIP_Go_Geo_Uniques::set_default_location( 'US' );
    VIP_Go_Geo_Uniques::add_location( 'GB' );
}
```

**Note:** `set_default_location` and `add_location` must be called before the `init` hook is fired, otherwise cache variance will not work.

## Serving geo-targeted content

You can now tailor your content to the different country audiences:

```
if ( function_exists( 'vip_geo_get_country_code' ) && 'GB' == vip_geo_get_country_code() ) {
    echo "Please select your favourite colour:";
} else {
    echo "Please select your favorite color:";
}
```

## Caching for sites using geo targeting

When you enable the VIP Go Geo Uniques plugin, your page cache is varied by country code. This means that for each URL requested, we hold a separate copy of the page (i.e. the HTTP response) for each country in our page cache. When you update a post or page, all cached copies for all country codes are automatically invalidated. When you purge the cache for your whole site, the whole cache is still invalidated.

## Geo targeting and reverse proxies

Geo targeting on VIP is not reliable if your site uses a reverse proxy.

This is because when a request comes via a reverse proxy, the remote IP address is that of the reverse proxy. The load balancers on VIP will assign the country code to the reverse proxy IP, and not the IP address of the end user. It is possible for the correct country to be served to the end user by chance, but in most cases times it will not be correct.

## Unexpected country codes

Our country codes are supplied via the Maxmind GeoIP database, which implements special "pseudo country codes" for particular situations:

> Note: "EU" and "AP" codes are only used when a specific country code has not been designated (see FAQ). Blocking or re-directing by "EU" or "AP" will only affect a small portion of IP addresses. Instead, you should list the countries you want to block/re-direct individually.
> - GeoIP and ISO 3166 Country Codes

At time of writing, the GeoIP pseudo country codes are:

 - AP,"Asia/Pacific Region"
 - A1,"Anonymous Proxy"
 - A2,"Satellite Provider"
 - O1,"Other Country"
 - EU,"Europe"

A common misconception is that the country code for the United Kingdom is UK, in fact it is GB.

---

Documentation is licensed under a Creative Commons Attribution-ShareAlike 4.0 International License	
