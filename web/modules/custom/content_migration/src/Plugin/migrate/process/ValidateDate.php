<?php

namespace Drupal\content_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Check if a date is valid.
 *
 * @MigrateProcessPlugin(
 *   id = "validate_date"
 * )
 */
class ValidateDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): string {
    $date_pieces = explode('/', $value);
    $date_is_valid = checkdate($date_pieces[0], $date_pieces[1], $date_pieces[2]);
    return $date_is_valid ? $value : '';
  }
}
