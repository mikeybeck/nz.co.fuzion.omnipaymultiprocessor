<?php
/**
 * Created by IntelliJ IDEA.
 * User: emcnaughton
 * Date: 10/18/17
 * Time: 12:53 PM
 */
$billingLocationID = CRM_Core_BAO_LocationType::getBilling();
return [
  [
    'name' => 'OmniPay - Cybersource',
    'entity' => 'payment_processor_type',
    'metadata' => [
      'billing_fields' => [
        'first_name' => 'billing_first_name',
        'middle_name' => 'billing_middle_name',
        'last_name' => 'billing_last_name',
        'street_address' => "billing_street_address-{$billingLocationID}",
        'city' => "billing_city-{$billingLocationID}",
        'country' => "billing_country_id-{$billingLocationID}",
        'state_province' => "billing_state_province_id-{$billingLocationID}",
        'postal_code' => "billing_postal_code-{$billingLocationID}",
      ],
      'transparent_redirect' => [
        'fields' => [
          'card_type' => [
            'core_field_name' => 'credit_card_type',
            'options' => [
              '' => ts('- select -'),
              '001' => 'Visa',
              '002' => 'Mastercard',
              '003' => 'Amex',
              '004' => 'Discover',
            ],
            'htmlType' => 'select',
            'title' => ts('Card Type'),
          ],
          'card_number' => [
            'htmlType' => 'text',
            'core_field_name' => 'credit_card_number',
            'title' => ts('Card Number'),
            'attributes' => [
              'size' => 20,
              'maxlength' => 20,
              'autocomplete' => 'off',
            ],
          ],
          'card_expiry_date' => [
            'core_field_name' => 'credit_card_exp_date',
            'htmlType' => 'date',
            'title' => ts('Expiration Date'),
            'month_field' => 'card_expiry_date_M',
            'year_field' => 'card_expiry_date_Y',
          ],
          'card_cvn' => [
            'htmlType' => 'text',
            'core_field_name' => 'cvv2',
            'attributes' => [
              'size' => 5,
              'maxlength' => 10,
              'autocomplete' => 'off',
            ],
            'title' => ts('Security Code'),
          ],
        ],
      ],
    ],
    'params' => [
      'version' => 3,
      'title' => 'OmniPay - Cybersource',
      'name' => 'omnipay_Cybersource',
      'description' => 'Omnipay Cybersource Payment Processor',
      'user_name_label' => 'Profile ID',
      'password_label' => 'Access Key',
      'signature_label' => 'Secret Key',
      'class_name' => 'Payment_OmnipayMultiProcessor',
      'url_site_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
      'url_api_default' => 'https://testsecureacceptance.cybersource.com/silent/pay',
      'billing_mode' => 4,
      'payment_type' => 3,
    ],
  ],
];
