<?php

namespace Drupal\content_migration\Plugin\migrate\process;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Check if term exists and create new if doesn't.
 *
 * @MigrateProcessPlugin(
 *   id = "existing_term"
 * )
 */
class ExistingTerm extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  protected EntityStorageInterface $taxonomyTermStorage;

  /**
   * Creates a EntityManager instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $taxonomy_term_storage
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    EntityStorageInterface $taxonomy_term_storage
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->taxonomyTermStorage = $taxonomy_term_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('taxonomy_term'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($term_name, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $vocabulary = $this->configuration['vocabulary'];
    if (empty($term_name) && $this->isPipelineStopped()) {
      return TRUE;
    }

    if ($tid = $this->getTidByName($term_name, $vocabulary)) {
      $term = Term::load($tid);
    }
    else {
      $term = Term::create([
        'name' => $term_name,
        'vid'  => $vocabulary,
      ])->save();
      if ($tid = $this->getTidByName($term_name, $vocabulary)) {
        $term = Term::load($tid);
      }
    }
    return [
      'target_id' => is_object($term) ? $term->id() : 0,
    ];
  }

  /**
   * Load term by name.
   */
  protected function getTidByName($name = NULL, $vocabulary = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vocabulary)) {
      $properties['vid'] = $vocabulary;
    }

    $terms = $this->taxonomyTermStorage->loadByProperties($properties);
    $term = reset($terms);
    return !empty($term) ? $term->id() : 0;
  }

}
