<?php
/**
 * Update Project Status Command
 *
 * @category  TextMaster
 * @package   TextMaster\TextMaster
 * @copyright 2021 TextMaster
 */

declare(strict_types=1);

namespace TextMaster\TextMaster\Console\Command;

use TextMaster\TextMaster\Model\Connector\CreateProject;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProjectStatus extends AbstractCommand
{
    const PROJECT_ID = 'project_id';
    const CALLBACK = 'callback';
    const DRY_RUN = 'dry-run';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::PROJECT_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Project Id'
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

        $this->setName('textmaster:update-project-status');
        $this->setDescription('Manually call API TextMaster to update project status');
        $this->setDefinition($options);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getOption(self::PROJECT_ID);
        $callback = $input->getOption(self::CALLBACK);
        $dryRun = $input->getOption(self::DRY_RUN);
        if (empty($projectId) || empty($callback)) {
            $output->writeln(
                '<comment>textmaster:update-project-status --project_id PROJECT_ID --callback CALLBACK</comment>'
            );
            return;
        }
        if (!in_array($callback, CreateProject::TEXTMASTER_PROJECT_CALLBACKS)) {
            $output->writeln(
                '<error>callback allowed values: ' .
                PHP_EOL .
                implode(PHP_EOL, CreateProject::TEXTMASTER_PROJECT_CALLBACKS) .
                '</error>'
            );
            return;
        }

        try {
            $output->writeln('<info>Update Project Status: projectId ' . $projectId . '</info>');
            $project = $this->projectRepository->getById((int) $projectId);

            if ($project->getProjectId()) {
                $output->writeln('<info>Project exists</info>');
                $output->writeln('<info>Call API Textmaster</info>');
                $response = $this->connectorHelper->getProjects($project->getTextMasterId());

                $token = $project->getToken();

                if ($dryRun) {
                    $baseUrl = $this->storeManager->getDefaultStoreView()->getBaseUrl();
                    $uri = $baseUrl . sprintf(CreateProject::MAGENTO_PROJECT_CALLBACK_URL, $token, $callback);
                    $output->writeln('<info>Web API Callback Project Url</info>');
                    $output->writeln('<info>' . $uri . '</info>');
                    $output->writeln('<info>Json Body</info>');
                    $output->writeln('<info>' . $this->serializer->serialize($response) . '</info>');
                }

                if (!$dryRun) {
                    $callbackModel = $this->callbackFactory->create();
                    $output->writeln('<info>Call Callback project</info>');
                    $callbackResponse = $callbackModel->updateProjectStatus(
                        $token,
                        $callback,
                        $response['id'],
                        $response['status']
                    );
                    if (isset($callbackResponse)) {
                        $output->writeln('<info>Response: ' . $callbackResponse->getMessage() . '</info>');
                    }
                }
            }
        } catch (Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
