<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UseCases\Card\Register;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Responses\DefaultResponse;
use App\Integrations\Banking\Card\Find;

class CardController extends Controller
{
    /**
     * Melhorias:
     * 
     * - Utilizar Resources (Laravel API Resources) para retorno de dados, evitando retorno direto de arrays nos métodos
     */

    /**
     * Exibe dados de um cartão
     *
     * POST api/users/{id}/card
     *
     * @return JsonResponse
     */
    public function show(string $userId): JsonResponse
    {
        $response = (new Find($userId))->handle();

        return $this->response(
            new DefaultResponse($response['data'])
        );
    }

    /**
     * Melhorias:
     * 
     * - Não possui validação para os campos enviados no payload.
     *  - Utilizar Form Requests para validação de entrada.
     * - Aplicar classes DTO (params)
     * - Utilizar Resources (Laravel API Resources) para retorno de dados, evitando retorno direto de arrays nos métodos
     */

    /**
     * Ativa um cartão
     *
     * POST api/users/{id}/card
     *
     * @return JsonResponse
     */
    public function register(string $userId, Request $request): JsonResponse
    {
        $response = (new Register($userId, $request->pin, $request->card_id))->handle();

        return $this->response(
            new DefaultResponse($response['data'])
        );
    }
}
