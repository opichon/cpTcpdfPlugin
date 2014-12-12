<?php
class SymfonyTCPDF extends TCPDFX {

  protected $culture;
  protected $i18n;
  protected $date_format;
  protected $number_format;
  protected $translation_catalogue = 'messages';

  protected $number_formatter;
  protected $currency_formatter;

  public function __construct($culture = null) {
    parent::__construct();
    $this->culture = $culture ?
                       $culture :
                       sfContext::getInstance()->getUser()->getCulture();
    /*
     * Remove google analytics script code from the end of the PDF's HEX code.
     * When the settings.yml::compressed setting is switched to 'on'
     * the script code appears at the end of the PDF and corrupts it.
     */
    sfConfig::set('app_google_analytics_enabled', false);
  }

  public function __($string, $args = array(), $catalogue = null) {
    return $this->getI18n()->__($string, $args, $catalogue ? $catalogue : $this->getTranslationCatalogue());
  }

  public function getCulture() {
    return $this->culture;
  }

  public function setCulture($culture) { $this->culture = $culture; }

  public function getTranslationCatalogue() { return $this->translation_catalogue; }

  public function setTranslationCatalogue($catalogue) { $this->translation_catalogue = $catalogue; }

  public function getWatermark() {
    return $this->__($this->watermark);
  }

  public function processName($name) {
    return $this->__($name, $this->getNameParams());
  }

  public function getNameParams() { return null; }

  public function getNumberFormatter($pattern = '# ##0.00') {
    if (!$this->number_formatter) {
      $this->number_formatter = new NumberFormatter(
        $this->getCulture(),
        NumberFormatter::PATTERN_DECIMAL
      );
    }
    $this->number_formatter->setPattern($pattern);
    return $this->number_formatter;
  }

  public function getCurrencyFormatter() {
    if (!$this->currency_formatter) {
      $this->currency_formatter = new NumberFormatter(
        $this->getCulture(),
        NumberFormatter::CURRENCY
      );
    }
    return $this->currency_formatter;
  }

  protected function getDateFormat() {
    if (!$this->date_format) {
      $this->date_format = new sfDateFormat($this->getCulture());
    }

    return $this->date_format;
  }

  protected function getNumberFormat() {
    if (!$this->number_format) {
      $this->number_format = new sfNumberFormat($this->getCulture());
    }

    return $this->number_format;
  }

  protected function getI18n() {
    if (!$this->i18n) {
      $this->i18n = sfContext::getInstance()->getI18n();
      $this->i18n->setCulture($this->getCulture());
    }

    return $this->i18n;
  }
  /*
  protected function getPagination() {
    return $this->__($this->getPaginateFormat(),
                     array('%page%' => $this->getAliasNumPage(),
                           '%pages%' => $this->getNumPages()),
                     $this->getTranslationCatalogue());
  }
  */

  protected function getCountry($country_iso) {
   $c = sfCultureInfo::getInstance($this->getCulture());
   $countries = $c->getCountries();

   return isset($countries[$country_iso]) ? $countries[$country_iso] : '';
  }
}