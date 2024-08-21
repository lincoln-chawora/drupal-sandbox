<?php

namespace Drupal\lds_content_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\DomProcessBase;

/**
 * Select strings from a DOMDocument object.
 *
 * Configuration:
 * - selector: An XPath selector that resolves to a string.
 *
 * Usage:
 *
 * @code
 * process:
 *   bar:
 *     -
 *       plugin: dom
 *       method: import
 *       source: text_field
 *     -
 *       plugin: custom_dom_select
 *       selector: //img
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "custom_dom_select"
 * )
 */
class CustomDomSelect extends DomProcessBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    $this->init($value, $destination_property);
    $values = [];

    $elements = $this->xpath->query($this->configuration['selector']);

    foreach ($elements as $element) {
      foreach ($element->attributes as $attr) {
        $values[$attr->name] = $attr->value;
      }
    }

    return $values;
  }

}
