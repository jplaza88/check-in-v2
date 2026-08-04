<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\HistoryPeriod;
use App\Enums\HistoryTab;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class HistoryFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tab' => ['sometimes', Rule::enum(HistoryTab::class)],
            'period' => ['sometimes', Rule::enum(HistoryPeriod::class)],
            'page' => ['sometimes', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
