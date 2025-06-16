<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Validações incompletas
     *  - Foi utilizado apenas o modificador 'sometimes' nas regras de validação — que valida o campo somente se ele estiver presente no payload — porém, 
     *    as demais validações não foram implementadas, tornando essa configuração ineficaz.
     *  - Os Form Requests não especificam limites de tamanho (max, min) para campos varchar/string, o que pode levar a inconsistências com as restrições do 
     *    banco de dados se esses dados, posteriormente, não forem validados no seu tamanho.
     * 
     * Sugestões:
     *  - Completar as regras de validação.
     *  - Definir limites de tamanho (max:255, por exemplo) nos campos varchar.
     *      - Falta de definição de tamanho para campos string causará erros de estouro de campos nas tabelas do banco de dados.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'   => 'sometimes',
            'email'  => 'sometimes',
            'status' => 'sometimes',
        ];
    }
}
