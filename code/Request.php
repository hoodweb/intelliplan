<?php

namespace Lundco\Intelliplan;

class Request extends \Object
{
	/**
	 * @var
	 * Core curl var
	 * */
	protected $curl;

	/**
	 * Request constructor. API function to be called with this request
	 * @param $api_function
	 * @param $api_domain
	 * @param $client_name
	 * @param $partner_code
	 */
	public function __construct($api_function, $api_domain, $client_name, $partner_code)
	{
		if(empty($api_domain)){
			user_error("No api domain specified", E_USER_ERROR);
		}
		if(empty($client_name)){
			user_error("No client name specified", E_USER_ERROR);
		}
		if(empty($partner_code)){
			user_error("No partner code specified", E_USER_ERROR);
		}

		$url = 'https://'
			. $client_name
			. '.'
			. $api_domain
			. '/'
			. $api_function
			. '?partner_code=' . $partner_code;
		$this->curl = curl_init($url);
		return $this;
	}

	/**
	 * @param string $method "POST" or "GET"
	 * @return object $this
	 * sets method to either POST or GET
	 * */
	public function setMethod($method)
	{
		if(strtolower($method) == 'post'){
			curl_setopt($this->curl, CURLOPT_POST, 1);
		}
		return $this;
	}

	/**
	 * @param array $data
	 * @return object $this
	 * */
	public function setFormData($data)
	{
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		return $this;
	}

	/**
	 * @param array $headers
	 * @return object $this
	 * */
	public function setHeaders($headers)
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
		return $this;
	}

	/**
	 * @return array reponse
	 * */
	public function execute()
	{
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		$repsonse = curl_exec($this->curl);
		curl_close($this->curl);
		return json_decode($repsonse, true);
	}
}
