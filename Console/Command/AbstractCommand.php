<?php
/**
 * Class Abstract Command
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Console\Command;

use TextMaster\TextMaster\Api\CallbackInterfaceFactory;
use TextMaster\TextMaster\Api\DocumentRepositoryInterface;
use TextMaster\TextMaster\Api\ProjectRepositoryInterface;
use TextMaster\TextMaster\Helper\Connector as ConnectorHelper;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    /**
     * @var ProjectRepositoryInterface
     */
    protected $projectRepository;

    /**
     * @var DocumentRepositoryInterface
     */
    protected $documentRepository;

    /**
     * @var ConnectorHelper
     */
    protected $connectorHelper;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CallbackInterfaceFactory
     */
    protected $callbackFactory;

    /**
     * @method __construct
     * @param ProjectRepositoryInterface $projectRepository
     * @param DocumentRepositoryInterface $documentRepository
     * @param ConnectorHelper $connectorHelper
     * @param SerializerInterface $serializer
     * @param StoreManagerInterface $storeManager
     * @param CallbackInterfaceFactory $callbackFactory
     */
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        DocumentRepositoryInterface $documentRepository,
        ConnectorHelper $connectorHelper,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        CallbackInterfaceFactory $callbackFactory
    ) {
        $this->projectRepository = $projectRepository;
        $this->documentRepository = $documentRepository;
        $this->connectorHelper = $connectorHelper;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->callbackFactory = $callbackFactory;
        parent::__construct();
    }
}
