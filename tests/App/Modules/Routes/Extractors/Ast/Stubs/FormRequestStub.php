<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Ast\Stubs;

use Illuminate\Http\Request;

class FormRequestStub extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
        ];
    }
}
