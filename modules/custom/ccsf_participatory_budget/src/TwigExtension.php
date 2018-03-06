<?php

// borrowed from https://drupal.stackexchange.com/questions/217009/how-to-get-language-specific-path-in-twig

namespace Drupal\ccsf_participatory_budget;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;

class TwigExtension extends \Twig_Extension {

  protected $languageManager;

  protected $urlGenerator;

  public function __construct(LanguageManagerInterface $language_manager, UrlGeneratorInterface $url_generator) {
    $this->languageManager = $language_manager;
    $this->urlGenerator = $url_generator;
  }

  public function getName() {
    return 'mymodule';
  }

  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('path_lang', [$this, 'getPathLang']),
    ];
  }

  public function getPathLang($name, $parameters = [], $options = [], $langcode = '') {
    if (!empty($langcode)) {
      if ($language = $this->languageManager->getLanguage($langcode)) {
        $options['language'] = $language;
      }
    }
    return $this->urlGenerator->generateFromRoute($name, $parameters, $options);
  }
}
