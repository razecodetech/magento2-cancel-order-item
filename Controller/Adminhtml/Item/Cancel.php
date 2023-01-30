<?php

namespace Razecode\CancelOrderItem\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Setup\Exception;

class Cancel extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $_itemFactory;
	protected $_orderFactory;
	protected $_orderRepository;
	protected $_eventManager;
	protected $_items;

	/**
	 * Item constructor.
	 * @param Context $context
	 * @param ItemFactory $itemFactory
	 * @param OrderFactory $orderFactory
	 * @param OrderRepositoryInterface $orderRepository
	 */
	public function __construct(
		Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
		ItemFactory $itemFactory,
		OrderFactory $orderFactory,
		OrderRepositoryInterface $orderRepository,
        ManagerInterface $eventManager,
		Item $items
	) {
		$this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
		$this->_itemFactory = $itemFactory;
		$this->_orderFactory = $orderFactory;
		$this->_orderRepository = $orderRepository;
		$this->_eventManager = $eventManager;
		$this->_items = $items;
	}

	/**
	 * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
	 */
    public function execute()
    {
        $id = $this->getRequest()->getParam('itemid');
		$orderId = $this->getRequest()->getParam('orderid');
		$qty = $this->getRequest()->getParam('qty');
		$bagId = $this->getRequest()->getParam('bagid');
                
        try {
			$item = $this->_itemFactory->create();
			$item->load($id);

            $order = $this->_orderFactory->create();
			$order->load($orderId);
			$itemsOrdered = $order->getTotalItemCount();
		
			$allowedCancel = ($item->getQtyOrdered() - $item->getQtyShipped()) - $item->getQtyCanceled();
                
			$qtyToBeCancelled = $qty + $item->getQtyCanceled();
			$canceledItem = $item->getName();
            $cancelOrder = true;

			foreach ($order->getAllVisibleItems() as $orderItem) {
				if ($orderItem->getItemId() == $id) {
					$orderItem->setQtyCanceled($qtyToBeCancelled)->save();
				}

				if ($orderItem->getQtyOrdered() != $orderItem->getQtyCanceled()) {
					$cancelOrder = false;
				}
			}

			if ($itemsOrdered == 1 && ($item->getQtyOrdered() <= $item->getQtyCanceled())) {
				$cancelOrder = true;
			}

			$this->messageManager->addSuccess(__('You have cancelled item ' . $canceledItem));
            
			if ((int) $cancelOrder) {
				$this->cancel($order);
			}

			if($order->getStatus() == "canceled") {
				//TODO:: on cancel status
			}
	
		} catch (\Exception $e) {
			$this->messageManager->addException($e, $e->getMessage());
		}

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
	}

	protected function cancel($order) {
		$state = \Magento\Sales\Model\Order::STATE_CANCELED;
		$order->setSubtotalCanceled($order->getSubtotal() - $order->getSubtotalInvoiced());
		$order->setBaseSubtotalCanceled($order->getBaseSubtotal() - $order->getBaseSubtotalInvoiced());

		$order->setTaxCanceled($order->getTaxAmount() - $order->getTaxInvoiced());
		$order->setBaseTaxCanceled($order->getBaseTaxAmount() - $order->getBaseTaxInvoiced());

		$order->setShippingCanceled($order->getShippingAmount() - $order->getShippingInvoiced());
		$order->setBaseShippingCanceled($order->getBaseShippingAmount() - $order->getBaseShippingInvoiced());

		$order->setDiscountCanceled(abs($order->getDiscountAmount()) - $order->getDiscountInvoiced());
		$order->setBaseDiscountCanceled(abs($order->getBaseDiscountAmount()) - $order->getBaseDiscountInvoiced());

		$order->setTotalCanceled($order->getGrandTotal() - $order->getTotalPaid());
		$order->setBaseTotalCanceled($order->getBaseGrandTotal() - $order->getBaseTotalPaid());

		$order->setState($state)->setStatus($state);

		$this->_orderRepository->save($order);
	}
}