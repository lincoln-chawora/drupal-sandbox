<?php

namespace Drupal\lds_content_migration\Plugin\migrate\process;

use Drupal\Core\File\FileUrlGenerator;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Convert file uri to relative url.
 *
 * @MigrateProcessPlugin(
 *   id = "file_uri_to_url"
 * )
 */
class FileUriToUrl extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  protected FileUrlGenerator $fileUrlGenerator;

  /**
   * Creates a FileUrlGenerator instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\File\FileUrlGenerator $file_url_generator
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    FileUrlGenerator $file_url_generator
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('file_url_generator')
    );
  }

  /**
   * Transforms the file URI to an absolute URL or relative path.
   *
   * @param mixed $value
   *   The input value (file URI).
   * @param MigrateExecutableInterface $migrate_executable
   *   The migrate executable.
   * @param Row $row
   *   The current row.
   * @param string $destination_property
   *   The destination property.
   *
   * @return mixed
   *   The transformed value (relative path).
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value['value']) && $this->isPipelineStopped()) {
      return TRUE;
    }
    // Return relative url of file.
    return $this->fileUrlGenerator->generateString($value['value']);
  }
}
