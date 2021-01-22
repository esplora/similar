<?php

declare(strict_types=1);

namespace Tabuna\Similar\Tests;

use PHPUnit\Framework\TestCase;
use Tabuna\Similar\Similar;

class SimilarTest extends TestCase
{

    /**
     * @var Similar
     */
    protected $similar;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->similar = new Similar(function (string $a, string $b) {
            similar_text($a, $b, $copy);

            return 51 < $copy;
        });
    }

    public function testEmptySimilar(): void
    {
        $group = $this->similar->findOut([]);

        self::assertEmpty($group);
    }

    public function testSameLinesSimilar(): void
    {
        $group = $this->similar
            ->comparison(function (string $a, string $b) {
                similar_text($a, $b, $copy);

                return 95 < $copy;
            })
            ->findOut([
                "'Make or break' approaching for EU-UK trade talks",
                "Make or break approaching for EU-UK trade talks",
            ]);

        $this->assertCount(2, $group->first());
    }

    public function testSuperfluousWord(): void
    {
        $group = $this->similar->findOut([
            'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
            'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',

            // Superfluous word
            'Can Trump win with ‘fantasy’ electors bid? State GOP says no',
        ]);

        self::assertCount(1, $group);
        self::assertCount(2, $group->first());
    }

    public function testSaveIndex(): void
    {
        $group = $this->similar->findOut([
            'foo' => 'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
            'bar' => 'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',

            // Superfluous word
            'baz' => 'Can Trump win with ‘fantasy’ electors bid? State GOP says no',
        ])->first();

        self::assertArrayHasKey('foo', $group);
        self::assertArrayHasKey('bar', $group);
    }

    public function testGroupSimilar(): void
    {
        $group = $this->similar->findOut([
            'kos' => "Trump acknowledges Biden's win in latest tweet",
            'foo' => 'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
            'baz' => 'Trump says Biden won but again refuses to concede',
            'bar' => 'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',
        ])->toArray();

        self::assertArrayHasKey('foo', $group);
        self::assertArrayHasKey('kos', $group);

        self::assertCount(2, $group['foo']);
        self::assertCount(2, $group['kos']);
    }

    public function testRussianSimilar(): void
    {
        $group = $this->similar->findOut([
            // Group 1
            'Макаревич призвал принять идиотизм большинства населения как данность',
            'Макаревич назвал «идиотами» 80% населения Земли',
            'Макаревич призвал смириться с идиотизмом 80% населения',
            'Макаревич назвал идиотами большинство населения',
            '«Принять как данность»: Макаревич назвал 80% населения Земли идиотами',

            // Group 2
            'Российское посольство обвинило Украину во вмешательстве в дела США',
            'Посольство России в Вашингтоне обвинило Украину во вмешательстве в дела США',
            'Посольство РФ обратило внимание Белого дома на вмешательство Украины в дела Штатов',
            'Украину обвинили во вмешательстве во внутренние дела США',
        ]);

        self::assertCount(5, $group->first());
        self::assertCount(4, $group->last());
    }

    public function testKeyClosureSimilar(): void
    {
        $group = $this->similar
            ->comparison(function (string $a, string $b, string $keyA, string $keyB) {
                return ($keyA === 'baz' || $keyA === 'bar') && ($keyB === 'baz' || $keyB === 'bar');
            })
            ->findOut([
                'kos' => "Trump acknowledges Biden's win in latest tweet",
                'foo' => 'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
                'baz' => 'Trump says Biden won but again refuses to concede',
                'bar' => 'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',
            ])->toArray();


        self::assertEquals([
            'baz' => [
                "baz" => "Trump says Biden won but again refuses to concede",
                "bar" => "Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday",
            ],
        ], $group);
    }
}
