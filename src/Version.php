<?php
declare(strict_types=1);

namespace Kristos80\Version;

final class Version {

	/**
	 *
	 */
	private const DEFAULT_VERSION = "1.0.0";

	/**
	 *
	 */
	private const MAX_PARENT_PATH_RECURSION = 50;

	/**
	 * @var bool
	 */
	private static bool $boot = FALSE;

	/**
	 * @var string
	 */
	private static string $composerFilePath;

	/**
	 * @var string
	 */
	private static string $version;

	/**
	 * @param string $default
	 * @return string
	 */
	public static function get(string $default = self::DEFAULT_VERSION): string {
		self::boot();

		if(self::$version ?? FALSE) {
			return self::$version;
		}

		$composerContent = [];
		self::$composerFilePath && ($composerContent = json_decode(file_get_contents(self::$composerFilePath), TRUE));

		return self::$version = $composerContent["version"] ?? $default;
	}

	/**
	 * @return void
	 */
	private static function boot(): void {
		if(self::$boot) {
			return;
		}

		self::$boot = TRUE;

		if(($_ENV["VERSION_COMPOSER_FILE_PATH"] ?? FALSE) && file_exists($_ENV["VERSION_COMPOSER_FILE_PATH"])) {
			self::$composerFilePath = $_ENV["VERSION_COMPOSER_FILE_PATH"];

			return;
		}

		$vendorPath = NULL;
		$recursion = 0;
		$parentDir = __DIR__;
		while(!$vendorPath && $recursion < ($_ENV["VERSION_MAX_PARENT_PATH_RECURSION"] ?? self::MAX_PARENT_PATH_RECURSION)) {
			$parentDir = dirname($parentDir);
			if(file_exists("$parentDir/vendor/autoload.php")) {
				$vendorPath = realpath("$parentDir/vendor");
			}

			$recursion++;
		}

		$composerFilePath = "$vendorPath/../composer.json";

		self::$composerFilePath = $composerFilePath;
	}
}