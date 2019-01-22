<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
            'members_from' => 'nullable|integer',
            'members_to' => 'nullable|integer',
            'type_id' => 'nullable|string',
            'period' => 'nullable|string|in:day,week,month',
            'country' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|integer',
            'is_verified' => 'nullable|boolean',
            'is_closed' => 'nullable|boolean',
            'is_adult' => 'nullable|boolean',
            'posts_from' => 'nullable|integer',
            'posts_to' => 'nullable|integer',
            'likes_from' => 'nullable|integer',
            'likes_to' => 'nullable|integer',
            'avg_posts_from' => 'nullable|integer',
            'avg_posts_to' => 'nullable|integer',
            'avg_comments_per_post_from' => 'nullable|integer',
            'avg_comments_per_post_to' => 'nullable|integer',
            'avg_likes_per_post_from' => 'nullable|integer',
            'avg_likes_per_post_to' => 'nullable|integer',
            'avg_shares_per_post_from' => 'nullable|integer',
            'avg_shares_per_post_to' => 'nullable|integer',
            'avg_views_per_post_from' => 'nullable|integer',
            'avg_views_per_post_to' => 'nullable|integer',
            'sort' => 'nullable|string|in:asc,desc',
            'offset' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ];
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->input('title');
    }

    /**
     * @return int|null
     */
    public function getMembersFrom(): ?int
    {
        return $this->input('members_from');
    }

    /**
     * @return int|null
     */
    public function getMembersTo(): ?int
    {
        return $this->input('members_to');
    }

    /**
     * @return int[]|null
     */
    public function getTypeId(): ?array
    {
        $typeId = $this->input('type_id');

        if ($typeId !== null) {
            return array_map('intval', explode(',', $typeId));
        } else {
            return null;
        }
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->input('country');
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->input('state');
    }

    /**
     * @return int|null
     */
    public function getCity(): ?int
    {
        return $this->input('city');
    }

    /**
     * @return bool|null
     */
    public function getIsVerified(): ?bool
    {
        return $this->input('is_verified');
    }

    /**
     * @return bool|null
     */
    public function getIsClosed(): ?bool
    {
        return $this->input('is_closed');
    }

    /**
     * @return bool|null
     */
    public function getIsAdult(): ?bool
    {
        return $this->input('is_adult');
    }

    /**
     * @return int|null
     */
    public function getPostsFrom(): ?int
    {
        return $this->input('posts_from');
    }

    /**
     * @return int|null
     */
    public function getPostsTo(): ?int
    {
        return $this->input('posts_to');
    }

    /**
     * @return int|null
     */
    public function getLikesFrom(): ?int
    {
        return $this->input('likes_from');
    }

    /**
     * @return int|null
     */
    public function getLikesTo(): ?int
    {
        return $this->input('likes_to');
    }

    /**
     * @return int|null
     */
    public function getAvgPostsFrom(): ?int
    {
        return $this->input('avg_posts_from');
    }

    /**
     * @return int|null
     */
    public function getAvgPostsTo(): ?int
    {
        return $this->input('avg_posts_to');
    }

    /**
     * @return int|null
     */
    public function getAvgCommentsPerPostFrom(): ?int
    {
        return $this->input('avg_comments_per_post_from');
    }

    /**
     * @return int|null
     */
    public function getAvgCommentsPerPostTo(): ?int
    {
        return $this->input('avg_comments_per_post_to');
    }

    /**
     * @return int|null
     */
    public function getAvgLikesPerPostFrom(): ?int
    {
        return $this->input('avg_likes_per_post_from');
    }

    /**
     * @return int|null
     */
    public function getAvgLikesPerPostTo(): ?int
    {
        return $this->input('avg_likes_per_post_to');
    }

    /**
     * @return int|null
     */
    public function getAvgSharesPerPostFrom(): ?int
    {
        return $this->input('avg_shares_per_post_from');
    }

    /**
     * @return int|null
     */
    public function getAvgSharesPerPostTo(): ?int
    {
        return $this->input('avg_shares_per_post_to');
    }

    /**
     * @return int|null
     */
    public function getAvgViewsPerPostFrom(): ?int
    {
        return $this->input('avg_views_per_post_from');
    }

    /**
     * @return int|null
     */
    public function getAvgViewsPerPostTo(): ?int
    {
        return $this->input('avg_views_per_post_to');
    }

    /**
     * @return string
     */
    public function getSort(): string
    {
        return $this->input('sort', 'members');
    }

    /**
     * @return string
     */
    public function getSortDirection(): string
    {
        return $this->input('direction', 'desc');
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
