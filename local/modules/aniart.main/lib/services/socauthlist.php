<?php
namespace Aniart\Main\Services;
/*
 * Список соц. сетей для авторизации
 */
class SocAuthList
{
    /*Твиттер пока не запускается*/
    private $list = ['google' => ['KEY' => GOOGLE_KEY, 'IMG' => SITE_TEMPLATE_PATH.'/images/gp48.png','SECRET_KEY' => GOOGLE_SECRET, 'SVG' => '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="29px" height="29px" viewBox="0 0 49.652 49.652" style="enable-background:new 0 0 49.652 49.652;" xml:space="preserve"><g><g><g><path d="M21.5,28.94c-0.161-0.107-0.326-0.223-0.499-0.34c-0.503-0.154-1.037-0.234-1.584-0.241h-0.066c-2.514,0-4.718,1.521-4.718,3.257c0,1.89,1.889,3.367,4.299,3.367c3.179,0,4.79-1.098,4.79-3.258c0-0.204-0.024-0.416-0.075-0.629C23.432,30.258,22.663,29.735,21.5,28.94z"/><path d="M19.719,22.352c0.002,0,0.002,0,0.002,0c0.601,0,1.108-0.237,1.501-0.687c0.616-0.702,0.889-1.854,0.727-3.077c-0.285-2.186-1.848-4.006-3.479-4.053l-0.065-0.002c-0.577,0-1.092,0.238-1.483,0.686c-0.607,0.693-0.864,1.791-0.705,3.012c0.286,2.184,1.882,4.071,3.479,4.121H19.719L19.719,22.352z"/><path d="M24.826,0C11.137,0,0,11.137,0,24.826c0,13.688,11.137,24.826,24.826,24.826c13.688,0,24.826-11.138,24.826-24.826C49.652,11.137,38.516,0,24.826,0z M21.964,36.915c-0.938,0.271-1.953,0.408-3.018,0.408c-1.186,0-2.326-0.136-3.389-0.405c-2.057-0.519-3.577-1.503-4.287-2.771c-0.306-0.548-0.461-1.132-0.461-1.737c0-0.623,0.149-1.255,0.443-1.881c1.127-2.402,4.098-4.018,7.389-4.018c0.033,0,0.064,0,0.094,0c-0.267-0.471-0.396-0.959-0.396-1.472c0-0.255,0.034-0.515,0.102-0.78c-3.452-0.078-6.035-2.606-6.035-5.939c0-2.353,1.881-4.646,4.571-5.572c0.805-0.278,1.626-0.42,2.433-0.42h7.382c0.251,0,0.474,0.163,0.552,0.402c0.078,0.238-0.008,0.5-0.211,0.647l-1.651,1.195c-0.099,0.07-0.218,0.108-0.341,0.108H24.55c0.763,0.915,1.21,2.22,1.21,3.685c0,1.617-0.818,3.146-2.307,4.311c-1.15,0.896-1.195,1.143-1.195,1.654c0.014,0.281,0.815,1.198,1.699,1.823c2.059,1.456,2.825,2.885,2.825,5.269C26.781,33.913,24.89,36.065,21.964,36.915z M38.635,24.253c0,0.32-0.261,0.58-0.58,0.58H33.86v4.197c0,0.32-0.261,0.58-0.578,0.58h-1.195c-0.322,0-0.582-0.26-0.582-0.58v-4.197h-4.192c-0.32,0-0.58-0.258-0.58-0.58V23.06c0-0.32,0.26-0.582,0.58-0.582h4.192v-4.193c0-0.321,0.26-0.58,0.582-0.58h1.195c0.317,0,0.578,0.259,0.578,0.58v4.193h4.194c0.319,0,0.58,0.26,0.58,0.58V24.253z"/></g></g></g></g></svg>'],
                    'facebook' => ['KEY' => FACEBOOK_KEY,  'IMG' => SITE_TEMPLATE_PATH.'/images/fb48.png', 'SECRET_KEY' => FACEBOOK_SECRET, 'SVG' => '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="29px" height="29px" viewBox="0 0 49.652 49.652" style="enable-background:new 0 0 49.652 49.652;" xml:space="preserve"><g><g><path d="M24.826,0C11.137,0,0,11.137,0,24.826c0,13.688,11.137,24.826,24.826,24.826c13.688,0,24.826-11.138,24.826-24.826C49.652,11.137,38.516,0,24.826,0z M31,25.7h-4.039c0,6.453,0,14.396,0,14.396h-5.985c0,0,0-7.866,0-14.396h-2.845v-5.088h2.845v-3.291c0-2.357,1.12-6.04,6.04-6.04l4.435,0.017v4.939c0,0-2.695,0-3.219,0c-0.524,0-1.269,0.262-1.269,1.386v2.99h4.56L31,25.7z"/></g></g></svg>'],
                    //'twitter' => ['KEY' => TWITTER_KEY, 'SECRET_KEY' => TWITTER_SECRET, 'SVG' => '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="29px" height="29px" viewBox="0 0 49.652 49.652" style="enable-background:new 0 0 49.652 49.652;" xml:space="preserve"><g><g><path d="M24.826,0C11.137,0,0,11.137,0,24.826c0,13.688,11.137,24.826,24.826,24.826c13.688,0,24.826-11.138,24.826-24.826C49.652,11.137,38.516,0,24.826,0z M35.901,19.144c0.011,0.246,0.017,0.494,0.017,0.742c0,7.551-5.746,16.255-16.259,16.255c-3.227,0-6.231-0.943-8.759-2.565c0.447,0.053,0.902,0.08,1.363,0.08c2.678,0,5.141-0.914,7.097-2.446c-2.5-0.046-4.611-1.698-5.338-3.969c0.348,0.066,0.707,0.103,1.074,0.103c0.521,0,1.027-0.068,1.506-0.199c-2.614-0.524-4.583-2.833-4.583-5.603c0-0.024,0-0.049,0.001-0.072c0.77,0.427,1.651,0.685,2.587,0.714c-1.532-1.023-2.541-2.773-2.541-4.755c0-1.048,0.281-2.03,0.773-2.874c2.817,3.458,7.029,5.732,11.777,5.972c-0.098-0.419-0.147-0.854-0.147-1.303c0-3.155,2.558-5.714,5.713-5.714c1.644,0,3.127,0.694,4.171,1.804c1.303-0.256,2.523-0.73,3.63-1.387c-0.43,1.335-1.333,2.454-2.516,3.162c1.157-0.138,2.261-0.444,3.282-0.899C37.987,17.334,37.018,18.341,35.901,19.144z"/></g></g></svg>']
    ];
    private $redirectUri = SOCAUTH_REDIRECT_URI;

