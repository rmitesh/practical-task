<?php

namespace App\Http\Requests;

use App\Models\Prize;
use Illuminate\Foundation\Http\FormRequest;
use Closure;

class PrizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $current_probability = Prize::getTotalProbability();
        $max_probability = $max_length = (int) Prize::MAX_PROBABILITY_LIMIT;

        // Check is the request is edit or not.
        // If request is edit then use existing probability for max validation.
        if ( $prize = request()->route('prize') ) {
            $max_length = $prize->probability;
        }

        return [
            'title' => 'required',
            'probability' => [
                'bail', 'required', 'numeric', 'min:0', "max:{$max_length}",
                function (string $attribute, mixed $value, Closure $fail) use ($max_probability, $current_probability) {
                    // validate only when the request is for create.
                    if (
                        is_null(request()->route('prize')) &&
                        ($max_probability < ( $current_probability + $value ))
                    ) {
                        $remaining_probability = $max_probability - $current_probability;
                        $fail("The {$attribute} field must not be greater than {$remaining_probability}.");
                    }
                }
            ],
        ];
    }
}
