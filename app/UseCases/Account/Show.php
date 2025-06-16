<?php

namespace App\UseCases\Account;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Integrations\Banking\Account\Find;

/**
 * Melhorias:
 *  - Padronizar a entrada com DTOs
 */

class Show extends BaseUseCase
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    /**
     * Conta
     *
     * @var array
     */
    protected array $account;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Encontra a conta
     *
     * @return void
     */
    protected function find(): void
    {
        $this->account = (new Find($this->userId))->handle();
    }

    /**
     * Retorna a conta
     */
    public function handle(): array
    {
        try {
            $this->find();
        } catch (Throwable $th) {
            $this->defaultErrorHandling(
                $th,
                [
                    'userId' => $this->userId,
                ]
            );
        }

        return $this->account;
    }
}
