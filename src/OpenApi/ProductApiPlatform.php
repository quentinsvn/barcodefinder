<?php 

declare(strict_types = 1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use ArrayObject;
use UnexpectedValueException;

class ProductApiPlatform implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $openApiFactory;

    public function __construct(OpenApiFactoryInterface $openApiFactory)
    {
        $this->openApiFactory = $openApiFactory;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->openApiFactory)($context);
        $components = $openApi->getComponents();
        $schemas = $components->getSchemas();
        if (null === $schemas) {
            throw new UnexpectedValueException('Failed to obtain OpenApi schemas');
        }

        $schemas['Product'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'isbn' => [
                    'type' => 'int',
                    'example' => '9791090085954',
                ],
            ],
        ]);

          $schemas['ProductResponse'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'categorie' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
                'description' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
                'title' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
                'img' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
                'isbn' => [
                    'type' => 'int',
                    'example' => 'int',
                ],
                'barcode' => [
                    'type' => 'text',
                    'example' => 'text'
                ],
            ],
        ]);
        
        $pathItem = new PathItem(
            ref: 'Product',
            post: new Operation(
                operationId: 'getProductItem',
                tags: ['Product'],
                responses: [
                    '200' => [
                        'description' => 'Exemple des types de réponses retournées par la requête',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/ProductResponse',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Récupérer les informations d\'un produit par son ISBN',
                requestBody: new Model\RequestBody(
                    description: 'Permet d\'obtenir les différentes informations d\'un produit selon son ISBN',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Product',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $paths = $openApi->getPaths();
        $paths->addPath('/api/product/{isbn}', $pathItem);
        return $openApi;
    
    }
}