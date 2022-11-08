<?php

namespace App\Generators;

use App\Token;

class OpenapiGenerator
{
    public function fromToken(Token $token)
    {
        $paths = [];
        foreach ($token->presets as $preset) {
            $access = $preset->access;
            $resource = '/'.$preset->name;
            $resourceId = $resource.'/:id';

            if ($access->can_read) {
                $paths[$resource]['get'] = [
                    'responses' => [
                        '200' => ['description' => 'List entities'],
                        '401' => ['description' => 'Bad api key'],
                        '403' => ['description' => 'Method not allowed'],
                        '404' => ['description' => 'Preset not found'],
                    ],
                ];

                $paths[$resourceId]['get'] = [
                    'responses' => [
                        '200' => ['description' => 'Single entity'],
                        '401' => ['description' => 'Bad api key'],
                        '403' => ['description' => 'Method not allowed'],
                        '404' => ['description' => 'Preset or entity not found'],
                    ],
                ];
            }

            if ($access->can_create) {
                $paths[$resource]['post'] = [
                    'responses' => [
                        '200' => ['description' => 'Created entity'],
                        '401' => ['description' => 'Bad api key'],
                        '403' => ['description' => 'Method not allowed'],
                        '404' => ['description' => 'Preset not found'],
                    ],
                ];
            }

            if ($access->can_update) {
                $paths[$resourceId]['put'] = [
                    'responses' => [
                        '204' => ['description' => 'Success'],
                        '401' => ['description' => 'Bad api key'],
                        '403' => ['description' => 'Method not allowed'],
                        '404' => ['description' => 'Preset or entity not found'],
                    ],
                ];
            }

            if ($access->can_delete) {
                $paths[$resourceId]['delete'] = [
                    'responses' => [
                        '204' => ['description' => 'Success'],
                        '401' => ['description' => 'Bad api key'],
                        '403' => ['description' => 'Method not allowed'],
                        '404' => ['description' => 'Preset or entity not found'],
                    ],
                ];
            }
        }

        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => $token->name,
                'version' => '1.0.0'
            ],
            'servers' => [
                [
                    'url' => env('APP_URL').'/token/presets',
                ],
            ],
            'paths' => array_filter($paths),
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                    ],
                ],
            ],
            'security' => [
                [
                    'bearerAuth' => [],
                ],
            ],
        ];
    }
}