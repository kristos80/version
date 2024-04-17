<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Kristos80\Version\Version;
use PHPUnit\Framework\TestCase;
use Kristos80\Version\InvalidSemanticVersionException;

final class VersionTest extends TestCase {

	/**
	 * @var bool
	 */
	protected static bool $envLoaded = FALSE;

	/**
	 * @var Version
	 */
	private Version $version;

	/**
	 * @return void
	 * @throws InvalidSemanticVersionException
	 */
	public function testCurrentVersion(): void {
		$composerFilePath = __DIR__ . "/../composer.json";
		$composerContent = json_decode(file_get_contents($composerFilePath), TRUE);

		$this->assertEquals($composerContent["version"], $this->version->get());

		self::loadEnv();

		$this->assertEquals("1.0.1", $this->version->get("151", TRUE));

		$_ENV["VERSION_COMPOSER_FILE_PATH"] = "dummy";

		$this->assertEquals("10", $this->version->get("10", TRUE));

		self::loadEnv();
		$semanticVersion = $this->version->getAsSemanticVersion("2", TRUE);
		$this->assertEquals(1, $semanticVersion->getMajor());
		$this->assertEquals(0, $semanticVersion->getMinor());
		$this->assertEquals(1, $semanticVersion->getPatch());
	}

	/**
	 * @return void
	 */
	protected function loadEnv(): void {
		$dotEnv = Dotenv::createMutable(__DIR__, ".env.test");
		$dotEnv->load();
	}

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->version = new Version();
	}
}
