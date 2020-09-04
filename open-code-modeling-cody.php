<?php

use EventEngine\CodeGenerator\Cartridge;
use EventEngine\InspectioGraph;
use Laminas\Filter;
use OpenCodeModeling\CodeGenerator;

// default filters
$filterConstName = new Filter\FilterChain();
$filterConstName->attach(new Cartridge\EventEngine\Filter\NormalizeLabel());
$filterConstName->attach(new Filter\Word\SeparatorToSeparator(' ', ''));
$filterConstName->attach(new Filter\Word\CamelCaseToUnderscore());
$filterConstName->attach(new Filter\Word\DashToUnderscore());
$filterConstName->attach(new Filter\StringToUpper());

$filterConstValue = new Filter\FilterChain();
$filterConstValue->attach(new Cartridge\EventEngine\Filter\NormalizeLabel());
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
        InspectioGraph\Cody\CodeGenerator\WorkflowConfigFactory::CodyJsonToEventSourcingAnalyzer(
            InspectioGraph\Cody\CodeGenerator\WorkflowConfigFactory::SLOT_JSON,
            $filterConstName,
            new InspectioGraph\Cody\Metadata\NodeJsonMetadataFactory()
        ),

        Cartridge\EventEngine\WorkflowConfigFactory::prototypeConfig(
            $workflowContext,
            InspectioGraph\Cody\CodeGenerator\WorkflowConfigFactory::SLOT_EVENT_SOURCING_ANALYZER,
            'src/Domain/Model',
            'src/Domain/Api',
            $filterConstName,
            $filterConstValue,
            $filterDirectoryToNamespace
        ),
        Cartridge\EventEngine\WorkflowConfigFactory::codeToFilesForPrototypeConfig(),
        Cartridge\EventEngine\WorkflowConfigFactory::functionalConfig(
            $workflowContext,
            InspectioGraph\Cody\CodeGenerator\WorkflowConfigFactory::SLOT_EVENT_SOURCING_ANALYZER,
            'src/Domain/Model',
            'src/Domain/Model',
            'src/Domain/Model',
            $filterConstName,
            $filterConstValue,
            $filterDirectoryToNamespace
        ),
        Cartridge\EventEngine\WorkflowConfigFactory::codeToFilesForFunctionalConfig(),
    ]
);

$config->addConsoleCommands(new InspectioGraph\Cody\Console\CodyJsonGenerateAllCommand());

return $config;
