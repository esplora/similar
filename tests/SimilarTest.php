<?php

declare(strict_types=1);

namespace Tabuna\Similar\Tests;

use PHPUnit\Framework\TestCase;
use Tabuna\Similar\Similar;

class SimilarTest extends TestCase
{
    public function testEmptySimilar(): void
    {
        $group = Similar::build([]);

        self::assertEmpty($group);
    }

    public function testSameLinesSimilar(): void
    {
        $group = Similar::build([
            "'Make or break' approaching for EU-UK trade talks",
            "Make or break approaching for EU-UK trade talks",
        ], 95);

        $this->assertCount(2, $group->first());
    }

    public function testSuperfluousWord(): void
    {
        $group = Similar::build([
            'Elon Musk gets mixed COVID-19 test results as SpaceX launches astronauts to the ISS',
            'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday',

            // Superfluous word
            'Can Trump win with ‘fantasy’ electors bid? State GOP says no'
        ]);

        self::assertCount(1, $group);
        self::assertCount(2, $group->first());
    }

    public function testRussianSimilar(): void
    {
        $group = Similar::build([
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

}
