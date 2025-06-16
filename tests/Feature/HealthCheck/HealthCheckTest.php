<?php

namespace Tests\Feature\HealthCheck;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Melhorias:
     *  - Isolamento de testes com RefreshDatabase
     *  - Nos testes existentes, não foi identificado o uso do trait RefreshDatabase, que é essencial para garantir que cada teste comece com o banco de dados em um estado limpo e controlado.
     */
    
    /**
     * Teste de acesso ao healthcheck
     *
     * @return void
     */
    public function testHealthCheck()
    {
        $response = $this->get('/api/healthcheck');

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => null,
            ],
            true
        );
    }
}
