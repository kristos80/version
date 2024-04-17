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
	 * @var string
	 */
	private string $rootPath;

	/**
	 * @var string|FALSE
	 */
	private string|false $composerFilePath;

	/**
	 * @var string
	 */
	private string $version;

	/**
	 * @var bool
	 */
	private bool $boot = FALSE;

	/**
	 * @param string $default
	 * @param bool $ignoreCache
	 *
	 * @return SemanticVersion
	 * @throws InvalidSemanticVersionException
	 */
	public function getAsSemanticVersion(string $default = self::DEFAULT_VERSION, bool $ignoreCache = FALSE): SemanticVersion {
		$version = $this->get($default, $ignoreCache);
		$versionParts = explode(".", $version);

		count($versionParts) > 3 && (throw new InvalidSemanticVersionException("Too many semantic parts in '$version'"));

		$major = (int) $versionParts[0];
		(($versionParts[0] !== "0" && $major === 0)) &&
		(throw new InvalidSemanticVersionException("Invalid major part '$versionParts[0]'"));

		$minor = (int) ($versionParts[1] ?? 0);
		(($versionParts[1] !== "0" && $minor === 0)) &&
		(throw new InvalidSemanticVersionException("Invalid minor part '$versionParts[1]'"));

		$patch = (int) ($versionParts[2] ?? 0);
		(($versionParts[2] !== "0" && $patch === 0)) &&
		(throw new InvalidSemanticVersionException("Invalid patch part '$versionParts[2]'"));

		return new SemanticVersion(major: $major, minor: $minor, patch: $patch);
	}

	/**
	 * @param string $default
	 * @param bool $ignoreCache
	 *
	 * @return string
	 */
	public function get(string $default = self::DEFAULT_VERSION, bool $ignoreCache = FALSE): string {
		$this->boot($ignoreCache);

		if(!$ignoreCache && ($this->version ?? FALSE)) {
			return $this->version;
		}

		$composerContent = [];
		$this->composerFilePath && ($composerContent = json_decode(file_get_contents($this->composerFilePath), TRUE));

		return $this->version = $composerContent["version"] ?? $default;
	}

	/**
	 * @param bool $ignoreCache
	 *
	 * @return void
	 */
	private function boot(bool $ignoreCache): void {
		if(!$ignoreCache && $this->boot) {
			return;
		}

		$this->boot = TRUE;

		$rootPath = $this->getRootPath();
		if(($_ENV["VERSION_COMPOSER_FILE_PATH"] ?? FALSE)) {
			$composerFilePath = $_ENV["VERSION_COMPOSER_FILE_PATH"];
			$this->composerFilePath = realpath("$rootPath/$composerFilePath");
			if(!($_ENV["VERSION_COMPOSER_FILE_PATH_RELATIVE"] ?? FALSE)) {
				$this->composerFilePath = realpath($composerFilePath);
			}

			return;
		}

		$this->composerFilePath = realpath("$rootPath/composer.json");
	}

	/**
	 * @return string
	 */
	private function getRootPath(): string {
		if($this->rootPath ?? FALSE) {
			return $this->rootPath;
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

		return $this->rootPath = realpath("$vendorPath/..");
	}
}