<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostExportHashRequest extends FormRequest
{
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
            'groupId' => 'required|integer',
            'postId' => 'required|integer',
            'exportHash' => 'required|string',
        ];
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->json('groupId');
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->json('postId');
    }

    /**
     * @return string
     */
    public function getExportHash(): string
    {
        return $this->json('exportHash');
    }
}
