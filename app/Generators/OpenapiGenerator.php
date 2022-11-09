<?php

namespace App\Generators;

use App\Preset;
use App\Token;

class OpenapiGenerator
{
    protected $title = '';
    protected $paths = [];

    public function fromToken(Token $token)
    {
        $this->addOpenapiMethods();
        $this->loadToken($token);
        return $this->generate();
    }

    public function loadToken(Token $token)
    {
        $this->setTitle($token->name);

        foreach ($token->presets as $preset) {
            $this->addPresetMethods($preset);
        }
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function addMethod(string $method, string $resource, array $operation)
    {
        $this->paths[$resource][$method] = $operation;
    }

    public function addOpenapiMethods()
    {
        $this->addMethod('get', '/openapi', [
            'summary' => 'Get this document',
            'responses' => [
                '200' => ['description' => 'Success'],
                '401' => ['description' => 'Bad api key'],
            ],
        ]);
    }

    public function addPresetMethods(Preset $preset)
    {
        $access = $preset->access;
        $name = $preset->name;
        $resource = '/presets/'.$preset->name;
        $resourceId = $resource.'/{id}';
        $parameterId = [
            'name' => 'id',
            'in' => 'path',
            'description' => 'Entity ID',
            'required' => true,
            'schema' => [
                'type' => 'integer',
            ],
        ];

        if ($access->can_read) {
            $this->addMethod('get', $resource, [
                'summary' => "List $name",
                'responses' => [
                    '200' => ['description' => 'List entities'],
                    '401' => ['description' => 'Bad api key'],
                    '403' => ['description' => 'Method not allowed'],
                    '404' => ['description' => 'Preset not found'],
                ],
            ]);

            $this->addMethod('get', $resourceId, [
                'summary' => "Single $name",
                'parameters' => [
                    $parameterId,
                ],
                'responses' => [
                    '200' => ['description' => 'Single entity'],
                    '401' => ['description' => 'Bad api key'],
                    '403' => ['description' => 'Method not allowed'],
                    '404' => ['description' => 'Preset or entity not found'],
                ],
            ]);
        }

        if ($access->can_create) {
            $this->addMethod('post', $resource, [
                'summary' => "Create $name",
                'responses' => [
                    '200' => ['description' => 'Created entity'],
                    '401' => ['description' => 'Bad api key'],
                    '403' => ['description' => 'Method not allowed'],
                    '404' => ['description' => 'Preset not found'],
                ],
            ]);
        }

        if ($access->can_update) {
            $this->addMethod('put', $resourceId, [
                'summary' => "Update $name",
                'parameters' => [
                    $parameterId,
                ],
                'responses' => [
                    '204' => ['description' => 'Success'],
                    '401' => ['description' => 'Bad api key'],
                    '403' => ['description' => 'Method not allowed'],
                    '404' => ['description' => 'Preset or entity not found'],
                ],
            ]);
        }

        if ($access->can_delete) {
            $this->addMethod('delete', $resourceId, [
                'summary' => "Delete $name",
                'parameters' => [
                    $parameterId,
                ],
                'responses' => [
                    '204' => ['description' => 'Success'],
                    '401' => ['description' => 'Bad api key'],
                    '403' => ['description' => 'Method not allowed'],
                    '404' => ['description' => 'Preset or entity not found'],
                ],
            ]);
        }
    }

    public function generate()
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => $this->title,
                'version' => '1.0.0'
            ],
            'servers' => [
                [
                    'url' => env('APP_URL').'/token',
                ],
            ],
            'paths' => array_filter($this->paths),
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