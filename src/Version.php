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
	private static string $rootPath;

	/**
	 * @var string|FALSE
	 */
	private static string|false $composerFilePath;

	/**
	 * @var string
	 */
	private static string $version;

	/**
	 * @param string $default
	 * @param bool $ignoreCache
	 * @return string
	 */
	public static function get(string $default = self::DEFAULT_VERSION, bool $ignoreCache = FALSE): string {
		self::boot($ignoreCache);

		if(!$ignoreCache && (self::$version ?? FALSE)) {
			return self::$version;
		}

		$composerContent = [];
		self::$composerFilePath && ($composerContent = json_decode(file_get_contents(self::$composerFilePath), TRUE));

		return self::$version = $composerContent["version"] ?? $default;
	}

	/**
	 * @param bool $ignoreCache
	 * @return void
	 */
	private static function boot(bool $ignoreCache): void {
		if(!$ignoreCache && self::$boot) {
			return;
		}

		self::$boot = TRUE;

		$rootPath = self::getRootPath();
		if(($_ENV["VERSION_COMPOSER_FILE_PATH"] ?? FALSE)) {
			$composerFilePath = $_ENV["VERSION_COMPOSER_FILE_PATH"];
			if($_ENV["VERSION_COMPOSER_FILE_PATH_RELATIVE"] ?? FALSE) {
				self::$composerFilePath = realpath("$rootPath/$composerFilePath");
			} else {
				self::$composerFilePath = realpath($composerFilePath);
			}

			return;
		}

		self::$composerFilePath = realpath("$rootPath/composer.json");
	}

	/**
	 * @return string
	 */
	private static function getRootPath(): string {
		if(self::$rootPath ?? FALSE) {
			return self::$rootPath;
		}

		$vendorPath = NULL;
		$recursion = 0;
		$parentDir = __DIR__;
		while(!$vendorPath && $recursion < self::MAX_PARENT_PATH_RECURSION) {
			$parentDir = dirname($parentDir);
			if(file_exists("$parentDir/vendor/autoload.php")) {
				$vendorPath = realpath("$parentDir/vendor");
			}

			$recursion++;
		}

		return self::$rootPath = realpath("$vendorPath/..");
	}
}