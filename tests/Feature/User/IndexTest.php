<?php

namespace Tests\Feature\User;

use App\Models\Account;
use Tests\TestCase;
use App\Models\User;

class IndexTest extends TestCase
{
    /**
     * Melhorias:
     *  - Isolamento de testes com RefreshDatabase
     *  - Nos testes existentes, não foi identificado o uso do trait RefreshDatabase, que é essencial para garantir que cada teste comece com o banco de dados em um estado limpo e controlado.
     */
    
    /**
     * Teste de lista de usuários quando não autorizado
     *
     * @return void
     */
    public function testIndexWhenUnauthorized()
    {
        $user  = User::factory()->user()->create();
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = $this->get('/api/users', $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => false,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 146001003,
                    ]
                ]
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'errors',
            ]
        );
    }

    /**
     * Teste de lista de usuários com filtro de nome
     *
     * @return void
     */
    public function testIndexWithNameFilter()
    {
        User::factory()->create();
        $user  = User::factory()->manager()->create();
        User::factory()->create(
            [
                'company_id' => $user->company_id,
            ]
        );
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $body = [
            'name' => $user->name,
        ];

        $response = $this->get('/api/users?' . http_build_query($body), $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'objects' => [
                        [
                            'id'    => $user->id,
                            'name'  => $user->name,
                            'email' => $user->email,
                            'type'  => $user->type,
                        ]
                    ]
                ],
                'total' => 1,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]
        );
    }

    /**
     * Teste de lista de usuários com filtro de email
     *
     * @return void
     */
    public function testIndexWithEmailFilter()
    {
        User::factory()->create();
        $user  = User::factory()->manager()->create();
        User::factory()->create(
            [
                'company_id' => $user->company_id,
            ]
        );
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $body = [
            'email' => $user->email,
        ];

        $response = $this->get('/api/users?' . http_build_query($body), $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'objects' => [
                        [
                            'id'    => $user->id,
                            'name'  => $user->name,
                            'email' => $user->email,
                            'type'  => $user->type,
                        ]
                    ]
                ],
                'total' => 1,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]
        );
    }

    /**
     * Teste de lista de usuários com filtro de status (INACTIVE)
     *
     * @return void
     */
    public function testIndexWithStatusInactiveFilter()
    {
        User::factory()->create();
        $user1 = User::factory()->manager()->create();
        $user2 = User::factory()->create(
            [
                'company_id' => $user1->company_id,
            ]
        );
        Account::factory()->create(
            [
                'user_id' => $user2->id,
            ]
        );

        $token = $user1->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $body = [
            'status' => 'INACTIVE',
        ];

        $response = $this->get('/api/users?' . http_build_query($body), $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'objects' => [
                        [
                            'id'    => $user1->id,
                            'name'  => $user1->name,
                            'email' => $user1->email,
                            'type'  => $user1->type,
                        ]
                    ]
                ],
                'total' => 1,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]
        );
    }

    /**
     * Teste de lista de usuários com filtro de status (ACTIVE)
     *
     * @return void
     */
    public function testIndexWithStatusActiveFilter()
    {
        User::factory()->create();
        $user1 = User::factory()->manager()->create();
        Account::factory()->active()->create(
            [
                'user_id' => $user1->id,
            ]
        );
        $user2 = User::factory()->create(
            [
                'company_id' => $user1->company_id,
            ]
        );
        Account::factory()->block()->create(
            [
                'user_id' => $user2->id,
            ]
        );

        $token = $user1->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $body = [
            'status' => 'ACTIVE',
        ];

        $response = $this->get('/api/users?' . http_build_query($body), $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'objects' => [
                        [
                            'id'    => $user1->id,
                            'name'  => $user1->name,
                            'email' => $user1->email,
                            'type'  => $user1->type,
                        ]
                    ]
                ],
                'total' => 1,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]
        );
    }

    /**
     * Teste de lista de usuários com filtro de status (ACTIVE)
     *
     * @return void
     */
    public function testIndexWithStatusBlockFilter()
    {
        User::factory()->create();
        $user1 = User::factory()->manager()->create();
        Account::factory()->block()->create(
            [
                'user_id' => $user1->id,
            ]
        );
        $user2 = User::factory()->create(
            [
                'company_id' => $user1->company_id,
            ]
        );
        Account::factory()->active()->create(
            [
                'user_id' => $user2->id,
            ]
        );

        $token = $user1->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $body = [
            'status' => 'BLOCK',
        ];

        $response = $this->get('/api/users?' . http_build_query($body), $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'objects' => [
                        [
                            'id'    => $user1->id,
                            'name'  => $user1->name,
                            'email' => $user1->email,
                            'type'  => $user1->type,
                        ]
                    ]
                ],
                'total' => 1,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]
        );
    }

    /**
     * Teste de lista de usuários sem filtro
     *
     * @return void
     */
    public function testIndexWithoutFilter()
    {
        User::factory()->create();
        $user1 = User::factory()->manager()->create(
            [
                'name' => 'A',
            ]
        );
        $user2 = User::factory()->create(
            [
                'name'       => 'B',
                'company_id' => $user1->company_id,
            ]
        );
        $token = $user1->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = $this->get('/api/users', $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'objects' => [
                        [
                            'id'    => $user1->id,
                            'name'  => $user1->name,
                            'email' => $user1->email,
                            'type'  => $user1->type,
                        ],
                        [
                            'id'    => $user2->id,
                            'name'  => $user2->name,
                            'email' => $user2->email,
                            'type'  => $user2->type,
                        ]
                    ]
                ],
                'total' => 2,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'current_page',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]
        );
    }
}
