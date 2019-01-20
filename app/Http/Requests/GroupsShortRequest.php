<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupsShortRequest extends FormRequest
{
    const DEFAULT_OFFSET = 0;
    const DEFAULT_LIMIT = 100;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string',
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ];
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->input('title', null);
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return (int)$this->input('offset', self::DEFAULT_OFFSET);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int)$this->input('limit', self::DEFAULT_LIMIT);
    }
}
