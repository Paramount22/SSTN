<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    public const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'webp'];

    public const IMAGE_MAX_KB = 10 * 1024;

    protected $fillable = [
        'title', 'slug', 'published_on', 'body',
        'image_path', 'image_alt', 'image_width', 'image_height', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            if (blank($article->slug)) {
                $article->slug = static::uniqueSlug($article->title, $article->id);
            }
        });

        static::deleted(function (Article $article) {
            $article->deleteImageFile();
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'clanok';
        $slug = $base;
        for ($i = 2; static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Store the photo under clanky/{year}/ on the public disk (served at
     * /storage/clanky/{year}/…) and replace any previous photo.
     */
    public function attachImage(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        $base = Str::slug($this->title, '_') ?: 'foto';
        $dir = 'clanky/'.$this->published_on->year;
        $disk = Storage::disk('public');

        $name = "{$base}.{$ext}";
        for ($i = 2; $disk->exists("{$dir}/{$name}") && "{$dir}/{$name}" !== $this->image_path; $i++) {
            $name = "{$base}-{$i}.{$ext}";
        }

        // getimagesize() is part of PHP core – works without the GD extension.
        [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

        $this->deleteImageFile();
        $this->forceFill([
            'image_path' => $disk->putFileAs($dir, $file, $name),
            'image_width' => $width,
            'image_height' => $height,
        ])->save();
    }

    public function removeImage(): void
    {
        $this->deleteImageFile();
        $this->forceFill(['image_path' => null, 'image_width' => null, 'image_height' => null])->save();
    }

    protected function deleteImageFile(): void
    {
        if ($this->image_path) {
            Storage::disk('public')->delete($this->image_path);
        }
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(trim(preg_replace('/\s+/', ' ', (string) $this->body)), 180);
    }

    /** @return list<string> */
    public function getParagraphsAttribute(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $this->body))));
    }
}
