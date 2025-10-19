<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Ast\Stubs;

use Illuminate\Http\Request;

class FormRequestWithInputConditionalStub extends Request
{
    public function rules(): array
    {
        $rule = 'required|string';

        $shouldAllowSomething = $this->boolean('something');

        return [
            'name' => $shouldAllowSomething ? 'required|string' : 'present|string',
            'email' => "{$rule}|email",
        ];
    }
}
