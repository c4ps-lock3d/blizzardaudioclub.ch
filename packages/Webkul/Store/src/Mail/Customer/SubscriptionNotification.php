<?php

namespace Webkul\Store\Mail\Customer;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Webkul\Core\Contracts\SubscribersList;
use Webkul\Store\Mail\Mailable;

class SubscriptionNotification extends Mailable
{
    /**
     * Create a mailable instance
     *
     * @return void
     */
    public function __construct(public SubscribersList $subscribersList) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [
                new Address(core()->getAdminEmailDetails()['email'],),
            ],
            subject: trans('shop::app.emails.customers.subscribed.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shop::emails.customers.subscribed',
            with: [
                'fullName' => core()->getAdminEmailDetails()['full_name'],
            ],
        );
    }
}
