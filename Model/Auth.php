<?php
namespace Jraisanen\Storefront\Model;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Integration\Model\Oauth\Token;
use Jraisanen\Storefront\Api\AuthInterface;

class Auth implements AuthInterface
{
    private $_httpRequest;
    private $_token;
    private $_tokenService;

    public function __construct(
        Http $httpRequest,
        Token $token,
        CustomerTokenServiceInterface $tokenService
    ) {
        $this->_httpRequest = $httpRequest;
        $this->_token = $token;
        $this->_tokenService = $tokenService;
    }

    /**
     * Logout
     *
     * @throws LocalizedException
     * @return bool
     */
    public function logout()
    {
        return $this->_tokenService->revokeCustomerAccessToken($this->getCustomerId());
    }

    /**
     * Get customer id by the authorization header
     *
     * @return int
     */
    public function getCustomerId()
    {
        $accessToken = explode(' ', $this->_httpRequest->getHeader('Authorization'))[1];
        return $this->_token->loadByToken($accessToken)->getCustomerId();
    }
}
