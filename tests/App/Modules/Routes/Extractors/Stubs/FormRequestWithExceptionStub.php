<?php

namespace Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Stubs;

use Illuminate\Http\Request;

class FormRequestWithExceptionStub extends Request
{
    public function rules(): array
    {
        throw new \RuntimeException('Cannot access request context');
    }
}
