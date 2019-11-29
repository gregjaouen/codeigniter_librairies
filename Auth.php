<?php  

/**
 * Authorization implementation as CodeIgniter Library:
 * 
 * Must set $config['auth'] in config.php
 * This must contain at least one type, as an associative array.
 * This array must contain:
 *          - 'table'       :   for the table to look at
 *          - 'login'       :   the field name for the login
 *          - 'password'    :   the field name for the password
 * 
 * Example:
 * $config['auth'] = [
 *      'customer' => [
 *          'table'     => 'customer',
 *          'login'     => 'customer_mail',
 *          'password'  => 'customer_password',
 *      ], [
 *      'seller' => [
 *          'table'     => 'seller',
 *          'login'     => 'seller_mail',
 *          'password'  => 'seller_password',
 *      ]
 * ];
 * 
 * PHP version 7.0
 *
 * @category    Login
 * @version     0.3
 * @author      Grégory Jaouën <gregory.jaouen@tutanota.com>
 * @license     http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link        https://github.com/gregjaouen/codeigniter_librairies
 */

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Auth
{
    private const AUTH = 'auth';
    private const LOGIN = 'login';
    private const PASSWORD = 'password';
    private const TABLE = 'table';

    private $auth_config;


    public function __construct(){
        $this->load_config();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');
    }


    /**
     * Try to login user from login/password couple. If OK, register user object in session
     * 
     * @param string        $login              The user's login
     * @param string        $password           The user's password
     * @param string        $type               The type where to find user
     * @param bool          $logout             If true, logout existing user in session
     * @param bool          $destroy_session    If true, destroy session after unsetting user and user_type
     * 
     * @return bool
     * 
     * @uses get_user_data
     * @uses logout
     */
    public function login(string $login, string $pass, string $type, bool $logout=true, bool $destroy_session=false) : bool {
        if (empty($login)||empty($pass)) {
            message("Veuillez remplir les champs");
            return false;
        }
        $user_data = $this->get_user_data($login, $pass, $type);
        if ($user_data != null && !empty($user_data)) {
            if ($logout){
                $this->logout($destroy_session);
            }
            $this->session->set_userdata("user", $user_data);
            $this->session->set_userdata("user_type", $type);
            return true;
        }
        return false;
    }

    /**
     * Unset login session
     * 
     * @param bool  $destroy_session    If true, destroy session after unsetting user and user_type
     * 
     * @return void
     */
    public function logout(bool $destroy_session=false) : void {
        $this->session->unset_userdata("user");
        $this->session->unset_userdata("user_type");
        if ($destroy_session){
            $this->session->sess_destroy();
        }
    }

    /**
     * Check if user is type of one $group. If not, redirect the user
     * 
     * @param array     $group      Array of types to check
     * @param string    $redirect   Location to redirect if user is not authorized
     * 
     * @return bool|void
     */
    public function authorized(array $group, string $redirect) : ?bool {
        if (!is_array($group)){
            $group = array($group);
        }
        if (in_array($this->session->userdata("user_type"), $group)){
            return true;
        }
        redirect(site_url($redirect));
    }


    /**
     * Check if config.php is correctly setted
     * 
     * @return void
     * @throws UnexpectedValueException $config[needed] is missing
     */
    protected function load_config() : void {
        if ($this->config->item(Auth::AUTH) && !empty($this->config->item(Auth::AUTH))){
            $auth_config = $this->config->item(Auth::AUTH);
            foreach($auth_config as $type => $content){
                foreach([Auth::LOGIN, Auth::PASSWORD, Auth::TABLE] as $tester){
                    if (!isset($content[$tester]) || $content[$tester] == null){
                        throw new UnexpectedValueException(sprintf("\$config[%s] is not setted <br>", $tester));
                    }
                }
            }
            $this->auth_config = $auth_config;
        }
        else {
            throw new UnexpectedValueException("\$config[auth] is not setted <br>");
        }
    }


    /**
     * Return the login state
     * 
     * @return bool
     */
    public function is_logged() : bool {
        if ($this->session->user) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Check if user is type of $type
     * 
     * @param string    $type   The type to compare
     * 
     * @return bool
     */
    public function is_type(string $type) : bool {
        return ($this->get_type() == $type);
    }


    /**
     * Return user_type from session
     * 
     * @return string|null
     */
    public function get_type() : ?string {
        return $this->session->userdata("user_type");
    }
    
    
    /**
     * Returns user data from login/password couple. If not, returns null
     * 
     * @param string        $login         The user's login
     * @param string        $password      The user's password
     * @param string        $type          The type where to find user
     * 
     * @return array|null
     * 
     * @uses get_conf
     */
    protected function get_user_data(string $login, string $password, string $type) : ?array {
        $query = $this->db->get_where($this->get_conf($type, Auth::TABLE),[
            $this->get_conf($type, Auth::LOGIN) => $login,
            $this->get_conf($type, Auth::PASSWORD) => $password,
        ]);
        return $query->row_array();
    }


    /**
     * Return the configuration for the type and item name
     * 
     * @param string    $type       The type name
     * @param string    $name       The configuration name
     * 
     * @return string|null
     */
    protected function get_conf(string $type, string $name) : ?string {
        return (isset($this->auth_config[$type][$name])) ? $this->auth_config[$type][$name] : null;
    }

 
    // access global library or model
    public function __get($var)
    {
        return get_instance()->$var;
    }
}
