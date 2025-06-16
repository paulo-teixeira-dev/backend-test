<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;

class ShowTest extends TestCase
{
    /**
     * Melhorias:
     *  - Isolamento de testes com RefreshDatabase
     *  - Nos testes existentes, não foi identificado o uso do trait RefreshDatabase, que é essencial para garantir que cada teste comece com o banco de dados em um estado limpo e controlado.
     */
    
    /**
     * Teste de dados de usuário quando não autorizado
     *
     * @return void
     */
    public function testShowWhenUnauthorized()
    {
        $user1 = User::factory()->user()->create();
        $user2 = User::factory()->create(
            [
                'company_id' => $user1->company_id,
            ]
        );
        $token = $user1->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = $this->get("/api/users/$user2->id", $headers);

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
     * Teste de busca de dados do próprio usuário logado
     *
     * @return void
     */
    public function testShowWhenSearchingAsLoggedUser()
    {
        $user = User::factory()->user()->create();
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = $this->get("/api/users/$user->id", $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'email'           => $user->email,
                    'type'            => $user->type,
                    'document_number' => $user->document_number,
                ],
            ],
            true
        );
    }

    /**
     * Teste de busca de dados quando usuário logado é gestor
     *
     * @return void
     */
    public function testShowWhenSearchingAsManager()
    {
        $user1 = User::factory()->manager()->create();
        $user2 = User::factory()->create(
            [
                'company_id' => $user1->company_id,
            ]
        );
        $token = $user1->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = $this->get("/api/users/$user2->id", $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'id'              => $user2->id,
                    'name'            => $user2->name,
                    'email'           => $user2->email,
                    'type'            => $user2->type,
                    'document_number' => $user2->document_number,
                ],
            ],
            true
        );
    }
}
