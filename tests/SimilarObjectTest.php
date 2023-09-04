<?php

declare(strict_types=1);

namespace Esplora\Similar\Tests;

use Esplora\Similar\Similar;
use PHPUnit\Framework\TestCase;

class SimilarObjectTest extends TestCase
{
    /**
     * @var Similar
     */
    protected $similar;

    public function setUp(): void
    {

        parent::setUp();

        $this->similar = new Similar(function (FixtureStingObject $a, FixtureStingObject $b) {
            similar_text((string) $a, (string) $b, $copy);

            return 51 < $copy;
        });
    }

    public function testObjectSimilar(): void
    {
        $group = $this->similar
            ->comparison(function ($a, $b, string $keyA, string $keyB) {
                return ($keyA === 'baz' || $keyA === 'bar') && ($keyB === 'baz' || $keyB === 'bar');
            })
            ->findOut([
                'baz' => new FixtureStingObject('Trump says Biden won but again refuses to concede'),
                'bar' => new FixtureStingObject('Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday'),
            ])->toArray();

        self::assertCount(1, $group);
        self::assertCount(2, $group['baz']);

        self::assertTrue(is_a($group['baz']['baz'], FixtureStingObject::class));
        self::assertTrue(is_a($group['baz']['bar'], FixtureStingObject::class));

        self::assertEquals((string) $group['baz']['baz'], 'Trump says Biden won but again refuses to concede');
        self::assertEquals((string) $group['baz']['bar'], 'Elon Musk may have Covid-19, should quarantine during SpaceX astronaut launch Sunday');
    }

    public function testSuperfluousObjectWord(): void
    {
        $group = $this->similar->findOut([
            // Group 1
            new FixtureStingObject('Макаревич призвал принять идиотизм большинства населения как данность'),
            new FixtureStingObject('Макаревич назвал «идиотами» 80% населения Земли'),
            new FixtureStingObject('Макаревич призвал смириться с идиотизмом 80% населения'),
            new FixtureStingObject('Макаревич назвал идиотами большинство населения'),
            new FixtureStingObject('«Принять как данность»: Макаревич назвал 80% населения Земли идиотами'),

            // Group 2
            new FixtureStingObject('Российское посольство обвинило Украину во вмешательстве в дела США'),
            new FixtureStingObject('Посольство России в Вашингтоне обвинило Украину во вмешательстве в дела США'),
            new FixtureStingObject('Посольство РФ обратило внимание Белого дома на вмешательство Украины в дела Штатов'),
            new FixtureStingObject('Украину обвинили во вмешательстве во внутренние дела США'),
        ]);

        self::assertCount(5, $group->first());
        self::assertCount(4, $group->last());
    }
}
