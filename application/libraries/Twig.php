<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Twig Library
 */
require_once APPPATH . 'third_party/Twig/lib/Twig/Autoloader.php';

/**
 * Twig Template Library Wrapper
 */
class Twig {

/**
 * @var Twig_Environment
 */
 protected $twig_instance;
 private $CI;

/**S
 * Twig constructor
 */
 public function __construct() {

    Twig_Autoloader::register();
    $this->CI = & get_instance();

    // All these settings might be loaded from
    // the a config file if you want. Just store
    // them there and fetch the values as:
    // $this->CI->config->item(‘some_value’);
    $laSettings['debug']            = false;
    $laSettings['charset']          = 'utf-8';
    $laSettings['base_template_class'] = 'Twig_Template';
    $laSettings['cache']            = false;
    $laSettings['auto_reload']      = true;
    $laSettings['strict_variables'] = false;
    $laSettings['optimizations']    = -1;

    $loLoader  = new Twig_Loader_Filesystem(APPPATH.'views');
    $this->twig_instance = new Twig_Environment($loLoader, $laSettings);

	$this->ci_function_init();
}



public function ci_function_init_one($twig_name, $callable)
{
   $this->twig_instance->addFunction(
     new Twig_SimpleFunction($twig_name, $callable, array('is_safe' => array('html')))
   );
}

public function ci_function_init()
{
  // url
  $this->ci_function_init_one('base_url', 'base_url');
  $this->ci_function_init_one('site_url', 'site_url');
  $this->ci_function_init_one('current_url', 'current_url');
  $this->ci_function_init_one('current_path', 'current_path');

  // form functions
  $this->ci_function_init_one('form_open', 'form_open');
  $this->ci_function_init_one('form_hidden', 'form_hidden');
  $this->ci_function_init_one('form_input', 'form_input');
  $this->ci_function_init_one('form_password', 'form_password');
  $this->ci_function_init_one('form_upload', 'form_upload');
  $this->ci_function_init_one('form_textarea', 'form_textarea');
  $this->ci_function_init_one('form_dropdown', 'form_dropdown');
  $this->ci_function_init_one('form_multiselect', 'form_multiselect');
  $this->ci_function_init_one('form_fieldset', 'form_fieldset');
  $this->ci_function_init_one('form_fieldset_close', 'form_fieldset_close');
  $this->ci_function_init_one('form_checkbox', 'form_checkbox');
  $this->ci_function_init_one('form_radio', 'form_radio');
  $this->ci_function_init_one('form_submit', 'form_submit');
  $this->ci_function_init_one('form_label', 'form_label');
  $this->ci_function_init_one('form_reset', 'form_reset');
  $this->ci_function_init_one('form_button', 'form_button');
  $this->ci_function_init_one('form_close', 'form_close');
  $this->ci_function_init_one('form_prep', 'form_prep');
  $this->ci_function_init_one('set_value', 'set_value');
  $this->ci_function_init_one('set_select', 'set_select');
  $this->ci_function_init_one('set_checkbox', 'set_checkbox');
  $this->ci_function_init_one('set_radio', 'set_radio');
  $this->ci_function_init_one('form_open_multipart', 'form_open_multipart');
}



/**
 * __call
 * @param string $method
 * @param array $args
 * @throws Exception
*/
public function __call($method, $args)
{
    if ( ! method_exists($this->twig_instance, $method)) {
        throw new Exception("Undefined method $method attempt in the Twig class.");
    }

    $this->CI->output->append_output( call_user_func_array(array($this->twig_instance, $method), $args) );
}
}
