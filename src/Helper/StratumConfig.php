<?php
declare(strict_types=1);

namespace SetBased\Stratum\Frontend\Helper;

use SetBased\Helper\Cast;
use SetBased\Stratum\Backend\Config;

/**
 * The concrete configuration class.
 */
class StratumConfig implements Config
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The actual configuration object.
   *
   * @var \Noodlehaus\Config
   */
  private \Noodlehaus\Config $config;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $path The path to the configuration file.
   */
  public function __construct(string $path)
  {
    $this->config = new \Noodlehaus\Config($path);
    $this->config->set('stratum.config_path', $path);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function manBool(string $key, ?bool $default = null): bool
  {
    return Cast::toManBool($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function manFiniteFloat(string $key, ?float $default = null): float
  {
    return Cast::toManFiniteFloat($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function manFloat(string $key, ?float $default = null): float
  {
    return Cast::toManFloat($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function manInt(string $key, ?int $default = null): int
  {
    return Cast::toManInt($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function manString(string $key, ?string $default = null): string
  {
    return Cast::toManString($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function optBool(string $key, ?bool $default = null): ?bool
  {
    return Cast::toOptBool($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function optFiniteFloat(string $key, ?float $default = null): ?float
  {
    return Cast::toOptFiniteFloat($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function optFloat(string $key, ?float $default = null): ?float
  {
    return Cast::toOptFloat($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function optInt(string $key, ?int $default = null): ?int
  {
    return Cast::toOptInt($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritDoc
   */
  public function optString(string $key, ?string $default = null): ?string
  {
    return Cast::toOptString($this->config->get($key, $default));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
