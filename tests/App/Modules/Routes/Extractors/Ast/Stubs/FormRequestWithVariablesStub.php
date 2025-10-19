<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Ast\Stubs;

use Illuminate\Http\Request;

class FormRequestWithVariablesStub extends Request
{
    public function rules(): array
    {
        $rule = 'required|string';

        return [
            'name' => $rule,
            'email' => "{$rule}|email",
        ];
    }
}
