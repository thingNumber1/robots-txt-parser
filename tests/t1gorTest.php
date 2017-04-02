<?php

/**
 * Class t1gorTest
 * This Test Class created to check bugs/issues which are found in similar library https://github.com/t1gor/Robots.txt-Parser-Class/issues
 */
class t1gorTest extends \PHPUnit\Framework\TestCase
{
	public static function setUpBeforeClass()
	{
		require_once(realpath(__DIR__.'/../RobotsTxtParser.php'));
	}

	/**
	 * Test that parsing of large robots.txt file require a little time. Less than 0.5s.
	 *
	 * https://github.com/t1gor/Robots.txt-Parser-Class/issues/62
	 */
	public function testParsingPerformanceIssue()
	{
		$robotsContent = file_get_contents(__DIR__ . '/robots.txt/goldmansachs.com');
		$startTime = microtime(true) * 1000; //ms
		$parserRobotsTxt = new RobotsTxtParser($robotsContent);
		$rules = $parserRobotsTxt->getRules();
		$endTime = microtime(true) * 1000; //ms
		$this->assertLessThanOrEqual(500, $endTime - $startTime, 'parsing takes a lot of time');
		$this->assertTrue((bool) $rules, 'parsed empty rules');
	}

	/**
	 * Test that all rules successfully parsed
	 *
	 * https://github.com/t1gor/Robots.txt-Parser-Class/issues/79
	 */
	public function testRulesLoadedIssue79()
	{
		$robotsTxtContent = <<<ROBOTS
User-agent:Googlebot
Crawl-delay: 1.5
Disallow:/
User-agent:Cocon.Se Crawler
Disallow:/
User-agent:Yandexbot
Disallow:/
User-agent:*
Disallow:
Crawl-delay: 1
ROBOTS;

		$parserRobotsTxt = new RobotsTxtParser($robotsTxtContent);
		$rules = $parserRobotsTxt->getRules();

		$this->assertEquals(
			[
				'googlebot' => [
					'crawl-delay' => 1.5,
					'disallow' => [
						0 => '/',
					],
				],
				'cocon.se crawler' => [
					'disallow' => [
						0 => '/',
					],
				],

				'yandexbot' => [
					'disallow' => [
						0 => '/',
					],
				],
				'*' => [
					'crawl-delay' => 1.0,
				],
			],
			$rules
		);
	}
}
