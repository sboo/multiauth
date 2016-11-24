<?php namespace Ollieread\Multiauth;

use Illuminate\Foundation\Application;

/**
 * Class MultiManager
 * @package Ollieread\Multiauth
 */
class MultiManager
{

    /**
     * Registered multiauth providers.
     *
     * @var array
     */
    protected $providers = array();
    protected $currentAuthUser = null;

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        foreach ($app['config']['auth.multi'] as $key => $config) {
            $this->providers[$key] = new AuthManager($app, $key, $config);
        }
        $this->currentAuthUser = session()->get('current_auth_user');
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments = array())
    {
        if (array_key_exists($name, $this->providers)) {
            return $this->providers[$name];
        }
    }

    /**
     * @param string $name
     * @return \Illuminate\Auth\Guard|\Ollieread\Multiauth\Guard
     */
    public function current($name = null){
        if(!is_null($name)){
          session(['current_auth_user'=>$name]);
          $this->currentAuthUser = session()->get('current_auth_user');
        }
        return $this->providers[$this->currentAuthUser];
    }

}
