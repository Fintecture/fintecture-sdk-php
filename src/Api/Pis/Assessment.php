<?php

namespace Fintecture\Api\Pis;

use Fintecture\Api\Api;
use Fintecture\Api\ApiResponse;

class Assessment extends Api
{
    /**
     * Assessment.
     *
     * @param string $siren Siren.
     * @param float $amount Amount.
     *
     * @return ApiResponse Assessment response.
     */
    public function get(
        string $siren,
        float $amount,
    ): ApiResponse {
        $path = '/assessments';

        $data = [
            'data' => [
                'type' => 'assessments',
                'attributes' => [
                    'amount' => [
                        'value' => (string) $amount,
                        'currency' => 'EUR',
                    ],
                    'category' => 'payments',
                ],
                'relationships' => [
                    'company' => [
                        'data' => [
                            'type' => 'companies',
                            'attributes' => [
                                'external_id' => $siren,
                                'external_id_type' => 'SIREN',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->apiWrapper->post($path, $data, true, null, 4);
    }
}
