<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Events 2025–2026 as published on sstn.sk (texts verbatim). Attachments are
 * copied from the static prototype in ../site/storage so the public URLs
 * (/storage/akcie/…) stay identical to the old site.
 */
class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Stretnutie normalizátorov Slovenska 2026',
                'type' => 'sstn', 'starts_on' => '2026-11-20',
                'location' => 'Dom ZSVTS Koceľova 15, Bratislava',
                'organizers' => 'SSTN, ZSVTS, ÚNMS SR',
                'intro' => 'Slovenská spoločnosť pre technickú normalizáciu, Člen Zväzu slovenských vedeckotechnických spoločností – ZSVTS, Úrad pre normalizáciu, metrológiu a skúšobníctvo SR Vás pozýva na seminár',
                'files' => [['Pozvánka a program', 'akcie/2026/sstn_seminar_november_2026.pdf']],
            ],
            [
                'title' => 'Kyberbezpečnosť, normalizácia, vzdelávanie a prax',
                'type' => 'cstn', 'starts_on' => '2026-12-02',
                'location' => 'Biskupský důr (sídlo ÚNMZ/ČAS), konferenčná miestnosť List',
                'organizers' => 'SSTN, ČSTN',
                'intro' => 'Vážení členovia ZSVTS a SSTN, spoločne s Českou spoločnosťou pre technickú normalizáciu si Vás dovoľujeme pozvať na seminár:',
                'files' => [['Pozvánka a program', 'akcie/2026/seminar-praha-2026.pdf']],
            ],
            [
                'title' => 'Konferencia KVTS 2026',
                'type' => 'zsvts', 'starts_on' => '2026-10-22',
                'location' => 'Hotel Park Inn by Radisson Danube, Bratislava',
                'organizers' => 'ZSVTS, MŠVVaM SR',
                'intro' => 'Vážení členovia ZSVTS a SSTN, spoločne s Ministerstvom školstva, výskumu, vývoja a mládeže SR si Vás dovoľujeme pozvať na seminár:',
                'body' => 'Tohtoročná téma je: Spolupráca SR vo veľkých európskych výskumných infraštruktúrach.',
                'files' => [['Pozvánka a program', 'akcie/2026/pozvanka_na_kvts_2026.pdf']],
            ],
            [
                'title' => 'Stretnutie normalizátorov Slovenska 2025',
                'type' => 'sstn', 'starts_on' => '2025-12-11',
                'location' => 'Dom ZSVTS Koceľova 15, Bratislava',
                'organizers' => 'SSTN, ZSVTS',
                'intro' => 'Slovenská spoločnosť pre technickú normalizáciu, Člen Zväzu slovenských vedeckotechnických spoločností – ZSVTS Vás pozýva na seminár',
                'files' => [['Pozvánka', 'akcie/2025/bratislava/stretnutie_sstn_2025_bratislava.pdf']],
            ],
            [
                'title' => 'Normalizácia, vzdelávanie a prax',
                'type' => 'cstn', 'starts_on' => '2025-12-03',
                'location' => 'Biskupský důr (sídlo ÚNMZ/ČAS), konferenčná miestnosť List',
                'organizers' => 'SSTN, ČSTN',
                'intro' => 'Vážení členovia ZSVTS a SSTN, spoločne s Českou spoločnosťou pre technickú normalizáciu si Vás dovoľujeme pozvať na seminár:',
                'files' => [['Pozvánka a program', 'akcie/2025/praha2025/seminar-praha-2025-verze10.pdf']],
            ],
            [
                'title' => 'Standardizace, vzdělávání a praxe',
                'type' => 'cstn', 'starts_on' => '2025-04-08', 'ends_on' => '2025-04-09', 'date_text' => '8. a 9. apríla 2025',
                'location' => 'ŽĎAS, a.s., Strojírenská 6, Žďár nad Sázavou',
                'organizers' => 'SSTN, ČSTN',
                'intro' => 'Vážení členovia ZSVTS a SSTN, spoločne s Českou spoločnosťou pre technickú normalizáciu si Vás dovoľujeme pozvať na seminár:',
                'body' => "Konferencia sa uskutoční vo firme ŽĎAS, a.s, Strojírenská 6, Žďár nad Sázavou.\n\nZraz o 10:45 na hlavnej bráne. Pre parkovanie využite parkovisko spoločnosti ŽĎAS, a.s.",
                'files' => [['Pozvánka', 'akcie/2025/zdar2025/pozvanka-33-vh-2025.pdf'], ['Program', 'akcie/2025/zdar2025/seminar-zdas-2025.pdf']],
            ],
            [
                'title' => 'Fórum inžinierov a technikov Slovenska 2025 (FITS ´25)',
                'type' => 'zsvts', 'starts_on' => '2025-03-20', 'ends_on' => '2025-03-21',
                'location' => 'Congress Hotel Centrum, Južná trieda 2A, Košice',
                'organizers' => 'ZSVTS',
                'intro' => 'Vážení členovia ZSVTS a SSTN, dovoľujeme si Vás pozvať na Fórum inžinierov a technikov Slovenska.',
                'body' => 'Podujatie sa koná pri príležitosti osláv 35. výročia vzniku ZSVTS.',
                'files' => [['Pozvánka', 'akcie/2025/fits2025/pozvanka_na_fits_2025_final.pdf']],
            ],
        ];

        $disk = Storage::disk('public');

        foreach ($events as $data) {
            $files = $data['files'];
            unset($data['files']);

            // Slug is set explicitly: DatabaseSeeder runs without model events.
            $existing = Event::where('title', $data['title'])->whereDate('starts_on', $data['starts_on'])->first();
            $data['slug'] = $existing?->slug ?? Event::uniqueSlug($data['title']);

            $event = Event::updateOrCreate(['title' => $data['title'], 'starts_on' => $data['starts_on']], $data);

            foreach ($files as $sort => [$label, $path]) {
                $source = base_path('../site/storage/'.$path);
                if (! $disk->exists($path) && File::exists($source)) {
                    $disk->put($path, File::get($source));
                }
                if (! $disk->exists($path)) {
                    $this->command?->warn("Missing file for {$event->title}: {$path}");
                    continue;
                }

                $event->attachments()->updateOrCreate(['path' => $path], [
                    'label' => $label,
                    'original_name' => basename($path),
                    'mime' => $disk->mimeType($path) ?: 'application/pdf',
                    'size' => $disk->size($path),
                    'sort' => $sort + 1,
                ]);
            }
        }
    }
}
