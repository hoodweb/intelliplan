<?php

namespace Lundco;

use Lundco\Intelliplan\Request;

class Intelliplan extends \Object
{
	/**
	 * @config string
	 * */
	protected $partner_code;

	/**
	 * @config string
	 * */
	protected $client_name;

	/**
	 * @config string
	 * */
	protected $api_domain;

	/**
	 * @var string
	 * */
	protected $session_id;

	/**
	 * @param string $partner_code
	 * */
	public function setPartnerCode($partner_code)
	{
		$this->partner_code = $partner_code;
	}

	/**
	 * @return string
	 * Returns partner code or sets it to config if unset.
	 * */
	public function getPartnerCode()
	{
		if(empty($this->partner_code)){
			$this->setPartnerCode($this->config()->partner_code);
		}

		return $this->partner_code;
	}

	/**
	 * @param string $client_name
	 * */
	public function setClientName($client_name)
	{
		$this->client_name = $client_name;
	}

	/**
	 * @return string
	 * */
	public function getClientName()
	{
		if(empty($this->partner_code)){
			$this->setClientName($this->config()->client_name);
		}

		return $this->client_name;
	}

	/**
	 * @param $domain
	 */
	public function setApiDomain($domain)
	{
		$this->api_domain = $domain;
	}

	/**
	 * @return string example: app.intelliplan.eu/
	 * Expected with trailing slash
	 * */
	public function getApiDomain()
	{
		if(empty($this->api_domain)){
			$this->setApiDomain($this->config()->api_domain);
		}

		return $this->api_domain;
	}

	/**
	 * @param string $session_id
	 * */
	public function setSessionID($session_id)
	{
		$this->session_id = $session_id;
	}

	/**
	 * @return string
	 */
	public function getSessionID()
	{
		return $this->session_id;
	}

	/**
	 * @param string $api_function "The function that is called with the request fx. 'CandidatesUserAccounts/CreateAccount'"
	 * @return Object $this
	 * */
	public function createRequest($api_function)
	{
		return Request::create(
			$api_function,
			$this->getApiDomain(),
			$this->getClientName(),
			$this->getPartnerCode()
		);
	}

	/**
	 * @param string $email
	 * @return array
	 * Returns response
	 * */
	public function createAccount($email)
	{
		$request = $this->createRequest('CandidatesUserAccounts/CreateAccount');
		$request->setMethod('POST');
		$request->setHeaders(array(
			'Accept: application/json'
		));
		$request->setFormData(array('email' => $email));
		return $request->execute();
	}

	/**
	 * @param $ticket
	 * @return array
	 * Returns response
	 */
	public function loginUsingTicket($ticket)
	{
		$request = $this->createRequest('CandidatesUserAccounts/LogonUsingTicket');
		$request->setMethod('POST');
		$request->setHeaders(array(
			'Accept: application/json',
			'Cache-Control: no-cache'
		));
		$request->setFormData(array('account_ticket' => $ticket));
		$response = $request->execute();
		if($response["data"]["intelliplan_session_id"]){
			$this->setSessionID($response["data"]["intelliplan_session_id"]);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param array $data
	 * @return mixed array("first_name" => '', "surname" => "city", '', "zip_code" => '', "street_address" => '', "mobile_phone" => '')
	 * Returns response
	 */
	public function setAccountData($data = array())
	{
		$request = $this->createRequest('Candidates/SetPersonalInformation');
		$request->setMethod('POST');
		$request->setHeaders(array(
			'Accept: application/json',
			'Cache-Control: no-cache',
			'intelliplan_session_id: ' . $this->getSessionID()
		));
		$request->setFormData($data);
		return $request->execute();
	}
}
