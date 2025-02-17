<?php
/**
 * Update Document Status Command
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Console\Command;

use TextMaster\TextMaster\Model\Connector\CreateDocument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDocumentStatus extends AbstractCommand
{
    const DOCUMENT_ID = 'document_id';
    const CALLBACK = 'callback';
    const DRY_RUN = 'dry-run';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::DOCUMENT_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Document Id'
            ),
            new InputOption(
                self::CALLBACK,
                null,
                InputOption::VALUE_REQUIRED,
                'Callback'
            ),
            new InputOption(
                self::DRY_RUN,
                null,
                InputOption::VALUE_NONE,
                'Dry run (display api url and json body with no changes made)'
            )
        ];

        $this->setName('textmaster:update-document-status');
        $this->setDescription('Manually call API TextMaster to update document status');
        $this->setDefinition($options);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $documentId = $input->getOption(self::DOCUMENT_ID);
        $callback = $input->getOption(self::CALLBACK);
        $dryRun = $input->getOption(self::DRY_RUN);
        if (empty($documentId) || empty($callback)) {
            $output->writeln(
                '<comment>textmaster:update-document-status --document_id DOCUMENT_ID --callback CALLBACK</comment>'
            );
            return;
        }
        if (!in_array($callback, CreateDocument::TEXTMASTER_DOCUMENT_CALLBACKS)) {
            $output->writeln(
                '<error>callback allowed values: ' .
                PHP_EOL .
                implode(PHP_EOL, CreateDocument::TEXTMASTER_DOCUMENT_CALLBACKS) .
                '</error>'
            );
            return;
        }

        try {
            $output->writeln(
                '<info>Update Document Status: documentId ' . $documentId . '</info>'
            );
            $document = $this->documentRepository->getById((int) $documentId);
            if ($document->getDocumentId()) {
                $output->writeln('<info>Document exists</info>');
                $output->writeln('<info>Call API Textmaster</info>');
                $response = $this->connectorHelper->getDocuments(
                    $document->getProject()->getTextMasterId(),
                    $document->getTextMasterId()
                );

                $token = $document->getToken();

                if ($dryRun) {
                    $baseUrl = $this->storeManager->getDefaultStoreView()->getBaseUrl();
                    $uri = $baseUrl . sprintf(CreateDocument::MAGENTO_DOCUMENT_CALLBACK_URL, $token, $callback);
                    $output->writeln('<info>Web API Callback Document Url</info>');
                    $output->writeln('<info>' . $uri . '</info>');
                    $output->writeln('<info>Json Body</info>');
                    $output->writeln('<info>' . $this->serializer->serialize($response) . '</info>');
                }

                if (!$dryRun) {
                    $callbackModel = $this->callbackFactory->create();
                    $output->writeln('<info>Call Callback document</info>');
                    $callbackResponse = $callbackModel->updateDocumentStatus(
                        $token,
                        $callback,
                        $response['id'] ?? $response['Id'],
                        $response['status']
                    );
                    if (isset($callbackResponse)) {
                        $output->writeln('<info>Response: ' . $callbackResponse->getMessage() . '</info>');
                    }
                }
            }
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
