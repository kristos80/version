<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Kristos80\Version\Version;
use PHPUnit\Framework\TestCase;

final class VersionTest extends TestCase {

	/**
	 * @var bool
	 */
	protected static bool $envLoaded = FALSE;

	public function testCurrentVersion(): void {
		$composerFilePath = __DIR__ . "/../composer.json";
		$composerContent = json_decode(file_get_contents($composerFilePath), TRUE);

		$this->assertEquals($composerContent["version"], Version::get());

		self::loadEnv();

		$this->assertEquals("1.0.1", Version::get("1", TRUE));

		$_ENV["VERSION_COMPOSER_FILE_PATH"] = "dummy";

		$this->assertEquals("10", Version::get("10", TRUE));
	}

	/**
	 * @return void
	 */
	protected function loadEnv(): void {
		if(self::$envLoaded) {
			return;
		}

		$dotEnv = Dotenv::createImmutable(__DIR__, ".env.test");
		$dotEnv->load();
	}
}
