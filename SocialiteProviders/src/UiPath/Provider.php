<?php

namespace SocialiteProviders\UiPath;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'UIPATH';

    /**
     * {@inheritdoc}
     */
    protected $usesPKCE = true;

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['PM.User.Read openid'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://cloud.uipath.com/identity_/connect/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://cloud.uipath.com/identity_/connect/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://cloud.uipath.com/identity_/connect/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        //dd($user);
        return (new User())->setRaw($user)->map([
            'id'       => $user['prt_id'],
            'nickname' => $user['first_name'],
            'name'     => $user['first_name'] . ' ' . $user['last_name'],
            'email'    => $user['email'],
            'avatar'   => $user['picture'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }
}
