<?php

use Omnipay\Common\Http\Client;
use Omnipay\SagePay\Message\ServerNotifyRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SagepayTestTrait
 *
 * This trait defines a number of helper functions for testing the Sagepay
 * integration.
 */
trait SagepayTestTrait {

  use \Omnipay\SagePay\Traits\ServerNotifyTrait;

  protected function getNewContributionPage($processorID): array {
    return [
      'title' => 'Donate',
      'financial_type_id' => '1',
      'is_credit_card_only' => '0',
      'is_monetary' => '1',
      'is_recur' => '0',
      'is_confirm_enabled' => '0',
      'is_recur_interval' => '0',
      'is_recur_installments' => '0',
      'adjust_recur_start_date' => '0',
      'is_pay_later' => '0',
      'pay_later_text' => 'I will send payment by check',
      'is_partial_payment' => '0',
      'is_allow_other_amount' => '1',
      'is_email_receipt' => '0',
      'is_active' => '1',
      'amount_block_is_active' => '1',
      'start_date' => '2020-02-20 09:17:00',
      'created_date' => '2020-02-20 09:17:53',
      'currency' => 'GBP',
      'is_share' => '0',
      'is_billing_required' => '0',
      'contribution_type_id' => '1',
      'payment_processor' => $processorID,
    ];
  }

  protected function getQfKey(): string {
    return '0e28675d3513bdfba43fca5';
  }


  protected function getNewTransaction(): array {
    return [
      'amount' => 20.00,
      'currency' => 'GBP',
      'description' => '33333-99999-Donation',
      'transactionId' => '99999',
      'clientIp' => '111.111.111.111',
      'returnUrl' => 'https://civi.example.org/civicrm/payment/ipn/99999/1',
      'cancelUrl' => 'https://civi.example.org/civicrm/contribute/transact?_qf_Main_display=1&qfKey=' . $this->getQfkey(),
      'notifyUrl' => 'https://civi.example.org/civicrm/payment/ipn/99999/1',
      'card' => [
        'firstName' => 'Emmy',
        'lastName' => 'Noether',
        'email' => 'emmynoether@sagepayexample.org',
        'billingAddress1' => 'Mathe Strasse',
        'billingAddress2' => '',
        'billingCity' => 'Erlangen',
        'billingPostcode' => 'ERLNGN',
        'billingState' => '',
        'billingCountry' => 'DE',
        'billingPhone' => '',
        'company' => '',
        'type' => '',
        'shippingAddress1' => 'Mathe Strasse',
        'shippingAddress2' => '',
        'shippingCity' => 'Erlangen',
        'shippingPostcode' => 'ERLNGN',
        'shippingState' => '',
        'shippingCountry' => 'DE',
        'cvv' => '',
        'number' => '',
      ],
      'cardReference' => NULL,
      'transactionReference' => NULL,
      'cardTransactionType' => NULL,
    ];
  }

  /**
   * @param $contributionPageID
   * @param $contactID
   * @param $processorID
   * @param $priceSetID
   *
   * @return array
   */
  protected function getContributionPageSubmission($contributionPageID, $contactID, $processorID, $priceSetID): array {
    $newTransaction = $this->getNewTransaction();

    return [
      'id' => $contributionPageID,
      'contact_id' => $contactID,
      'amount' => $newTransaction['amount'],
      'price_set_id' => $priceSetID,
      'payment_processor_id' => $processorID,
    ];
  }

  // This same information is in the mock: SagepayOneoffPaymentSecret.txt
  protected function getSagepayTransactionSecret(): array {
    return [
      'VPSProtocol' => '3.00',
      'Status' => 'OK',
      'StatusDetail' => '2014 : The Transaction was Registered Successfully.',
      'VPSTxId' => '{C46AF0B5-E2D2-6477-4EE4-991BC04B44C4}',
      'SecurityKey' => 'POW8PD7OPZ',
      'NextURL' => 'https://test.sagepay.com/gateway/service/cardselection?vpstxid={C46AF0B5-E2D2-6477-4EE4-991BC04B44C4',
    ];
  }

  /**
   * @param $processorID
   *
   * @return array
   */
  protected function getSagepayPaymentConfirmation($processorID, $contributionID): array {
    return [
      'q' => 'civicrm/payment/ipn/' . $contributionID . '/' . $processorID,
      'processor_id' => $processorID,
      'VPSProtocol' => '3.00',
      'TxType' => 'PAYMENT',
      'VendorTxCode' => $contributionID,
      'VPSTxId' => '{C46AF0B5-E2D2-6477-4EE4-991BC04B44C4}',
      'Status' => 'OK',
      'StatusDetail' => '0000 : The Authorisation was Successful.',
      'TxAuthNo' => '4898041',
      'AVSCV2' => 'SECURITY CODE MATCH ONLY',
      'AddressResult' => 'NOTMATCHED',
      'PostCodeResult' => 'NOTMATCHED',
      'CV2Result' => 'MATCHED',
      'GiftAid' => '0',
      '3DSecureStatus' => 'NOTCHECKED',
      'CardType' => 'VISA',
      'Last4Digits' => '0006',
      'DeclineCode' => '00',
      'ExpiryDate' => '0123',
      'BankAuthCode' => '999777',
      'IDS_request_uri' => '/civicrm/payment/ipn/99999/1',
      'IDS_user_agent' => 'SagePay-Notifier/1.0',
    ];
  }

  /**
   * @param $params
   */
  public function signRequest($params): void {
    Civi::$statics['Omnipay_Test_Config']['request'] = new Request();
    Civi::$statics['Omnipay_Test_Config']['request']->initialize(
      [],
      $params
    );
    $request = new ServerNotifyRequest(new Client(), Civi::$statics['Omnipay_Test_Config']['request']);
    $request->setVendor('abc');
    $request->setSecurityKey('POW8PD7OPZ');
    $params['VPSSignature'] = $request->buildSignature();
    Civi::$statics['Omnipay_Test_Config']['request'] = new Request();
    Civi::$statics['Omnipay_Test_Config']['request']->initialize(
      [],
      $params
    );

  }

}
