<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Exceptions\MissingDependencyException;
use Intervention\Image\Laravel\Facades\Image;
use InvalidArgumentException;

class Article extends Model
{
    use HasFactory;

    public const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'webp'];

    public const IMAGE_MAX_KB = 10 * 1024;

    /** Longest side of a stored photo, in pixels. */
    public const IMAGE_MAX_SIDE = 1600;

    public const IMAGE_QUALITY = 82;

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
     * /storage/clanky/{year}/…), scaled down to IMAGE_MAX_SIDE, and replace
     * any previous photo.
     */
    public function attachImage(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        // The stored name keeps this extension, and the web server picks the content type from it.
        if (! in_array($ext, self::IMAGE_TYPES, true)) {
            throw new InvalidArgumentException("Nepovolená prípona fotografie: .{$ext}");
        }
        $base = Str::slug($this->title, '_') ?: 'foto';
        $dir = 'clanky/'.$this->published_on->year;
        $disk = Storage::disk('public');

        $name = "{$base}.{$ext}";
        for ($i = 2; $disk->exists("{$dir}/{$name}") && "{$dir}/{$name}" !== $this->image_path; $i++) {
            $name = "{$base}-{$i}.{$ext}";
        }

        $this->deleteImageFile();

        try {
            $image = Image::decodePath($file->getRealPath())->scaleDown(self::IMAGE_MAX_SIDE, self::IMAGE_MAX_SIDE);
            $disk->put("{$dir}/{$name}", (string) $image->encode(match ($ext) {
                'png' => new PngEncoder,
                'webp' => new WebpEncoder(quality: self::IMAGE_QUALITY),
                default => new JpegEncoder(quality: self::IMAGE_QUALITY, progressive: true),
            }));
            [$width, $height] = [$image->width(), $image->height()];
        } catch (MissingDependencyException $e) {
            // No GD/Imagick on this server: keep the original file as uploaded.
            Log::warning('Photo stored without resizing: '.$e->getMessage());
            $disk->putFileAs($dir, $file, $name);
            [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];
        }

        $this->forceFill([
            'image_path' => "{$dir}/{$name}",
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
