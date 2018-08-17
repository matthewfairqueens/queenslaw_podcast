<?php

namespace Drupal\queenslaw_podcast\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a form for storing podcast settings.
 *
 * @ingroup queenslaw_podcast
 */
class QueensLawPodcastAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'queenslaw_podcast_admin';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'queenslaw_podcast.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $upload_validators = _queenslaw_podcast_upload_validators();
    $config = $this->config('queenslaw_podcast.settings');
    $default_image = [];
    if ($config->get('image')) $default_image[] = $config->get('image');
    $form = [
      '#attributes' => [
        'enctype' => 'multipart/form-data'
      ],
      'path' => [
        '#type' => 'textfield',
        '#title' => $this->t('Path'),
        '#description' => $this->t('The path to the podcast.'),
        '#size' => 40,
        '#maxlength' => 255,
        '#default_value' => $config->get('path'),
        '#required' => TRUE,
      ],
      'title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#description' => $this->t('The podcast title.'),
        '#size' => 40,
        '#maxlength' => 255,
        '#default_value' => $config->get('title'),
        '#required' => TRUE,
      ],
      'image' => [
        '#type' => 'managed_file',
        '#title' => $this->t('Image'),
        '#upload_validators' => $upload_validators,
        '#upload_location' => 'public://queenslaw-podcast',
        '#default_value' => $default_image,
        '#required' => FALSE,
      ],
      'subtitle' => [
        '#type' => 'textfield',
        '#title' => $this->t('Subtitle'),
        '#description' => $this->t('The podcast subtitle.'),
        '#size' => 40,
        '#maxlength' => 255,
        '#default_value' => $config->get('subtitle'),
        '#required' => FALSE,
      ],
      'description' => [
        '#type' => 'textarea',
        '#title' => $this->t('Description'),
        '#description' => $this->t('A description of the podcast.'),
        '#size' => 40,
        '#maxlength' => 255,
        '#default_value' => $config->get('description'),
        '#required' => FALSE,
      ],
      'source_url' => [
        '#type' => 'textfield',
        '#title' => $this->t('Source URL'),
        '#description' => $this->t('The source URL from which podcast data is loaded. Do not include a trailing slash.'),
        '#size' => 40,
        '#maxlength' => 255,
        '#default_value' => $config->get('source_url'),
        '#required' => TRUE,
      ],
      'username' => [
        '#type' => 'textfield',
        '#title' => $this->t('Username'),
        '#description' => $this->t('If HTTP authentication is required to access the source data, provide the username.'),
        '#size' => 40,
        '#maxlength' => 255,
        '#default_value' => $config->get('username'),
        '#required' => FALSE,
      ],
      'password' => [
        '#type' => 'textfield',
        '#title' => $this->t('Password'),
        '#description' => $this->t('If HTTP authentication is required to access the source data, provide the password.'),
        '#size' => 40,
        '#maxlength' => 255,
        '#default_value' => $config->get('password'),
        '#required' => FALSE,
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    foreach ($values as $key => $value) {
      if (is_string($value)) $this->config('queenslaw_podcast.settings')->set($key, $value)->save();
    }
    // For some reason $values['image'] is always empty, even though the file was added
    // and a fid was generated; this still doesn't work: for the initial setup I found the
    // fid manually and ran the following separately to make the file permanent and set
    // the relevant usage.
    if (isset($values['image']) && ($values['image'])) {
      if ($file = File::load($values['image'])) {
        $file->setPermanent();
        $file->save();
        $file_usage = \Drupal::service('file.usage');
        $file_usage->add($file, 'queenslaw_podcast', 'queenslaw_podcast', \Drupal::currentUser()->id());
      }
    }
    drupal_flush_all_caches();
    drupal_set_message($this->t('The configuration was updated.'));
  }

}
