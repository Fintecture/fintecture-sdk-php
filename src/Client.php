<?php

/**
 * Class Fintecture Client
 * v 1.0.0 - 2020-06-18
 *
 * User: gfournel@factomos.com
 * Date: 2020-06-17
 */

namespace Fintecture;

class Client {

    public $client;
    public $fintecture_oauth_url;
    public $fintecture_pis_url;
    public $fintecture_connect_url;
    public $fintecture_app_id;
    public $fintecture_app_secret;
    public $fintecture_private_key;
    public $headers;
    public $access_token;

    /**
     * FintectureClient constructor.
     * @param   $params = [
     *              'FINTECTURE_OAUTH_URL' => Url to get the Access token,
     *              'FINTECTURE_PIS_URL' => Url to initiate a payment (preparing the connect URL),
     *              'FINTECTURE_CONNECT_URL' => Begining of the connect URL for the redirection,
     *              'FINTECTURE_APP_ID' => App ID available in the Fintecture Console (https://console.fintecture.com/),
     *              'FINTECTURE_APP_SECRET' => App Secret available in the Fintecture Console (https://console.fintecture.com/),
     *              'FINTECTURE_PRIVATE_KEY' => Private Key path downloaded from the Fintecture Console (https://console.fintecture.com/),
     *          ]
     */
    public function __construct($params) {

        $this->fintecture_oauth_url = $params['FINTECTURE_OAUTH_URL'];
        $this->fintecture_pis_url = $params['FINTECTURE_PIS_URL'];
        $this->fintecture_connect_url = $params['FINTECTURE_CONNECT_URL'];
        $this->fintecture_app_id = $params['FINTECTURE_APP_ID'];
        $this->fintecture_app_secret = $params['FINTECTURE_APP_SECRET'];
        $this->fintecture_private_key = $params['FINTECTURE_PRIVATE_KEY'];

        $this->client = new \GuzzleHttp\Client();

    }

    /**
     * Function to generate a Universal Unique ID (UUID)
     *
     * @return string - a UUID
     */
    public function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    /**
     * Do a Raw Post of a JSON content
     *
     * @param $url
     * @param bool $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postJson($url, $body = false) {
        $response = $this->client->request('POST', $url, ['headers' => $this->headers, 'json' => $body]);
        return $response;
    }


    /**
     * Do a urlencoded form POST
     *
     * @param $url
     * @param bool $postParameters
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postForm($url, $postParameters = false) {
        $response = $this->client->request('POST', $url, ['headers' => $this->headers, 'form_params' => $postParameters]);
        return $response;
    }

    /**
     * Get the access token needed to generate the connect URL
     *
     * @return string - the access token
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getAccessToken(){
        $url = $this->fintecture_oauth_url . '/oauth/accesstoken';
        $this->headers = [
            'Authorization' => 'Basic ' . base64_encode($this->fintecture_app_id . ':' . $this->fintecture_app_secret),
            'Accept' => 'application/json',
        ];
        $postParameters = [
            'grant_type' => 'client_credentials',
            'app_id' => $this->fintecture_app_id,
            'scope' => 'PIS',
        ];

        $response = $this->postForm($url, $postParameters);

        $responseObject = json_decode((string)$response->getBody());
        $this->access_token = $responseObject->access_token;
        return $this->access_token;
    }

    /**
     * Generate the Connect URL in order to redirect the buyer to the fintecture payment page
     *
     * @param $data = [
     *              'meta' => [
     *                  'psu_name' => Buyer Name,
     *                  'psu_email' => Buyer Email,
     *                  'psu_address' => [
     *                      'street' => Buyer Street,
     *                      'zip' => Buyer Zipcode,
     *                      'city' => Buyer City,
     *                      'country' => Buyer Country Iso Code (ex: FR),
     *                  ],
     *              ],
     *              'data' => [
     *                  'type' => 'SEPA',
     *                  'attributes' => [
     *                      'amount' => Amount to pay,
     *                      'currency' => 'EUR',
     *                      'communication' => Text to display for the payment,
     *                      'beneficiary' => [
     *                          'name' => Name of the beneficiary,
     *                          'street' => Street of the beneficiary ,
     *                          'number' => '',
     *                          'city' =>  City of the beneficiary,
     *                          'zip' => Zipcode of the beneficiary,
     *                          'country' => Country Iso code of the beneficiary,
     *                          'iban' => Iban of the beneficiary,
     *                          'swift_bic' => Bic of the beneficiary,
     *                          'bank_name' => Name of the bank of the beneficiary,
     *                      ],
     *                  ],
     *              ],
     *          ]
     * @param string $state - an ID that will be sent to the callback
     * @return array[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateConnectURL($data, $state = '') {

        $output = [
            'error' => [
                'code' => 0,
                'message' => 'No error',
            ]
        ];

        $access_token = $this->getAccessToken();

        $url = $this->fintecture_pis_url . '/pis/v1/prepare';

        $digest = 'SHA-256=' . base64_encode(hash('sha256',json_encode($data), true));

        $x_date = date('r');
        $x_request_id = $this->gen_uuid();

        $signing_string = '';
        $signing_string .= 'x-date: ' . $x_date . "\n";
        $signing_string .= 'digest: ' . $digest . "\n";
        $signing_string .= 'x-request-id: ' . $x_request_id;

        openssl_sign($signing_string, $crypted_string, $this->fintecture_private_key,OPENSSL_ALGO_SHA256);

        $signature = 'keyId="' . $this->fintecture_app_id . '",algorithm="rsa-sha256",headers="x-date digest x-request-id",signature="' . base64_encode($crypted_string) . '"';

        $this->headers = [
            'Authorization' => 'Bearer ' . $access_token,
            'Signature' => $signature,
            'Digest' => $digest,
            'X-Date' => $x_date,
            'X-Request-Id' => $x_request_id,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $response = $this->postJson($url, $data);

        $responseObject = json_decode((string)$response->getBody(), true);

        if(isset($responseObject['meta']['code']) && $responseObject['meta']['code'] == 201) {

            $payload = [
                'meta' => [
                    'session_id' => $responseObject['meta']['session_id'],
                ],
                'data' => [
                    'attributes' => [
                        'amount' => $data['data']['attributes']['amount'],
                        'currency' => 'EUR',
                        'beneficiary' => [
                            'name' => $data['data']['attributes']['beneficiary']['name'],
                        ],
                    ],
                ],
            ];

            $digest = 'SHA-256=' . base64_encode(hash('sha256',json_encode($payload), true));

            $x_date = date('r');
            $x_request_id = $this->gen_uuid();

            $signing_string = '';
            $signing_string .= 'digest: ' . $digest . "\n";
            $signing_string .= 'x-date: ' . $x_date . "\n";
            $signing_string .= 'x-request-id: ' . $x_request_id;

            openssl_sign($signing_string, $crypted_string2, $this->fintecture_private_key,OPENSSL_ALGO_SHA256);

            $config = base64_encode(json_encode([
                'app_id' => $this->fintecture_app_id,
                'access_token' => $access_token,
                'date' => $x_date,
                'request_id' => $x_request_id,
                'signature_type' => 'rsa-sha256',
                'signature' => base64_encode($crypted_string2),
                'payload' => $payload,
                'state' => $state,
                'psu_type' => 'retail',
                'country' => 'fr',
                'language' => 'fr',
            ]));

            $connectUrl = $this->fintecture_connect_url . '/pis?config=' . $config;

            $output['data'] = [
                'connect_url' => $connectUrl,
                'session_id' => $responseObject['meta']['session_id'],
            ];

        }

        return $output;

    }

}