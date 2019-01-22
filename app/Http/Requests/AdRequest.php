<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AdRequest extends FormRequest
{
    private const TIMEZONE = 'Europe/Moscow';
    private const DEFAULT_OFFSET = 0;
    private const DEFAULT_LIMIT = 100;

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
            'groupId' => 'nullable|array',
            'dates.from' => 'nullable|date',
            'dates.to' => 'nullable|date',
            'likes.from' => 'nullable|integer',
            'likes.to' => 'nullable|integer',
            'comments.from' => 'nullable|integer',
            'comments.to' => 'nullable|integer',
            'shares.from' => 'nullable|integer',
            'shares.to' => 'nullable|integer',
            'views.from' => 'nullable|integer',
            'views.to' => 'nullable|integer',
            'is_video' => 'nullable|boolean',
            'is_gif' => 'nullable|boolean',
            'is_shared' => 'nullable|boolean',
            'url' => 'nullable|string',
        ];
    }

    /**
     * @return array|null
     */
    public function getGroupId(): ?array
    {
        $groupId = $this->input('groupId', null);

        if (is_array($groupId)) {
            return collect($groupId)
                ->map(function ($id) { return (int)$id; })
                ->values()
                ->toArray();
        } else {
            return null;
        }
    }

    /**
     * @return Carbon|null
     */
    public function getDatesFrom(): ?Carbon
    {
        return $this->input('dates.from') !== null ? (new Carbon($this->input('dates.from')))->timezone(self::TIMEZONE) : null;
    }

    /**
     * @return Carbon|null
     */
    public function getDatesTo(): ?Carbon
    {
        return $this->input('dates.to') !== null ? (new Carbon($this->input('dates.to')))->timezone(self::TIMEZONE) : null;
    }

    /**
     * @return int|null
     */
    public function getLikesFrom(): ?int
    {
        return $this->input('likes.from', null);
    }

    /**
     * @return int|null
     */
    public function getLikesTo(): ?int
    {
        return $this->input('likes.to', null);
    }

    /**
     * @return int|null
     */
    public function getCommentsFrom(): ?int
    {
        return $this->input('comments.from', null);
    }

    /**
     * @return int|null
     */
    public function getCommentsTo(): ?int
    {
        return $this->input('comments.to', null);
    }

    /**
     * @return int|null
     */
    public function getSharesFrom(): ?int
    {
        return $this->input('shares.from', null);
    }

    /**
     * @return int|null
     */
    public function getSharesTo(): ?int
    {
        return $this->input('shares.to', null);
    }

    /**
     * @return int|null
     */
    public function getViewsFrom(): ?int
    {
        return $this->input('views.from', null);
    }

    /**
     * @return int|null
     */
    public function getViewsTo(): ?int
    {
        return $this->input('views.to', null);
    }

    /**
     * @return bool|null
     */
    public function getIsVideo(): ?bool
    {
        return $this->input('is_video', null);
    }

    /**
     * @return bool|null
     */
    public function getIsGif(): ?bool
    {
        return $this->input('is_gif', null);
    }

    /**
     * @return bool|null
     */
    public function getIsShared(): ?bool
    {
        return $this->input('is_shared', null);
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->input('url', null);
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
