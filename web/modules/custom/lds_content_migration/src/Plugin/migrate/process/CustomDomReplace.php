<?php

namespace Drupal\lds_content_migration\Plugin\migrate\process;

use Drupal\Component\Utility\Html;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Replace img src elements, remove script tags and replace iframe elements with drupal-url embed element.
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

    $iframes = $xpath->query('//iframe');
    $video_ids = [];
    $walking_dead = [];
    foreach ($iframes as $iframe) {
      $iframe_src = $iframe->getAttribute('src');
      // Regular expression to match the value between 'embed/' and '?si'.
      preg_match('/embed\/(.*?)\?si/', $iframe_src, $matches);

      // Add extracted value into video ids array.
      $video_ids[] = $matches[1];
      $walking_dead[] = $iframe;
    }

    foreach ($walking_dead as $key => $node) {
      // Get the parent node.
      $parentNode = $node->parentNode;

      if ($video_ids) {
        // Replace iframe with <drupal-url> element to host the youtube url using the video id.
        $drupal_url_element = $node->ownerDocument->createElement('drupal-url', 'Youtube video');
        $drupal_url_element->setAttribute('data-embed-url', 'https://www.youtube.com/watch?v=' . $video_ids[$key]);
        $drupal_url_element->setAttribute('data-url-provider', 'YouTube');
        $parentNode->replaceChild($drupal_url_element, $node);
      } else {
        // Remove the existing node (this deletes the iframe element). This assumes no valid video id was found.
        $parentNode->removeChild($node);
      }
    }

    return Html::serialize($dom);
  }
}
