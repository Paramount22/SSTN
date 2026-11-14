<?php

namespace Database\Seeders;

use App\Models\Link;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/** Sidebar/footer links and partners from sstn.sk, plus the membership form file. */
class LinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            // partners (header strip)
            ['partner', 'ZSVTS', 'https://www.zsvts.sk/', 'Zväz slovenských vedeckotechnických spoločností'],
            ['partner', 'ÚNMS SR', 'https://www.normoff.gov.sk/', 'Úrad pre normalizáciu, metrológiu a skúšobníctvo SR'],
            ['partner', 'ČSTN', 'https://www.cstn.cz/', 'Česká spoločnosť pre technickú normalizáciu'],
            ['partner', 'MŠVVaM SR', 'https://www.minedu.sk/', 'Ministerstvo školstva, výskumu, vývoja a mládeže SR'],
            // footer
            ['footer', 'SSTN je členom ZSVTS', 'https://www.zsvts.sk/organizacie/slovenska-spolocnost-pre-technicku-normalizaciu-sstn/', null],
            ['footer', 'ZSVTS', 'https://www.zsvts.sk/', null],
            ['footer', 'VTS News – aktuálne číslo', 'https://www.zsvts.sk/vts-news/', null],
            ['footer', 'Archív VTS News', 'https://www.zsvts.sk/vts-news/archiv/', null],
            ['footer', 'COVID-19 bezodplatne sprístupnené technické normy', 'https://www.unms.sk/?covid-19-aktualne-informacie&sprava=unms-sr-v-boji-proti-ochoreniu-covid-19-bezodplatne-spristupnil-dalsie-technicke-normy', null],
            ['footer', 'SDN – Svetový deň normalizácie', 'https://www.normoff.gov.sk/aktuality/1007/svetovy-den-normalizacie/', null],
            // videos
            ['video', 'Víťazné video 2015', 'https://www.youtube.com/watch?v=Dk33hLQKIJU&list=PLVtjsE0I-ufmttdrINAkhGs-jfY5rteNg', null],
            ['video', 'Víťazné video 2016', 'https://www.youtube.com/watch?v=SJvNkZeQrDs', null],
            ['video', 'Víťazné video 2017', 'https://www.youtube.com/watch?v=G8uSH6bGxb4', null],
            // documents
            ['document', 'Prihláška za člena', '/storage/clenstvo/prihlaskasstn2016.doc', 'DOC · 32 kB'],
        ];

        $sort = [];
        foreach ($links as [$group, $label, $url, $description]) {
            $sort[$group] = ($sort[$group] ?? 0) + 1;
            Link::updateOrCreate(['group' => $group, 'label' => $label], [
                'url' => $url, 'description' => $description, 'sort' => $sort[$group],
            ]);
        }

        // Membership form – copied from the static prototype so /storage/clenstvo/... keeps working.
        $source = base_path('../site/storage/clenstvo/prihlaskasstn2016.doc');
        if (File::exists($source) && ! Storage::disk('public')->exists('clenstvo/prihlaskasstn2016.doc')) {
            Storage::disk('public')->put('clenstvo/prihlaskasstn2016.doc', File::get($source));
        }
    }
}
