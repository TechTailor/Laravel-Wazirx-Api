<?php

namespace TechTailor\WazirxApi\Traits;

trait HandlesResponseErrors
{
    private function handleError($response)
    {
        // Set a default error.
        $error = [
            'code'    => '1000',
            'error'   => 'Invalid',
            'message' => 'Unable to identify the type of error.',
        ];

        // Return server related errors (500 range).
        if ($response->serverError()) {
            // TBA
        }
        // Return client related errors.
        elseif ($response->clientError()) {
            // If client error has a response code.
            if (isset($response['code'])) {
                // Switch between known Wazirx error codes.
                switch ($response['code']) {
                    case '1999':
                            $error = [
                                'code'    => '1999',
                                'error'   => 'Api Error',
                                'message' => 'Symbol does not have a valid value.',
                            ];
                            break;
                    case '2000':
                            $error = [
                                'code'    => '2000',
                                'error'   => 'Api Connection Error',
                                'message' => 'Something went wrong. Please contact support.',
                            ];
                            break;
                    case '2005':
                            $error = [
                                'code'    => '2005',
                                'error'   => 'Api Connection Error',
                                'message' => 'API Signature/Secret is Invalid.',
                            ];
                            break;
                    case '2112':
                            $error = [
                                'code'    => '2112',
                                'error'   => 'Api Connection Error',
                                'message' => 'API Key is missing / Invalid API Key.',
                            ];
                            break;
                }
            } else {
                // If client error a response status.
                if ($response->status() === 403) {
                    $error = [
                        'code'    => '403',
                        'error'   => 'Forbidden',
                        'message' => "You don't have permission to access this resouce.",
                    ];
                }
            }

            return $error;
        }
    }
}
