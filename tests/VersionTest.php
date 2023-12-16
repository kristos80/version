<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

abstract class VersionTest extends TestCase {

	/**
	 * @var bool
	 */
	protected static bool $envLoaded = FALSE;

	public function testCurrentVersion(): void {
		$composerFilePath = __DIR__ . "/../composer.json";
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
