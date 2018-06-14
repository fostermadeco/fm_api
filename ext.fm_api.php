<?php

class Fm_api_ext {

    var $name           = 'Foster Made API';
    var $version        = '2.0.0';
    var $description    = 'Provides API endpoints for installed add-ons';
    var $settings_exist = 'n';
    var $docs_url       = '';

    var $settings = array();

    var $hooks_and_methods = array(
        'core_boot' => array('core_boot'),
        'sessions_start' => array('sessions_start')
    );

    private $allowedRequestMethods = array(
        'GET',
        'DELETE',
        'POST',
        'PUT'
    );

    private $allowedEndpoints = null;

    function __construct($settings = '')
    {
        $this->settings = $settings;
        $this->allowedEndpoints = ee()->config->item('apiEndpoints') ?: array();
    }

    function activate_extension()
    {
        $this->settings = array();

        foreach ($this->hooks_and_methods as $hook => $methods) {
            foreach ($methods as $method) {
                $data = array(
                    'class'     => __CLASS__,
                    'method'    => $method,
                    'hook'      => $hook,
                    'settings'  => serialize($this->settings),
                    'priority'  => 10,
                    'version'   => $this->version,
                    'enabled'   => 'y'
                );

                ee()->db->insert('extensions', $data);
            }
        }
    }

    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return FALSE;
        }

        if ($current < '2.0.0')
        {
            // Update to version 2.0.0
        }

        ee()->db->where('class', __CLASS__);
        ee()->db->update('extensions', array('version' => $this->version));
    }

    function disable_extension()
    {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

    function sessions_start()
    {
        if (!$this->shouldProcessRequest()) {
            return;
        }
    }

    function core_boot()
    {
        if (!$this->shouldProcessRequest()) {
            return;
        }

        $api = ee()->uri->segment(2);
        $method = strtolower($_SERVER['REQUEST_METHOD']) . ucwords($api);

        ee()->load->library($api);

        if (in_array($_SERVER['REQUEST_METHOD'], $this->allowedRequestMethods)) {
            ee()->{$api}->{$method}();
        }
    }

    /**
     * Disables CSRF protection for POST requests
     *
     * @param string $protect
     */
    private function disableSecureFormsProtection($protect = 'n')
    {
        ee()->config->set_item('disable_csrf_protection', $protect);
    }

    /**
     *
     *
     * @return bool
     */
    private function shouldProcessRequest()
    {
        // Don't run boot requests in the control panel or on action requests.
        if (REQ == 'CP' || REQ == 'ACTION') {
            return false;
        }

        if (ee()->uri->segment(1) == 'api') {
            $this->disableSecureFormsProtection('y');
        }

        // Only proceed if we're making an api request
        if (ee()->uri->segment(1) === 'api' &&
            ee()->uri->segment(2) &&
            in_array(ee()->uri->segment(2), $this->allowedEndpoints)) {

            return true;
        }

        return false;
    }

}