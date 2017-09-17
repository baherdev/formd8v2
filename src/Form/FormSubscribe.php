<?php

namespace Drupal\formd8v2\form;

use \Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\file\Entity\File;

class FormSubscribe extends FormBase{

 public function getFormId(){
  return 'subscribe_form';
}

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state){

    $form['first_name']=array(
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#size' => 60,
      '#maxlength' => 128,
      );

    $form['last_name']=array(
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#size' => 60,
      '#maxlength' => 128,
      );

    $form['gender'] = [
    '#type' => 'select',
    '#title' => $this->t('Gender'),
    '#options' => [
    'Male' => $this->t('Male'),
    'Female' => $this->t('Female'),
    ],
    '#required'=> 'TRUE',
    ];

    $form['birth_date'] = array(
      '#type' => 'date',
      '#title' => $this->t('Birth Date'),
      '#default_value' => array('year' => 2020, 'month' => 2, 'day' => 15,)
      );

    $form['email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Email'),
      );

    $form['picture'] = array(
      '#type' => 'managed_file',
      '#title' => t('Upload picture'),
      '#upload_location' => 'public://images/',
      );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      );
    return $form;
  }

  /**
   * Form validation handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state){

    if (is_numeric($form_state->getValue('first_name'))){
      $form_state->setErrorByName('first_name',$this->t('Error, The first name must be a string'));
    }

  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state){

    $picture = $form_state->getValue('picture');

    $file = File::load($picture[0]);

    $profile_value = array(
      'first_name' => $form_state->getValue('first_name'),
      'last_name' => $form_state->getValue('last_name'),
      'gender' => $form_state->getValue('gender'),
      'birth_date' => strtotime($form_state->getValue('birth_date')),
      'email' => $form_state->getValue('email'),
      'fid' => $picture[0],
      );

    $file->setPermanent();
    $file->save();

    $query = \Drupal::database();
    $query -> insert('form_1')
    -> fields($profile_value)
    -> execute();
    if (!is_null($query)){

      drupal_set_message("Data saved");
      drupal_set_message($this->t('Your First Name is @first_name <br>Your Last Name is: @last_name <br>Your Birth Date is: @birth_date <br>Your Gender is @gender: <br>Your email is: @email',
        array('@first_name' => $form_state->getValue('first_name'),
          '@last_name' => $form_state->getValue('last_name'),
          '@gender' => $form_state->getValue('gender'),
          '@birth_date' => $form_state->getValue('birth_date'),
          '@email' => $form_state->getValue('email'),
          )));
    }
  }
}
