<?php

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\NotIn;
use Illuminate\Validation\Rules\RequiredIf;
use Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Ast\Stubs\FormRequestStub;
use Sunchayn\Nimbus\Tests\App\Modules\Routes\Extractors\Stubs\StatusEnumStub;

class TestController
{
    public function simple_call(Request $request): void
    {
        $validated = $request->validate([
            'foobar' => 'required|string',
            'foobaz' => ['required', 'integer'],
        ]);
    }

    public function simple_call_without_assignment(FormRequestStub $request)
    {
        $request->validate([
            'foobar' => 'required|string',
            'foobaz' => ['required', 'integer'],
        ]);
    }

    public function getting_rules_from_sub_method(Request $request): void
    {
        $validated = $request->validate($this->craftRules());
    }

    public function call_from_a_variable(Request $request)
    {
        $rules = [
            'foobar' => 'required|string',
            'foobaz' => ['required', 'integer'],
        ];

        $validated = $request->validate($rules);
    }

    public function call_from_nested_variables(Request $request)
    {
        $rule = ['required', 'string', 'email'];

        $rules = [
            'foobar' => 'required|string',
            'foobaz' => $rule,
        ];

        $validated = $request->validate($rules);
    }

    public function call_with_rules_instances(Request $request)
    {
        $rules = [
            'foobar' => [
                'required',
                Rule::in(1, 2, 3, 4),
                new NotIn(3, 4),
                new RequiredIf(fn () => '::value::'),
                Rule::enum(StatusEnumStub::class),
            ],
            'foobaz' => ['required', 'string', new In(1, 2)],
        ];

        $validated = $request->validate($rules);
    }

    public function call_validateWithBag(Request $request)
    {
        $rules = [
            'foobar' => [
                'required',
                'in:1, 2, 3, 4',
            ],
            'foobaz' => ['required', 'string', 'email'],
        ];

        $validated = $request->validateWithBag('foobaz', $rules);
    }

    public function no_validate_call(Request $request)
    {
        return response()->json('Hi');
    }

    public function call_validate_on_different_class(Container $container)
    {
        $container->validate([
            'foobar' => [
                'required',
                'in:1, 2, 3, 4',
            ],
            'foobaz' => ['required', 'string', 'email'],
        ]);
    }

    public function nested_methods_calls(Request $request)
    {
        $this->validateFormData($request);

        return response()->json('Hi');
    }

    private function validateFormData(Request $request)
    {
        return $request->validate([
            'foobaz' => ['required', 'string', 'email'],
        ]);
    }

    private function craftRules(): array
    {
        $rule = ['required', 'string', 'email'];

        $interpolation = 'required|string';

        $interpolation2 = '|max:200';

        $fieldname = 'foobaz';

        return [
            'foobar' => 'required_with:'.$fieldname,
            'foobarr' => 'present_with'.':'.'foobar',
            'fooobazz' => "{$interpolation}|email{$interpolation2}",
            'baz' => "{$interpolation}|present".'|'.'email',
            'foobaz' => $rule,
        ];
    }
}
