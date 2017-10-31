<?php

namespace Drupal\ccsf_district\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\ccsf_district\Entity\DistrictInterface;

/**
 * Class DistrictController.
 *
 *  Returns responses for District routes.
 */
class DistrictController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a District  revision.
   *
   * @param int $district_revision
   *   The District  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($district_revision) {
    $district = $this->entityManager()->getStorage('district')->loadRevision($district_revision);
    $view_builder = $this->entityManager()->getViewBuilder('district');

    return $view_builder->view($district);
  }

  /**
   * Page title callback for a District  revision.
   *
   * @param int $district_revision
   *   The District  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($district_revision) {
    $district = $this->entityManager()->getStorage('district')->loadRevision($district_revision);
    return $this->t('Revision of %title from %date', ['%title' => $district->label(), '%date' => format_date($district->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a District .
   *
   * @param \Drupal\ccsf_district\Entity\DistrictInterface $district
   *   A District  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DistrictInterface $district) {
    $account = $this->currentUser();
    $langcode = $district->language()->getId();
    $langname = $district->language()->getName();
    $languages = $district->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $district_storage = $this->entityManager()->getStorage('district');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $district->label()]) : $this->t('Revisions for %title', ['%title' => $district->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all district revisions") || $account->hasPermission('administer district entities')));
    $delete_permission = (($account->hasPermission("delete all district revisions") || $account->hasPermission('administer district entities')));

    $rows = [];

    $vids = $district_storage->revisionIds($district);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\ccsf_district\DistrictInterface $revision */
      $revision = $district_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $district->getRevisionId()) {
          $link = $this->l($date, new Url('entity.district.revision', ['district' => $district->id(), 'district_revision' => $vid]));
        }
        else {
          $link = $district->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.district.translation_revert', ['district' => $district->id(), 'district_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.district.revision_revert', ['district' => $district->id(), 'district_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.district.revision_delete', ['district' => $district->id(), 'district_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['district_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
