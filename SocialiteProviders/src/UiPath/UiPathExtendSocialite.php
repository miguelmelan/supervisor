<?php

namespace SocialiteProviders\UiPath;

use SocialiteProviders\Manager\SocialiteWasCalled;

class UiPathExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('uipath', Provider::class);
    }
}
