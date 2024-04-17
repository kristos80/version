<?php
declare(strict_types=1);

namespace Kristos80\Version;

final readonly class SemanticVersion {

	/**
	 * @param int $major
	 * @param int $minor
	 * @param int $patch
	 */
	public function __construct(private int $major, private int $minor, private int $patch) {}

	/**
	 * @return int
	 */
	public function getMajor(): int {
		return $this->major;
	}

	/**
	 * @return int
	 */
	public function getMinor(): int {
		return $this->minor;
	}

	/**
	 * @return int
	 */
	public function getPatch(): int {
		return $this->patch;
	}
}