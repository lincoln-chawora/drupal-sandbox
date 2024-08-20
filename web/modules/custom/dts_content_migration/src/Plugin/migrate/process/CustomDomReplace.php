<?php

namespace Drupal\dts_content_migration\Plugin\migrate\process;

use Drupal\Component\Utility\Html;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Replace img src elements and remove script tags.
 *
 * @MigrateProcessPlugin(
 *   id = "custom_dom_replace"
 * )
 */
class CustomDomReplace extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Load content from current row.
    $content = $row->getSource()['content'];

    // Convert content into dom object in order to search and replace image src value.
    $dom = Html::load($content);
    $xpath = new \DOMXPath($dom);

    // Get image file path for current row.
    $file_path = $row->getDestination()['pseudo_image_file_path'];

    // Load all image tags and set their src tag to the image file path.
    $img_elements = $xpath->query('//img');
    foreach ($img_elements as $image) {
      $image->setAttribute('src', $file_path[0]);
    }

    // Load all script tags and remove them.
    $scripts = $xpath->query('//script');
    foreach ($scripts as $script) {
      $script->parentNode->removeChild($script);
    }

    return Html::serialize($dom);
  }
}
