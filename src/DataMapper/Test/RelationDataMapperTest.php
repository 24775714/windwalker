<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2014 - 2015 LYRASOFT Taiwan, Inc. All rights reserved.
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Windwalker\DataMapper\Test;

use Windwalker\Compare\GteCompare;
use Windwalker\DataMapper\DataMapper;

/**
 * Test class of DataMapper
 *
 * @since 2.0
 */
class RelationDataMapperTest extends DatabaseTest
{
	/**
	 * Test instance.
	 *
	 * @var DataMapper
	 */
	protected $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->db = static::$dbo;

		$this->instance = DataMapper::newRelation('flower', 'ww_flower');

		$this->instance->leftJoin('category', 'ww_categories', 'flower.catid = category.id');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}

	/**
	 * Test find()
	 *
	 * @covers Windwalker\DataMapper\RelationDataMapper::find
	 *
	 * @return void
	 */
	public function testFind()
	{
		$dataset = $this->instance->find(
			array(
				'flower.state' => 1,
				new GteCompare('category.id', 2)
			),
			'flower.title DESC',
			1,
			3
		);

		$sql = <<<SQL
SELECT `flower`.`id`,
	`flower`.`catid`,
	`flower`.`title`,
	`flower`.`meaning`,
	`flower`.`ordering`,
	`flower`.`state`,
	`flower`.`params`,
	`category`.`id` AS `category_id`,
	`category`.`title` AS `category_title`,
	`category`.`ordering` AS `category_ordering`,
	`category`.`params` AS `category_params`
FROM `ww_flower` AS `flower`
	LEFT JOIN `ww_categories` AS `category` ON flower.catid = category.id
WHERE `flower`.`state` = 1
	AND `category`.`id` >= 2
ORDER BY flower.title DESC
LIMIT 1, 3
SQL;

		$this->assertEquals($dataset, $this->loadToDataset($sql));
	}

	/**
	 * Test find()
	 *
	 * @covers Windwalker\DataMapper\RelationDataMapper::find
	 *
	 * @return void
	 */
	public function testFindGroup()
	{
		$mapper = new DataMapper;
		$mapper->leftJoin('category', 'ww_categories')
			->leftJoin('flower', 'ww_flower', 'flower.catid = category.id')
			->group('category.id');

		$dataset = $mapper->find(array('flower.state' => 1), 'flower.title DESC');

		$sql = <<<SQL
SELECT `flower`.`id` AS `flower_id`,
	`flower`.`catid` AS `flower_catid`,
	`flower`.`title` AS `flower_title`,
	`flower`.`meaning` AS `flower_meaning`,
	`flower`.`ordering` AS `flower_ordering`,
	`flower`.`state` AS `flower_state`,
	`flower`.`params` AS `flower_params`,
	`category`.`id` AS `id`,
	`category`.`title` AS `title`,
	`category`.`ordering` AS `ordering`,
	`category`.`params` AS `params`
FROM `ww_categories` AS `category`
	LEFT JOIN `ww_flower` AS `flower` ON flower.catid = category.id
WHERE `flower`.`state` = 1
GROUP BY category.id
ORDER BY flower.title DESC
SQL;

		$this->assertEquals($dataset, $this->loadToDataset($sql));
	}

	/**
	 * testFindWhere
	 *
	 * @return  void
	 */
	public function testFindWhere()
	{
		$mapper = DataMapper::newRelation('category', 'ww_categories')
			->leftJoin('flower', 'ww_flower', 'flower.catid = category.id')
			->where('flower.catid < :catid')->bind('catid', 2)
			->where('%n > %a', 'flower.id', 10);

		$dataset = $mapper->find(array('flower.state' => 1), 'flower.title DESC');

		$sql = <<<SQL
SELECT `flower`.`id` AS `flower_id`,
	`flower`.`catid` AS `flower_catid`,
	`flower`.`title` AS `flower_title`,
	`flower`.`meaning` AS `flower_meaning`,
	`flower`.`ordering` AS `flower_ordering`,
	`flower`.`state` AS `flower_state`,
	`flower`.`params` AS `flower_params`,
	`category`.`id` AS `id`,
	`category`.`title` AS `title`,
	`category`.`ordering` AS `ordering`,
	`category`.`params` AS `params`
FROM `ww_categories` AS `category`
	LEFT JOIN `ww_flower` AS `flower` ON flower.catid = category.id
WHERE `flower`.`state` = 1 AND `flower`.`catid` < 2 AND `flower`.`id` > 10
ORDER BY flower.title DESC
SQL;

		$this->assertEquals($dataset, $this->loadToDataset($sql));
	}

	/**
	 * Test findAll()
	 *
	 * @covers Windwalker\DataMapper\RelationDataMapper::findAll
	 *
	 * @return void
	 */
	public function testFindAll()
	{
		$dataset = $this->instance->findAll(
			'flower.title DESC',
			1,
			3
		);

		$sql = <<<SQL
SELECT `flower`.`id`,
	`flower`.`catid`,
	`flower`.`title`,
	`flower`.`meaning`,
	`flower`.`ordering`,
	`flower`.`state`,
	`flower`.`params`,
	`category`.`id` AS `category_id`,
	`category`.`title` AS `category_title`,
	`category`.`ordering` AS `category_ordering`,
	`category`.`params` AS `category_params`
FROM `ww_flower` AS `flower`
	LEFT JOIN `ww_categories` AS `category` ON flower.catid = category.id
ORDER BY flower.title DESC
LIMIT 1, 3
SQL;

		$this->assertEquals($dataset, $this->loadToDataset($sql));
	}

	/**
	 * Test find one.
	 *
	 * @covers Windwalker\DataMapper\RelationDataMapper::findOne
	 *
	 * @return void
	 */
	public function testFindOne()
	{
		$data = $this->instance->findOne(
			array(
				'flower.state' => 1,
				new GteCompare('category.id', 2)
			),
			'flower.title DESC'
		);

		$sql = <<<SQL
SELECT `flower`.`id`,
	`flower`.`catid`,
	`flower`.`title`,
	`flower`.`meaning`,
	`flower`.`ordering`,
	`flower`.`state`,
	`flower`.`params`,
	`category`.`id` AS `category_id`,
	`category`.`title` AS `category_title`,
	`category`.`ordering` AS `category_ordering`,
	`category`.`params` AS `category_params`
FROM `ww_flower` AS `flower`
	LEFT JOIN `ww_categories` AS `category` ON flower.catid = category.id
WHERE `flower`.`state` = 1
	AND `category`.`id` >= 2
ORDER BY flower.title DESC
SQL;

		$this->assertEquals($data, $this->loadToData($sql));
	}

	/**
	 * Method to test addTable().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DataMapper\RelationDataMapper::addTable
	 */
	public function testAddTable()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * Method to test removeTable().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DataMapper\RelationDataMapper::removeTable
	 * @TODO   Implement testRemoveTable().
	 */
	public function testRemoveTable()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
