<?php


namespace MaxRedemption\MaxRedemptionPayment\Controller\Ipn;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use MaxRedemption\MaxRedemptionPayment\Helper\Data as MaxRedemptionPaymentHelper;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

class Handler extends Action implements CsrfAwareActionInterface
{

    const API_KEY = 'payment/maxredemptionpayment/api_key';
    const API_ID = 'payment/maxredemptionpayment/api_iss';

    /**
     * @var ResponseFactory
     */
    private $_responseFactory;

    /**
     * @var JsonFactory
     */
    private $_resultJsonFactory;

    /**
     * @var OrderInterface
     */
    private $orderInterface;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var MaxRedemptionPaymentHelper
     */
    private $maxredemptionPaymentHelper;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param ResponseFactory $responseFactory
     * @param JsonFactory $resultJsonFactory
     * @param OrderInterface $orderInterface
     * @param MaxRedemptionPaymentHelper $maxredemptionPaymentHelper
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        ResponseFactory $responseFactory,
        JsonFactory $resultJsonFactory,
        OrderInterface $orderInterface,
        MaxRedemptionPaymentHelper $maxredemptionPaymentHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_responseFactory = $responseFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->orderInterface = $orderInterface;
        $this->maxredemptionPaymentHelper = $maxredemptionPaymentHelper;

        return parent::__construct($context);
    }

    public function execute()
    {
        $token = $this->getRequest()->getContent();

        $api_key = $this->scopeConfig->getValue(self::API_KEY, ScopeInterface::SCOPE_STORE);
        $api_iss = $this->scopeConfig->getValue(self::API_ID, ScopeInterface::SCOPE_STORE);

        $resultJson = $this->_resultJsonFactory->create();
        try {
            $payload = $this->maxredemptionPaymentHelper->getDecodeToken($token, $api_key, ['HS256']);
        } catch (Exception $exception) {
            $response = ['error' => 'true', 'message' => $exception->getMessage()];
            $resultJson->setData($response)->setHttpResponseCode(500);
        }

        if (isset($payload->orderNumber)) {

            $order = $this->orderInterface->loadByIncrementId($payload->orderNumber);
            $total = $order->getBaseGrandTotal();
            if ($payload->iss != $api_iss || !$order || $payload->amount != $total) {
                $response = ['error' => 'true', 'message' => 'IPN does not match with any existent order'];
                return $resultJson->setData($response)->setHttpResponseCode(500);
            }

            if ($payload->status === 'SOLD') {
                $order->addStatusHistoryComment(sprintf('eGiftCertificate obtained: %s', $payload->pin))
                    ->setIsCustomerNotified(true);
                $order->save();
                $response = ['success' => 'true'];
                return $resultJson->setData($response)->setHttpResponseCode(200);
            }

            if ($payload->status === 'USED') {
                $order->addStatusHistoryComment(sprintf('eGiftCertificate validated & redeemed successfully'))
                    ->setIsCustomerNotified(true);
                $order->save();
                $order->setState(Order::STATE_PROCESSING)
                    ->setStatus(Order::STATE_PROCESSING);
                $order->save();
                $response = ['success' => 'true'];
                return $resultJson->setData($response)->setHttpResponseCode(200);
            }

        } else {
            $response = ['error' => 'true', 'message' => 'Invalid IPN Payload'];
            return $resultJson->setData($response)->setHttpResponseCode(500);
        }

        return $resultJson;
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
