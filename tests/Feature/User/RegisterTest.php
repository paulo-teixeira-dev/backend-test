<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\Company;

class RegisterTest extends TestCase
{

    /**
     * Melhorias:
     *  - Isolamento de testes com RefreshDatabase
     *  - Nos testes existentes, não foi identificado o uso do trait RefreshDatabase, que é essencial para garantir que cada teste comece com o banco de dados em um estado limpo e controlado.
     */
    
    /**
     * Teste de registro de novo usuário com sucesso
     *
     * @return void
     */
    public function testRegisterWithSuccess()
    {
        $body = [
            'user_document_number'    => app(Person::class)->cpf(false),
            'user_name'               => $this->faker->name,
            'company_document_number' => app(Company::class)->cnpj(false),
            'company_name'            => $this->faker->company,
            'email'                   => $this->faker->unique()->freeEmail(),
            'password'                => $this->faker->word,
        ];

        $response = $this->postJson('/api/users/register', $body);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'user' => [
                        'name' => $body['user_name'],
                    ],
                    'company' => [
                        'name' => $body['company_name'],
                    ],
                ],
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'data' => [
                    'user' => [
                        'id',
                        'name',
                    ],
                    'company' => [
                        'id',
                        'name',
                    ],
                    'access_token'
                ]
            ]
        );

        $content = json_decode($response->getContent(), true);

        $this->assertDatabaseHas(
            'companies',
            [
                'id'              => $content['data']['company']['id'],
                'document_number' => $body['company_document_number'],
                'name'            => $body['company_name'],
            ]
        );

        $this->assertDatabaseHas(
            'users',
            [
                'id'              => $content['data']['user']['id'],
                'document_number' => $body['user_document_number'],
                'name'            => $body['user_name'],
                'email'           => $body['email'],
                'type'            => 'MANAGER',
            ]
        );
    }
}
