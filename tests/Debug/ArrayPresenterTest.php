<?php

namespace Thinktomorrow\Vine\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Vine\Debug\ArrayPresenter;
use Thinktomorrow\Vine\DefaultNode;
use Thinktomorrow\Vine\NodeCollection;
use Thinktomorrow\Vine\Tests\Fixtures\FixtureSource;

class ArrayPresenterTest extends TestCase
{
    public function test_it_can_represent_a_collection_as_array()
    {
        $result = (new ArrayPresenter())->render(new NodeCollection([new DefaultNode(['id' => 1])]));

        $this->assertIsArray($result);
    }

    public function test_it_can_represent_tree_as_array()
    {
        $tree = NodeCollection::fromIterable($this->getTranslation());

        $result = (new ArrayPresenter())->render($tree);

        $this->assertEquals([
            [
                "id" => "1",
                "parent_id" => null,
                "entry" => [1, 0, "root-1"],
                "children" => [
                    [
                        "id" => "2",
                        "parent_id" => "1",
                        "entry" => [2, 1, "child-1"],
                        "children" => [
                            [
                                "id" => "3",
                                "parent_id" => "2",
                                "entry" => [3, 2, "child-1-1"],
                                "children" => [
                                    [
                                        "id" => "7",
                                        "parent_id" => "3",
                                        "entry" => [7, 3, "child-2-1"],
                                        "children" => [],
                                    ],
                                    [
                                        "id" => "8",
                                        "parent_id" => "3",
                                        "entry" => [8, 3, "child-2-2"],
                                        "children" => [],
                                    ],
                                    [
                                        "id" => "9",
                                        "parent_id" => "3",
                                        "entry" => [9, 3, "child-2-3"],
                                        "children" => [],
                                    ],
                                ],
                            ],
                            [
                                "id" => "4",
                                "parent_id" => "2",
                                "entry" => [4, 2, "child-1-2"],
                                "children" => [],
                            ],
                            [
                                "id" => "5",
                                "parent_id" => "2",
                                "entry" => [5, 2, "child-1-3"],
                                "children" => [
                                    [
                                        "id" => "10",
                                        "parent_id" => "5",
                                        "entry" => [10, 5, "child-2-4"],
                                        "children" => [],
                                    ],
                                    [
                                        "id" => "11",
                                        "parent_id" => "5",
                                        "entry" => [11, 5, "child-2-5"],
                                        "children" => [
                                            [
                                                "id" => "12",
                                                "parent_id" => "11",
                                                "entry" => [12, 11, "child-3-1"],
                                                "children" => [
                                                    [
                                                        "id" => "14",
                                                        "parent_id" => "12",
                                                        "entry" => [14, 12, "child-4-1"],
                                                        "children" => [],
                                                    ],
                                                ],
                                            ],
                                            [
                                                "id" => "13",
                                                "parent_id" => "11",
                                                "entry" => [13, 11, "child-3-2"],
                                                "children" => [],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            [
                                "id" => "6",
                                "parent_id" => "2",
                                "entry" => [6, 2, "child-1-4"],
                                "children" => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                "id" => "15",
                "parent_id" => null,
                "entry" => [15, 0, "root-2"],
                "children" => [
                    [
                        "id" => "16",
                        "parent_id" => "15",
                        "entry" => [16, 15, "child-1-5"],
                        "children" => [],
                    ],
                ],
            ],
        ], $result);
    }

    private function getTranslation(): iterable
    {
        return (new FixtureSource('default'))->getAsCollection();
    }
}
