<?php

namespace Zealousweb\GdprCompliance\Model\Source;

class Forms 
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'user_login',
                'label' => 'Customer Login Form'
            ],
            [
                'value' => 'user_create',
                'label' => 'Customer Registration Form'
            ],
            [
                'value' => 'user_forgotpassword',
                'label' => 'Forgot Password'
            ],
            [
                'value' => 'guest_checkout',
                'label' => 'Checkout Login popup'
            ],
            [
                'value' => 'contact_us',
                'label' => 'Contact Us'
            ],
            [
                'value' => 'user_edit',
                'label' => 'Change password'
            ],
            [
                'value' => 'user_delete',
                'label' => 'Delete Customer'
            ],
            [
                'value' => 'newsletter_subscription',
                'label' => 'Newsletter subscription'
            ]
        ];
    }
}
