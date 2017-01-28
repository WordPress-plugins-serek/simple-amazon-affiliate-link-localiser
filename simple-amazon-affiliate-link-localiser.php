<?php
   /*
   Plugin Name: Simple Amazon Affiliate Link Localiser
   Plugin URI: https://odd-one-out.serek.eu
   Description: A simple plugin to create Amazon Affiliate links using [geo] shortcodes. 
   Version: 1.0
   Author: Poul Serek
   Author URI: https://odd-one-out.serek.eu
   License: GPL2
   */

function cloudflare_geolocation($atts = [], $content = null, $tag = '') {
        //Settings
        //Amazon affiliate ids
        $amazon_affiliate_ids =array();
        $amazon_affiliate_ids['US']  = "serek-eu-us-20";
        $amazon_affiliate_ids['GB']  = "serek-eu-uk-21";
        $amazon_affiliate_ids['DE']  = "serek-eu-de-21";
        $amazon_affiliate_ids['ES']  = "serek-eu-es-21";
        $amazon_affiliate_ids['FR']  = "serek-eu-fr-21";
        $amazon_affiliate_ids['IT']  = "serek-eu-it-21";
        $amazon_affiliate_ids['CN']  = "serek-eu-cn-23";
        $amazon_affiliate_ids['CA']  = "serek-eu-ca-20";

        //Amazon affiliate urls
        $amazon_affiliate_urls =array();
        $amazon_affiliate_urls['US']  = "https://www.amazon.com";
        $amazon_affiliate_urls['GB']  = "https://www.amazon.co.uk";
        $amazon_affiliate_urls['DE']  = "https://www.amazon.de";
        $amazon_affiliate_urls['ES']  = "https://www.amazon.es";
        $amazon_affiliate_urls['FR']  = "https://www.amazon.fr";
        $amazon_affiliate_urls['IT']  = "https://www.amazon.it";
        $amazon_affiliate_urls['CN']  = "https://www.amazon.cn";
        $amazon_affiliate_urls['CA']  = "https://www.amazon.ca";

        //Buy from texts for the add to cart
        $amazon_affiliate_add_to_cart_texts=array();
        $amazon_affiliate_add_to_cart_texts['US']  = "Buy from Amazon.com";
        $amazon_affiliate_add_to_cart_texts['GB']  = "Buy from Amazon.co.uk";
        $amazon_affiliate_add_to_cart_texts['DE']  = "Kaufen bei Amazon.de";
        $amazon_affiliate_add_to_cart_texts['ES']  = "Comprar en Amazon.es";
        $amazon_affiliate_add_to_cart_texts['FR']  = "Achetez chez Amazon.fr";
        $amazon_affiliate_add_to_cart_texts['IT']  = "Compra su Amazon.it";
        $amazon_affiliate_add_to_cart_texts['CN']  = "Buy from Amazon.cn";
        $amazon_affiliate_add_to_cart_texts['CA']  = "Buy from Amazon.ca";

        //Misc settings
        $AWS_PublicKey = "insertYourPubicKey";

        // normalize attribute keys, lowercase
        $atts = array_change_key_case((array)$atts, CASE_UPPER);

        //Override country code from CloudFlare if spoof_locale is set
        $amazon_region = strtoupper(htmlspecialchars( $_GET["spoof_country"] ?? null));
        //... else use custom country code from CloudFlare
        $amazon_region = $amazon_region ?: $_SERVER["AMAZON_REGION"];
        //... else use the country code from CloudFlare
        $amazon_region = $amazon_region ?: $_SERVER["HTTP_CF_IPCOUNTRY"];

        //Use US as default value / region
        $geo_content = trim( $atts[$amazon_region] ?? $atts["ASIN"] ?? $atts["US"] ?? '' );
        $region = "US";
        if (isset($atts[$amazon_region]) || isset($atts["ASIN"]))
        {
                $region = strtoupper($amazon_region);
        }

      	switch (strtoupper($atts["TYPE"])) {
       		case "AMAZON_LINK_ASIN":
                     	return "<a href=".$amazon_affiliate_urls[$region]."/dp/".$geo_content."/?tag=".$amazon_affiliate_ids[$region]." rel=nofollow >".$atts["TITLE"]."</a>";
              	case "AMAZON_ADD_TO_CART":
                      	return "<form method='GET' action='".$amazon_affiliate_urls[$region]."/gp/aws/cart/add.html'> <input type='hidden' name='AssociateTag' value='".$amazon_affiliate_ids[$region]."' /> <input type='hidden' name='AWSAccessKeyId' value='".$AWS_PublicKey."' /> <input type='hidden' name='ASIN.1' value='".$geo_content."' /><input type='hidden' name='Quantity.1' value='1' /><input type='image' name='add' value='".$amazon_affiliate_add_to_cart_texts[$region]."' border=0 alt='".$amazon_affiliate_add_to_cart_texts[$region]."' src='https://odd-one-out.serek.eu/wp-custom/amazon/".$region."/buy-from-tan.gif'/> </form>";
              	case "CONTENT";
                      	return $geo_content;
              	case "AMAZON_LINK_SEARCH":
                      	return "<a href=".$amazon_affiliate_urls[$amazon_region]."/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords=".urlencode(trim($atts["SEARCH"]))."&tag=".$amazon_affiliate_ids[$amazon_region]." rel=nofollow>".$atts["TITLE"]."</a>";;
              	default:
                      	return "<a href='Type:".strtoupper($atts["TYPE"])."'>Invalid link</a>";
        }
}
add_shortcode('geo', 'cloudflare_geolocation');

?>
