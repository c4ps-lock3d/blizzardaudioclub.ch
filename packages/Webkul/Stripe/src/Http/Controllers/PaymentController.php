<?php

namespace Webkul\Stripe\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     *
     * @var OrderRepository
     * @var InvoiceRepository
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository,
    ) {
        //
    }

    /**
     * Redirects to the Stripe server.
     */
    public function redirectcard(): RedirectResponse
    {
        $cart = Cart::getCart();
        $billingAddress = $cart->billing_address;

        Stripe::setApiKey(core()->getConfigData('sales.payment_methods.stripe.stripe_api_key'));

        $shippingRate = $cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0;
        $discountAmount = $cart->discount_amount;
        $totalAmount = $cart->grand_total;

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => $cart->global_currency_code,
                    'product_data' => [
                        'name' => 'Stripe Checkout Payment order id - '.$cart->id,
                    ],
                    'unit_amount' => $cart->grand_total * 100,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $cart->billing_address->email,
            'mode'        => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url'  => route('stripe.cancel'),
        ]);

        return redirect()->away($checkoutSession->url);
    }

    public function redirecttwint(): RedirectResponse
    {
        $cart = Cart::getCart();
        $billingAddress = $cart->billing_address;

        Stripe::setApiKey(core()->getConfigData('sales.payment_methods.stripe.stripe_api_key'));

        $shippingRate = $cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0;
        $discountAmount = $cart->discount_amount;
        $totalAmount = $cart->grand_total;

        $checkoutSession = Session::create([
            'payment_method_types' => ['twint'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => $cart->global_currency_code,
                    'product_data' => [
                        'name' => 'Stripe Checkout Payment order id - '.$cart->id,
                    ],
                    'unit_amount' => $cart->grand_total * 100,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $cart->billing_address->email,
            'mode'        => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url'  => route('stripe.cancel'),
        ]);

        return redirect()->away($checkoutSession->url);
    }

    public function redirectpaypal(): RedirectResponse
    {
        $cart = Cart::getCart();
        $billingAddress = $cart->billing_address;

        Stripe::setApiKey(core()->getConfigData('sales.payment_methods.stripe.stripe_api_key'));

        $shippingRate = $cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0;
        $discountAmount = $cart->discount_amount;
        $totalAmount = $cart->grand_total;

        $checkoutSession = Session::create([
            'payment_method_types' => ['paypal'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => $cart->global_currency_code,
                    'product_data' => [
                        'name' => 'Stripe Checkout Payment order id - '.$cart->id,
                    ],
                    'unit_amount' => $cart->grand_total * 100,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $cart->billing_address->email,
            'mode'        => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url'  => route('stripe.cancel'),
        ]);

        return redirect()->away($checkoutSession->url);
    }

    /**
     * Place an order and redirect to the success page.
     */
    public function success(): RedirectResponse
    {
        $cart = Cart::getCart();
        /*$cart->payment->method_title = 'TWINT';*/

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        return redirect()->route('shop.checkout.onepage.success');
    }

    /**
    * failure
    */
    public function failure()
    {
        session()->flash('error', 'Paiement annulé');
        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Prepares order's invoice data for creation.
     */
    protected function prepareInvoiceData($order): array
    {
        $invoiceData = [
            'order_id' => $order->id,
            'invoice'  => ['items' => []],
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
