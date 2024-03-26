<?php

namespace App\Models;

use App\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class File extends Model
{
    use HasFactory, NodeTrait, SoftDeletes, HasCreatorAndUpdater;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(File::class, 'parent_id');
    }

    public function owner(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['created_by'] === Auth::id() ? 'Me' : $this->user->name;
            }
        );
    }

    public function isOwnedBy($userId): bool
    {
        return $this->created_by === $userId;
    }

    public function isRoot(): bool
    {
        return !$this->parent_id;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->parent) {
                return;
            }
            $model->path = (!$model->parent->isRoot() ? $model->parent->path . '/' : '')
                . Str::slug($model->name);
        });
    }
}
