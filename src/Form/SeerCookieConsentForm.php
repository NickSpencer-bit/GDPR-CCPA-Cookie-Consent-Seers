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
    $user = \Drupal::currentUser();
    $form['consent_url'] = [
      '#type' => 'textfield',
      '#title' =>  $this->t('URL'),
      '#maxlength' => 200,
      '#size' => 64,
      '#default_value' => (($config->get('consent_url')) ? $config->get('consent_url') : $GLOBALS['base_url'] ),
      '#attributes' => array('readonly' => 'readonly')
    ];
    $form['consent_email'] = [
      '#type' => 'textfield',
      '#title' =>  $this->t('Email'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => (($config->get('consent_email')) ? $config->get('consent_email') : $user->getEmail() ),
      '#required' => true
    ];
    $form['seers_term_condition'] = [
      '#type' => 'checkbox',
      '#title' =>  $this->t('I agree Seers <a href="https://seersco.com/terms-and-conditions.html" target="_blank">Terms & Condition</a> and <a href="https://seersco.com/privacy-policy.html" target="_blank">Privacy Policy</a>,'),
      '#default_value' => (($config->get('seers_term_condition')) ? "1" : "" ),
      '#required' => true
    ];
    $form['seers_term_condition_url'] = [
      '#type' => 'checkbox',
      '#title' =>  $this->t('I agree Seers to use my email and url to create an account and power the cookie banner.'),
      '#default_value' => (($config->get('seers_term_condition_url')) ? "1" : "" ),
      '#required' => true
    ];
    
    $form['cookie_id'] = [
      '#type' => 'textfield',
      '#title' =>  $this->t('Domain Group ID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('cookie_id'),
      '#attributes' => array('readonly' => 'readonly')
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitForm($form, $form_state);

    //if the checkboxes are checked
    if ($form_state->getValue('seers_term_condition') && $form_state->getValue('seers_term_condition_url')) {

      $useremail = $form_state->getValue('consent_email');
      $baseurl = $form_state->getValue('consent_url');
      $termsandcondition = $form_state->getValue('seers_term_condition');
      $termsandcondurl = $form_state->getValue('seers_term_condition_url');
      $consetid = '';

      $postData = array(
        'domain'=>$baseurl,
        'email'=>$useremail,
        'secret'=>'$2y$10$9ygTfodVBVM0XVCdyzEUK.0FIuLnJT0D42sIE6dIu9r/KY3XaXXyS',
        'platform'=>'drupal',
        'lang' => \Drupal::languageManager()->getCurrentLanguage()->getId()
      );
      
      $request_headers = array(
        'Content-Type: application/json',
        'Referer: '.$baseurl
      );
      $url = "https://seersco.com/api/save-domain-credentials";
      $postdata = json_encode($postData);
  
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      $result = curl_exec($ch);
      curl_close($ch);
      
      $response  = json_decode($result,true);

      if ( !empty($response['key']) ) {

        $consetid = $response['key'];

    $this->config('seers_cookie_consent_privacy_policy.seercookieconsent')
    ->set('consent_email', $useremail)
    ->set('consent_url', $baseurl)
    ->set('cookie_id', $consetid)
    ->set('seers_term_condition', $form_state->getValue('seers_term_condition'))
    ->set('seers_term_condition_url', $form_state->getValue('seers_term_condition_url'))
      ->save();
      
    }
  }
  }

}
