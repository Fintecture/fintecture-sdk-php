<?php

/**
 * Class Fintecture Client
 * v 1.1- 2020-12-03
 *
 * User: gfournel@factomos.com
 * Date: 2020-06-25
 * 
 * User: bastien@fintecture.com
 * Date: 2020-12-03
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
    private function gen_uuid() {
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
     * Do a Get http query
     *
     * @param $url
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function get($url, $generateHeaders = true) {
        $method = 'GET';
        if($generateHeaders == true){
            $this->generateHeaders($method);
        }
        $response = $this->client->request($method, $url, ['headers' => $this->headers]);
        return $response;
    }

    /**
     * Do a Raw Post of a JSON content
     *
     * @param $url
     * @param bool $body
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function postJson($url, $body = false) {
        $method = 'POST';
        $this->generateHeaders($method, $body);
        $response = $this->client->request($method, $url, ['headers' => $this->headers, 'json' => $body]);
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
    private function postForm($url, $postParameters = false) {
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
     * Generate headers for the request with the signature and the digest.
     * 
     * @param string $method The name of the method using for the request.
     * @param $body The body of the request. It's used to generate the digest for the signature
     */
    private function generateHeaders(string $method, $body = null): void
    {

        $access_token = $this->getAccessToken();

        $x_date = date('r');
        $x_request_id = $this->gen_uuid();

        $signing_string = '';
        $signing_string .= 'x-date: ' . $x_date . "\n";
        if($method === 'POST'){
            $digest = 'SHA-256=' . base64_encode(hash('sha256',json_encode($body,JSON_UNESCAPED_UNICODE), true));
            $signing_string .= 'digest: ' . $digest . "\n";
        } 
        
        $signing_string .= 'x-request-id: ' . $x_request_id;

        openssl_sign($signing_string, $crypted_string, $this->fintecture_private_key,OPENSSL_ALGO_SHA256);

        if($method === 'POST'){
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
        } else if($method === 'GET'){
            $signature = 'keyId="' . $this->fintecture_app_id . '",algorithm="rsa-sha256",headers="x-date x-request-id",signature="' . base64_encode($crypted_string) . '"';
            $this->headers = [
                'Authorization' => 'Bearer ' . $access_token,
                'Signature' => $signature,
                'X-Date' => $x_date,
                'X-Request-Id' => $x_request_id,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];
        }
    }

    /**
     * Generate the Connect URL in order to redirect the buyer to the fintecture payment page
     *
     * @param $body = [
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
    public function generateConnectURL($body, string $state = '') :array
    {
        $output = [
            'error' => [
                'code' => 0,
                'message' => 'No error',
            ]
        ];

        $url = $this->fintecture_pis_url . '/pis/v1/prepare';

        $response = $this->postJson($url, $body);

        $responseObject = json_decode((string)$response->getBody(), true);

        if(isset($responseObject['meta']['code']) && $responseObject['meta']['code'] == 201) {

            $payload = [
                'meta' => [
                    'session_id' => $responseObject['meta']['session_id'],
                ],
                'data' => [
                    'attributes' => [
                        'amount' => $body['data']['attributes']['amount'],
                        'currency' => 'EUR',
                        'beneficiary' => [
                            'name' => htmlentities($body['data']['attributes']['beneficiary']['name']),
                        ],
                    ],
                ],
            ];

            $digest = 'SHA-256=' . base64_encode(hash('sha256',json_encode($payload,JSON_UNESCAPED_UNICODE), true));

            $x_date = date('r');
            $x_request_id = $this->gen_uuid();

            $signing_string = '';
            $signing_string .= 'digest: ' . $digest . "\n";
            $signing_string .= 'x-date: ' . $x_date . "\n";
            $signing_string .= 'x-request-id: ' . $x_request_id;

            openssl_sign($signing_string, $crypted_string2, $this->fintecture_private_key,OPENSSL_ALGO_SHA256);

            $config = base64_encode(json_encode([
                'app_id' => $this->fintecture_app_id,
                'access_token' => $this->access_token,
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

        } else {
            $output['error']['code'] = $responseObject['meta']['code'];
            $output['error']['message'] = 'error';
        }

        return $output;

    }



    /**
     * Get a payment info by its session_id (needed in the callback to verify the payment status)
     *
     * @param $session_id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPayment(string $session_id) {

        $url = $this->fintecture_pis_url . '/pis/v1/payments/' . $session_id;

        $response = $this->get($url);

        $responseObject = json_decode((string)$response->getBody(), true);

        return $responseObject;

    }

    /**
     * Get a provider info by its providerId 
     *
     * @param $providerId The id of the provider
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProvider(string $providerId)
    {

        $url = $this->fintecture_pis_url . '/res/v1/providers/' . $providerId;
        
        $this->headers = [
            'app_id' => $this->fintecture_app_id,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $response = $this->get($url, false);

        $responseObject = json_decode((string)$response->getBody(), true);

        return $responseObject;

    }

    /**
     * Get all providers info
     *
     * @param array $params - array with different filter = 
     * [
     *      'filter[country]' => 'FR' ,
     *      'filter[ais]' => 'Accounts',
     *      'filter[pis]' => 'SEPA',
     *      'filter[psu_type]' => 'retail',
     *      'filter[auth_model]' => 'redirect',
     *      'sort[name]' => 'ASC',
     *      'sort[full_name]' => 'ASC',
     *      'sort[country]' => 'ASC',
     *      'sort[provider_id]' => 'ASC'
     * ]
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProviders(array $params = null)
    {

        if ($params !== null){
            $params = http_build_query($params);
            $url = $this->fintecture_pis_url . '/res/v1/providers?'. $params;
        } else {
            $url = $this->fintecture_pis_url . '/res/v1/providers';
        }

        $this->headers = [
            'app_id' => $this->fintecture_app_id,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $response = $this->get($url, false);
        
        return json_decode((string)$response->getBody(), true);
    }


    /**
     * Initiate a payment 
     *
     * @param array $body = [
     *                  'meta' => [
     *                      // Info of the buyer
     *                        'psu_name' => 'M. John Doe',
     *                      'psu_email' => 'john@doe.com',
     *                      'psu_address' => [
     *                          'street' => 'Void Street',
     *                          'number' => '3',
     *                          'zip' => '12345',
     *                          'city' => 'Gotham',
     *                          'country' => 'FR',
     *                      ],
     *                      'psu_phone' => '0601020304'
     *                  ],
     *                  'data' => [
     *                      'type' => 'PIS',
     *                      'attributes' => [
     *                          'amount' => '550.60',
     *                          'currency' => 'EUR',
     *                          'communication' => 'Commande N15654',
     *                          'beneficiary' => [
     *                              'name' => 'World Company',
     *                              'street' => 'power street',
     *                              'number' => '3',
     *                              'city' => 'Atlantis',
     *                              'zip' => '12345',
     *                              'country' => 'FR',
     *                              'iban' => 'FR7630001007941234567890185',
     *                              'swift_bic' => 'BDFEFRPPCCT'
     *                          ],
     *                      ],
     *                  ],
     *              ]
     * @param string $providerId - The unique identifier of the provider
     * @param string $redirectUri - The redirect uri where to redirect the user after the authentication
     * @param string $state - an ID that will be sent to the callback
     * @return array[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postInitiate(array $body, string $providerId, string $redirectUri, string $state = '') {

        $params = [
            'redirect_uri' => $redirectUri,
            'state' => $state
        ];
        $params = http_build_query($params);
        $url = $this->fintecture_pis_url . '/pis/v1/provider/' . $providerId . '/initiate?'. $params;
        
        $response = $this->postJson($url, $body);
        
        return json_decode((string)$response->getBody(), true);
    }

    /**
     * The function validate the webhook received by Fintecture.
     * 
     * @param array $headers - The headers of the weebhook. Must be contains the digest, the date, the x-request-id and the signature.
     * @param $body - The body of the request.
     * 
     * @return bool
     */
    public function validateWebhook(Array $headers, $body) :bool
    {
        $digest = $headers['digest'];
        $date = $headers['date'];
        $x_request_id = $headers['x-request-id'];
        $signature = explode(",",$headers['signature']);        

        $digestGenerate = 'SHA-256=' . base64_encode(hash('sha256',$body, true));

        if($digest === $digestGenerate){

            $signingString = "date: ".$date."\ndigest: ".$digest."\nx-request-id: ".$x_request_id;

            openssl_private_decrypt(base64_decode(explode("=",$signature[3])[1]), $decrypted, $this->fintecture_private_key, OPENSSL_PKCS1_OAEP_PADDING);

            if($signingString === $decrypted){
                return true;
            }
        }
        return false;
    }


}