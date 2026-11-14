<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventAttachment extends Model
{
    public const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'rtf', 'odt', 'xls', 'xlsx', 'ppt', 'pptx', 'zip'];

    protected $fillable = ['label', 'path', 'original_name', 'mime', 'size', 'sort'];

    protected static function booted(): void
    {
        static::deleted(function (EventAttachment $attachment) {
            Storage::disk('public')->delete($attachment->path);
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Store an upload under akcie/{year}/ on the public disk, keeping a readable
     * file name. Files become reachable at /storage/akcie/{year}/{name}, the same
     * URL scheme the old sstn.sk used.
     */
    public static function fromUpload(Event $event, UploadedFile $file, ?string $label = null): static
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '_') ?: 'subor';
        $dir = 'akcie/'.$event->starts_on->year;
        $disk = Storage::disk('public');

        $name = "{$base}.{$ext}";
        for ($i = 2; $disk->exists("{$dir}/{$name}"); $i++) {
            $name = "{$base}-{$i}.{$ext}";
        }

        $path = $disk->putFileAs($dir, $file, $name);

        return $event->attachments()->create([
            'label' => filled($label) ? $label : Str::headline($base),
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'sort' => ($event->attachments()->max('sort') ?? 0) + 1,
        ]);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getExtensionAttribute(): string
    {
        return strtoupper(pathinfo($this->path, PATHINFO_EXTENSION));
    }

    public function getSizeLabelAttribute(): string
    {
        return $this->size >= 1048576
            ? number_format($this->size / 1048576, 1, ',', ' ').' MB'
            : number_format($this->size / 1024, 0, ',', ' ').' kB';
    }
}
