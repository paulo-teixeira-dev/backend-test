<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use Faker\Provider\pt_BR\Person;

class CreateTest extends TestCase
{
    /**
     * Melhorias:
     *  - Isolamento de testes com RefreshDatabase
     *  - Nos testes existentes, não foi identificado o uso do trait RefreshDatabase, que é essencial para garantir que cada teste comece com o banco de dados em um estado limpo e controlado.
     */
    
    /**
     * Teste de criação de usuário quando não autorizado
     *
     * @return void
     */
    public function testCreateWhenUnauthorized()
    {
        $user  = User::factory()->user()->create();
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $body = [
            'document_number' => app(Person::class)->cpf(false),
            'name'            => $this->faker->name,
            'email'           => $this->faker->unique()->freeEmail(),
            'password'        => $this->faker->word,
            'type'            => $this->faker->randomElement(['USER', 'VIRTUAL', 'MANAGER']),
        ];

        $response = $this->postJson('/api/users', $body, $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => false,
                'method'  => 'POST',
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
     * Teste de criação de usuário com sucesso
     *
     * @return void
     */
    public function testCreateWithSuccess()
    {
        $user  = User::factory()->manager()->create();
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $body = [
            'document_number' => app(Person::class)->cpf(false),
            'name'            => $this->faker->name,
            'email'           => $this->faker->unique()->freeEmail(),
            'password'        => $this->faker->word,
            'type'            => $this->faker->randomElement(['USER', 'VIRTUAL', 'MANAGER']),
        ];

        $response = $this->postJson('/api/users', $body, $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'name'  => $body['name'],
                    'email' => $body['email'],
                    'type'  => $body['type'],
                ],
            ],
            true
        );

        $content = json_decode($response->getContent(), true);

        $this->assertDatabaseHas(
            'users',
            [
                'id'              => $content['data']['id'],
                'company_id'      => $user->company_id,
                'document_number' => $body['document_number'],
                'name'            => $body['name'],
                'email'           => $body['email'],
                'type'            => $body['type'],
            ]
        );
    }
}
