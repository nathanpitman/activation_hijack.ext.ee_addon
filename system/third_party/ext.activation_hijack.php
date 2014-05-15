<?php

/**
 * Activation Hijack
 *
 * Allows for the hijack of member activation/validation
 *
 * @package     Activation_hijack
 * @category    Extension
 * @author      Nine Four
 * @link        https://github.com/ninefour
 */

class Activation_hijack_ext {

    public $name                = 'Activation Hijack';
    public $version             = '1.0';
    public $description         = 'Allows for the hijack of member activation/validation';
    public $settings_exist      = 'n';
    public $docs_url            = 'https://github.com/ninefour';

    public $settings            = array();

    // --------------------------------------------------------------------------

    function Activation_hijack_ext($settings='')
    {
        $this->EE =& get_instance();

        $this->settings = $settings;
    }

    // --------------------------------------------------------------------------

    /**
     * Set some current data.
     *
     * @param $session_data
     * @return array The session data.
     */
    function activation_hijack ( $session_data )
    {

        if (ee()->input->get('ACT')) {

            $act = ee()->input->get('ACT');

            if ($act==8 AND ee()->input->get('id')) {

                $id = ee()->input->get('id');
                // Is there even a Pending (group 4) account for this particular user?
                $query = ee()->db->select('member_id, group_id, email')
                ->where('group_id', 4)
                ->where('authcode', $id)
                ->get('members');

                if ($query->num_rows() == 0)
                {
                    // Bypass activation and redirect, this user is no longer pending
                    $path = ee()->functions->create_url('/', FALSE);
                    header("Location: ". $path);
                    exit;
                }
            }
        }

    }

    // --------------------------------------------------------------------------

    /**
     * Activate Extension
     *
     * @return void
     */
    function activate_extension()
    {
        $data = array(
            'class'     => __CLASS__,
            'method'    => 'activation_hijack',
            'hook'      => 'sessions_start',
            'settings'  => '',
            'priority'  => 6,
            'version'   => $this->version,
            'enabled'   => 'y'
        );

        $this->EE->db->insert('extensions', $data);
    }

    // --------------------------------------------------------------------------

    /**
     * Disable Extension
     *
     * @return void
     */
    function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

}

/* End of file ext.activation_hijack.php */
/* Location: ./system/expressionengine/third_party/ext.activation_hijack.php */