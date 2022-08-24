<?php

// The namespace is Drupal\[module_key]\[Directory\Path(s)]
namespace Drupal\seers_cookie_consent_privacy_policy\Service;

/**
 * The CustomFunctions service. Does a bunch of stuff.
 */
class CustomFunctions {

    public $apisecrekkey = '----theapisecretkey-------';

  /**
   * Does something.
   *
   * @return json-object or NULL
   *   Some value.
   */
  public function doActiveInactive($isactive = 0, $modulename='') {

    $postData = array(
        'domain' => \Drupal::request()->getHost(),
        'isactive' => $isactive,
        'secret' => $this->apisecrekkey,
        'platform' => 'drupal',
        'pluginname' => $modulename
    );
    $request_headers = array(
        'Content-Type' => 'application/json',
        'Referer' => \Drupal::request()->getHost(),
    );
    //$url = "https://seersco.backend/api/plugin-domain";
    $url = "https://cmp.seersco.com/api/plugin-domain";
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => $request_headers,
        CURLOPT_POSTFIELDS => $postData
    ));

    $response = curl_exec($curl);
    $error_number = curl_errno($curl);
    $error_message = curl_error($curl);
    curl_close($curl);
    
    $response =json_decode($response, TRUE);

    return $response;
    
  }

}