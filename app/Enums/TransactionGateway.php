<?php

namespace App\Enums;
enum TransactionGateway: string
{
	case WALLET = 'Wallet';
	case AUTHORIZE = 'Authorize';
	case BLOCKCHAIN = 'Blockchain';
	case BTCPAY = 'BTCPay';
	case CASHMAAL = 'Cashmaal';
	case CHECKOUT = 'Checkout';
	case COINBASECOMMERCE = 'CoinbaseCommerce';
	case COINGATE = 'Coingate';
	case COINPAYMENTS = 'Coinpayments';
	case COINPAYMENTSFIAT = 'CoinpaymentsFiat';
	case FLUTTERWAVE = 'Flutterwave';
	case INSTAMOJO = 'Instamojo';
	case MERCADOPAGO = 'MercadoPago';
	case MOLLIE = 'Mollie';
	case NMI = 'NMI';
	case NOWPAYMENTSCHECKOUT = 'NowPaymentsCheckout';
	case NOWPAYMENTSHOSTED = 'NowPaymentsHosted';
	case PAYEER = 'Payeer';
	case PAYPAL = 'Paypal';
	case PAYPALSDK = 'PaypalSdk';
	case PAYSTACK = 'Paystack';
	case PAYTM = 'Paytm';
	case PERFECTMONEY = 'PerfectMoney';
	case RAZORPAY = 'Razorpay';
	case SKRILL = 'Skrill';
	case STRIPE = 'Stripe';
	case STRIPEJS = 'StripeJs';
	case STRIPEV3 = 'StripeV3';
	case TWOCHECKOUT = 'TwoCheckout';
	case VOGUEPAY = 'Voguepay';

}
