<?php
namespace Jraisanen\Storefront\Api;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Integration\Model\Oauth\Token;

interface AuthInterface
{
    public function __construct(
        Http $httpRequest,
        Token $token,
        CustomerTokenServiceInterface $tokenService
    );

    /**
     * Logout
     *
     * @throws LocalizedException
     * @return bool
     */
    public function logout();
}
