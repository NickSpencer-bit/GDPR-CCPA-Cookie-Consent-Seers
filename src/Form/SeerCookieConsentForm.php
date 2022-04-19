<?php

namespace Drupal\seers_cookie_consent_privacy_policy\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SeerCookieConsentForm.
 */
class SeerCookieConsentForm extends ConfigFormBase
{

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'seers_cookie_consent_privacy_policy.seercookieconsent',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'seer_cookie_consent_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('seers_cookie_consent_privacy_policy.seercookieconsent');
    $form['cookie_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cookie ID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('cookie_id'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitForm($form, $form_state);

    $this->config('seers_cookie_consent_privacy_policy.seercookieconsent')
      ->set('cookie_id', $form_state->getValue('cookie_id'))
      ->save();
  }

}
