<?php

namespace App\Generators;

use App\Models\v1\Preset;
use App\Models\v1\Token;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\Yaml\Yaml;

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
                '200' => [
                    'description' => 'Success',
                    'content' => [
                        'application/yaml' => [
                            'schema' => [
                                'type' => 'string',
                            ],
                        ],
                    ],
                ],
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

        $idProperty = [
            'id' => [
                'type' => 'integer',
                'example' => 1,
            ],
        ];

        $nameProperty = [
            'name' => [
                'type' => 'string',
                'example' => 'Entity Name',
            ],
        ];

        $timestampProperties = [
            'created_at' => [
                'type' => 'string',
                'example' => '2000-12-31T23:59:59.000000Z',
            ],
            'updated_at' => [
                'type' => 'string',
                'example' => '2000-12-31T23:59:59.000000Z',
            ],
        ];

        $tagsPropterty = [
            'tags' => [
                'type' => 'array',
                'items' => [
                    'type' => 'string',
                ],
            ],
        ];

        $presetProperties = [];
        foreach ($preset->fields as $field) {
            $presetProperties[$field->code] = [
                'type' => 'string',
                'description' => $field->type,
            ];
        }

        $readSchema = [
            'required' => [
                'id',
                'name',
            ],
            'type' => 'object',
            'properties' => array_merge($idProperty, $nameProperty, $presetProperties, $timestampProperties, $tagsPropterty),
        ];

        $updateSchema = [
            'required' => [
                'name',
            ],
            'type' => 'object',
            'properties' => array_merge($nameProperty, $presetProperties),
        ];

        $createdSchema = [
            'required' => [
                'id',
            ],
            'type' => 'object',
            'properties' => array_merge($idProperty),
        ];

        if ($access->can_read) {
            $this->addMethod('get', $resource, [
                'summary' => "List $name",
                'responses' => [
                    '200' => [
                        'description' => 'List entities',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => $readSchema,
                                ],
                            ],
                        ],
                    ],
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
                    '200' => [
                        'description' => 'Single entity',
                        'content' => [
                            'application/json' => [
                                'schema' => $readSchema,
                            ],
                        ],
                    ],
                    '401' => ['description' => 'Bad api key'],
                    '403' => ['description' => 'Method not allowed'],
                    '404' => ['description' => 'Preset or entity not found'],
                ],
            ]);
        }

        if ($access->can_create) {
            $this->addMethod('post', $resource, [
                'summary' => "Create $name",
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => $updateSchema,
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Created entity',
                        'content' => [
                            'application/json' => [
                                'schema' => $createdSchema,
                            ],
                        ],
                    ],
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
                'requestBody' => [
                    'required' => true,
                    'content' => [
                        'application/json' => [
                            'schema' => $updateSchema,
                        ],
                    ],
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

    public function generateYaml()
    {
        return str_replace('{  }', '[]', Yaml::dump($this->generate(), 16, 2));
    }

    public function responseYaml()
    {
        return response($this->generateYaml())
            ->header('Content-Type', 'application/yaml')
            ->header('Content-Disposition', HeaderUtils::makeDisposition('inline', $this->title.'.yaml'));
    }
}