    /*
     * Получить список ссылок
     */
    public function getList()
    {
        $arUrl = [];
        foreach($this->list as $service => $arParams)
        {
            $url = ['URL' => $this->getUrl($service), 'SVG' => $arParams['SVG'], 'IMG' => $arParams['IMG']];
            $arUrl[$service] = $url;
        }
        return $arUrl;
    }

    private function getUrl($service)
    {
        switch($service)
        {
            case 'google': return $this->getGoogleUrl();
                break;
            case 'facebook': return $this->getFacebookUrl();
                break;
            case 'twitter': return $this->getTwitterUrl();
                break;
        }
    }
    private function getGoogleUrl()
    {
        $googleAuthUrl = 'https://accounts.google.com/o/oauth2/auth';
        $params = array(
            'redirect_uri'  => $this->redirectUri,
            'response_type' => 'code',
            'client_id'     => $this->list['google']['KEY'],
            'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
        );
        return $googleAuthUrl . '?' . urldecode(http_build_query($params));
    }

    private function getFacebookUrl()
    {
        $fb = new \Facebook\Facebook([
          'app_id' => $this->list['facebook']['KEY'],
          'app_secret' => $this->list['facebook']['SECRET_KEY'],
          'default_graph_version' => 'v2.2',
          ]);

        $helper = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        return $helper->getLoginUrl($this->redirectUri, $permissions);
    }

    private function getTwitterUrl()
    {
        $connection = new \Abraham\TwitterOAuth\TwitterOAuth($this->list['twitter']['KEY'], $this->list['twitter']['SECRET_KEY']);
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => false));
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        return $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    }

    public function getParams()
    {
        return $this->list;
    }
    
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }
}