<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Stubs;

use Illuminate\Http\Request;

class FormRequestWithDifferentRulesStub extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'required',
            'content' => 'nullable|string',
        ];
    }
}
