<?php

namespace Webkul\RateRules\Carriers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Models\CartShippingRate;

class StandardRate extends AbstractShipping
{
    /**
     * Shipping method carrier code.
     *
     * @var string
     */
    protected $code = 'standardrate';

    /**
     * Shipping method code.
     *
     * @var string
     */
    protected $method = 'standardrate_standardrate';

    /**
     * Calculate rate for flatrate.
     *
     * @return \Webkul\Checkout\Models\CartShippingRate|false
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        return $this->getRate();
    }

    /**
     * Get rate.
     */
    public function getRate(): CartShippingRate
    {
        $cart = Cart::getCart();

        $cartShippingRate = new CartShippingRate;

        $cartShippingRate->carrier = $this->getCode();
        $cartShippingRate->carrier_title = $this->getConfigData('title');
        $cartShippingRate->method = $this->getMethod();
        $cartShippingRate->method_title = $this->getConfigData('title');
        $cartShippingRate->method_description = $this->getConfigData('description');
        $cartShippingRate->price = 0;
        $cartShippingRate->base_price = 0;
        /* initialiser le poids à 0 ***/
        $itemweight = 0;

        foreach ($cart->items as $item) {
            if ($item->getTypeInstance()->isStockable()) {
                $cartShippingRate->price += core()->convertPrice($this->getConfigData('default_rate')) * $item->quantity;
                $cartShippingRate->base_price += $this->getConfigData('default_rate') * $item->quantity;
                $itemweight += $item->weight * $item->quantity;
            }
        }
        /* conditions de frais de livraison, par rapport au pays de livraison et au poids des articles */
        if ($cart->shipping_address->country == 'CH') {
            if ($itemweight < 0.25){
                $cartShippingRate->base_price += 4.50;
                $cartShippingRate->price += 4.50;
            } elseif ($itemweight < 0.5){
                $cartShippingRate->base_price += 9.50;
                $cartShippingRate->price += 9.50;
            } elseif ($itemweight < 2){
                $cartShippingRate->base_price += 9.50;
                $cartShippingRate->price += 9.50;
            } else {
                $cartShippingRate->base_price += 12.50;
                $cartShippingRate->price += 12.50;
            }
        } elseif ($cart->shipping_address->country == 'AD' or $cart->shipping_address->country == 'AL' or $cart->shipping_address->country == 'AM' or $cart->shipping_address->country == 'AT' or $cart->shipping_address->country == 'AX' or $cart->shipping_address->country == 'AZ' or $cart->shipping_address->country == 'BA' or$cart->shipping_address->country == 'BE' or $cart->shipping_address->country == 'BG' or $cart->shipping_address->country == 'BY' or $cart->shipping_address->country == 'CY' or $cart->shipping_address->country == 'CZ' or $cart->shipping_address->country == 'DE' or $cart->shipping_address->country == 'DK' or $cart->shipping_address->country == 'EE' or $cart->shipping_address->country == 'ES' or $cart->shipping_address->country == 'FI' or $cart->shipping_address->country == 'FO' or $cart->shipping_address->country == 'FR' or $cart->shipping_address->country == 'GB' or $cart->shipping_address->country == 'GE' or $cart->shipping_address->country == 'GG' or $cart->shipping_address->country == 'GI' or $cart->shipping_address->country == 'GR' or $cart->shipping_address->country == 'HR' or $cart->shipping_address->country == 'HU' or $cart->shipping_address->country == 'IE' or $cart->shipping_address->country == 'IM' or $cart->shipping_address->country == 'IS' or $cart->shipping_address->country == 'IT' or $cart->shipping_address->country == 'JE' or $cart->shipping_address->country == 'KZ' or $cart->shipping_address->country == 'LI' or $cart->shipping_address->country == 'LT' or $cart->shipping_address->country == 'LU' or $cart->shipping_address->country == 'LV' or $cart->shipping_address->country == 'MC' or $cart->shipping_address->country == 'MD' or $cart->shipping_address->country == 'ME' or $cart->shipping_address->country == 'MK' or $cart->shipping_address->country == 'MT' or $cart->shipping_address->country == 'NL' or $cart->shipping_address->country == 'NO' or $cart->shipping_address->country == 'PL' or $cart->shipping_address->country == 'PT' or $cart->shipping_address->country == 'RO' or $cart->shipping_address->country == 'RS' or $cart->shipping_address->country == 'RU' or $cart->shipping_address->country == 'SE' or $cart->shipping_address->country == 'SI' or $cart->shipping_address->country == 'SJ' or $cart->shipping_address->country == 'SK' or $cart->shipping_address->country == 'SM' or $cart->shipping_address->country == 'TR' or $cart->shipping_address->country == 'UA' or $cart->shipping_address->country == 'VA' or $cart->shipping_address->country == 'XK') {
            if ($itemweight < 0.25){
                $cartShippingRate->base_price += 8.00;
                $cartShippingRate->price += 8.00;
            } elseif ($itemweight < 0.5){
                $cartShippingRate->base_price += 12.00;
                $cartShippingRate->price += 12.00;
            } elseif ($itemweight < 2){
                $cartShippingRate->base_price += 26.00;
                $cartShippingRate->price += 26.00;
            } else {
                $cartShippingRate->base_price += 49.00;
                $cartShippingRate->price += 49.00;
            }
        } 
        else {
            if ($itemweight < 0.25){
                $cartShippingRate->base_price += 12.00;
                $cartShippingRate->price += 12.00;
            } elseif ($itemweight < 0.5){
                $cartShippingRate->base_price += 21.00;
                $cartShippingRate->price += 21.00;

            } elseif ($itemweight < 2){
                $cartShippingRate->base_price += 45.00;
                $cartShippingRate->price += 45.00;
            } else {
                $cartShippingRate->base_price += 92.00;
                $cartShippingRate->price += 92.00;
            }
        } 
        return $cartShippingRate;
    }
}
