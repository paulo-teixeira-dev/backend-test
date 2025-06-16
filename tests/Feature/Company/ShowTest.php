<?php

namespace Tests\Feature\Company;

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
     * Teste de busca de dados de empresa
     *
     * @return void
     */
    public function testShow()
    {
        $user    = User::factory()->user()->create();
        $company = $user->company;
        $token   = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = $this->get('/api/company', $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'id'   => $company->id,
                    'name' => $company->name,
                ],
            ],
            true
        );
    }
}
