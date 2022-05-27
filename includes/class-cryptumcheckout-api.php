<?php

require_once('class-cryptumcheckout-log.php');

// @codeCoverageIgnoreStart
defined('ABSPATH') or exit;
// @codeCoverageIgnoreEnd

class CryptumCheckout_Api
{
	static function get_cryptum_url($environment)
	{
		return $environment == 'production' ? 'https://api.cryptum.io' : 'https://api-dev.cryptum.io';
	}
	static function get_cryptum_store_url($environment)
	{
		return $environment == 'production' ? 'https://api.cryptum.io/plugins' : 'https://api-dev.cryptum.io/plugins';
	}
	static function get_cryptum_checkout_frontend($environment)
	{
		return $environment == 'production' ? 'https://plugin-checkout.cryptum.io/public/payment-details/' : 'https://plugin-checkout-dev.cryptum.io/public/payment-details/';
	}

	static function request($url, $args = array())
	{
		$response = wp_safe_remote_request($url, $args);
		if (is_wp_error($response)) {
			Log::error(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
			return [
				'error' => 'Error',
				'message' => $response->get_error_message()
			];
		}

		$responseObj = $response['response'];
		$responseBody = json_decode($response['body'], true);
		if (isset($responseBody['error']) || (isset($responseObj) && $responseObj['code'] >= 400)) {
			$error_message = isset($responseBody['error']['message']) ? $responseBody['error']['message'] : $responseBody['message'];
			Log::error(json_encode($responseBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
			return [
				'error' => 'Error',
				'message' => $error_message
			];
		}
		return $responseBody;
	}
}