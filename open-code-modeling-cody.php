<?php

use EventEngine\CodeGenerator\Cartridge;
use EventEngine\CodeGenerator\EventEngineAst;
use EventEngine\InspectioGraphCody;
use Laminas\Filter;
use OpenCodeModeling\CodeGenerator;

// default filters
$filterConstName = new Filter\FilterChain();
$filterConstName->attach(new EventEngineAst\Filter\NormalizeLabel());
$filterConstName->attach(new Filter\Word\SeparatorToSeparator(' ', ''));
$filterConstName->attach(new Filter\Word\CamelCaseToUnderscore());
$filterConstName->attach(new Filter\Word\DashToUnderscore());
$filterConstName->attach(new Filter\StringToUpper());

$filterConstValue = new Filter\FilterChain();
$filterConstValue->attach(new EventEngineAst\Filter\NormalizeLabel());
$filterConstValue->attach(new Filter\Word\SeparatorToSeparator(' ', '-'));
$filterConstValue->attach(new Filter\Word\UnderscoreToCamelCase());
$filterConstValue->attach(new Filter\Word\DashToCamelCase());

$filterDirectoryToNamespace = new Filter\FilterChain();
$filterDirectoryToNamespace->attach(new Filter\Word\SeparatorToSeparator(DIRECTORY_SEPARATOR, '|'));
$filterDirectoryToNamespace->attach(new Filter\Word\SeparatorToSeparator('|', '\\\\'));

/**
 * slot SLOT_XML_FILE and SLOT_XSL_FILE are provided via \EventEngine\CodeGenerator\Inspectio\Console\XmlGenerateAllCommand
 */

/** @var CodeGenerator\Workflow\WorkflowContext $workflowContext */
$workflowContext->put('xml_filename', 'data/domain.xml');

$config = new CodeGenerator\Config\WorkflowList(
    ...[
        InspectioGraphCody\CodeGenerator\WorkflowConfigFactory::codyJsonToEventSourcingAnalyzer(
            InspectioGraphCody\CodeGenerator\WorkflowConfigFactory::SLOT_JSON,
            $filterConstName,
            new InspectioGraphCody\Metadata\NodeJsonMetadataFactory()
        ),

        Cartridge\EventEngine\PrototypeWorkflowFactory::prototypeConfig(
            $workflowContext,
            InspectioGraphCody\CodeGenerator\WorkflowConfigFactory::SLOT_EVENT_SOURCING_ANALYZER,
            'src/Domain/Model',
            'src/Domain/Api',
            $filterConstName,
            $filterConstValue,
            $filterDirectoryToNamespace
        ),
        Cartridge\EventEngine\PrototypeWorkflowFactory::codeToFilesForPrototypeConfig(),
        Cartridge\EventEngine\FunctionalWorkflowFactory::functionalConfig(
            $workflowContext,
            InspectioGraphCody\CodeGenerator\WorkflowConfigFactory::SLOT_EVENT_SOURCING_ANALYZER,
            'src/Domain/Model',
            'src/Domain/Model',
            'src/Domain/Model',
            $filterConstName,
            $filterConstValue,
            $filterDirectoryToNamespace
        ),
        Cartridge\EventEngine\FunctionalWorkflowFactory::codeToFilesForFunctionalConfig(),
    ]
);

$config->addConsoleCommands(new InspectioGraphCody\Console\CodyJsonGenerateAllCommand());

return $config;
