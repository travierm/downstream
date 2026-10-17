<?php

namespace Tests\Unit;

use App\Services\Filter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TitleFilterTest extends TestCase
{
    #[DataProvider('titles')]
    public function test_cleans_promotional_tags_and_preserves_song_information(string $title, string $expected): void
    {
        $this->assertSame($expected, Filter::title($title, []));
        $this->assertSame($expected, Filter::title($expected, []));
    }

    public static function titles(): array
    {
        return [
            'full screen' => ['Artist - Song [Official Full Screen]', 'Artist - Song'],
            'case insensitive' => ['Artist - Song [oFfIcIaL fUlLsCrEeN]', 'Artist - Song'],
            'parentheses' => ['Artist - Song (Official Music Video)', 'Artist - Song'],
            'lyric video' => ['Artist - Song [Official Lyric Video]', 'Artist - Song'],
            'audio' => ['Artist - Song (Official Audio)', 'Artist - Song'],
            'multiple labels' => ['Artist - Song [Official Video] (HD) [Lyrics]', 'Artist - Song'],
            'combined labels' => ['Artist - Song [OFFICIAL MUSIC VIDEO / 4K]', 'Artist - Song'],
            'visualizer' => ['Artist - Song (Official Visualiser)', 'Artist - Song'],
            'whitespace' => ["  Artist  - Song\t[Official Full Screen]  ", 'Artist - Song'],
            'HTML entities' => ['Artist &amp; Guest - Song &lbrack;Official Full Screen&rbrack;', 'Artist & Guest - Song'],
            'live version' => ['Artist - Song (Live at Wembley)', 'Artist - Song (Live at Wembley)'],
            'remix' => ['Artist - Song (Official Remix)', 'Artist - Song (Official Remix)'],
            'featured artist' => ['Artist - Song [feat. Guest]', 'Artist - Song [feat. Guest]'],
            'remaster' => ['Artist - Song (2011 Remaster)', 'Artist - Song (2011 Remaster)'],
            'unbracketed song title' => ['Artist - Official', 'Artist - Official'],
            'unknown brackets' => ['Artist - Song [Part II]', 'Artist - Song [Part II]'],
        ];
    }

    public function test_applies_custom_filters_alongside_builtin_cleanup(): void
    {
        $this->assertSame('Artist - Song', Filter::title('Artist - Song [Official Full Screen] (Custom Upload)', ['(Custom Upload)']));
    }
}
