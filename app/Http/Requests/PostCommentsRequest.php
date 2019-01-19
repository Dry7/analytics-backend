<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostCommentsRequest extends FormRequest
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
            'comments' => 'required|integer',
        ];
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->json()->getInt('groupId');
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->json()->getInt('postId');
    }

    /**
     * @return int
     */
    public function getComments(): int
    {
        return $this->json()->getInt('comments');
    }
}
