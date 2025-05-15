<?php

CONST MACRO_SINGLE  = 1;
CONST MACRO_BLOCKER = 2;
CONST MACRO_ALL     = 99;

function _getAddonsMacrosList1(): array {
    $macroEntry = [];
    array_push($macroEntry, "child-counter", "link-window", "link-to", "outgoing-links", "link-page", "add-page");//,"incoming-links");
    $addOns['small-test'] = $macroEntry;

    return $addOns;
}

function _getAddonsMacrosList2(): array {
    $addOns = [
        'Advanced Tables for Confluence'         => [
            'json-table',
            'table-plus',
            'csv',
            'jql-table',
        ],
        'BPMN Modeler Enterprise'                => [
            'vbcp-macro-enterprise',
            'chart-plugin',
            'chart',
        ],
        'Comala Document Management'             => [
            'pagestatus',
            'pageactivity',
        ],
        'Confluence HTML Macros'                 => [
            'html',
            'html-include',
        ],
        'Linking for Confluence'                 => [
            'add-page',
            'link-page',
        ],
        'projectdoc Toolbox for Confluence'      => [
            'projectdoc-properties-marker',
        ],
        'Scroll Documents for Confluence'        => [
            'scroll-document-location',
        ],
        'Scroll Exporter Extensions'             => [
            'scroll-ignore',
            'scroll-tablelayout',
            'scroll-title',
            'scroll-content-block',
            'scroll-pagebreak',
            'scroll-landscape',
            'scroll-portrait',
            'scroll-pagetitle',
            'scroll-only',
            'scroll-exportbutton',
            'scroll-bookmark',
        ],
        'Scroll Platform'                        => [
            'includeplus',
            'sv-pagetree',
            'excerpt-includeplus',
        ],
        'Table Filter and Charts for Confluence' => [
            'table-joiner',
            'table-excerpt',
            'table-excerpt-include',
            'table-filter',
            'table-chart',
            'pivot-table',
            'csv-table',
            'spreadsheet-table',
        ],
    ];

    return $addOns;
}

function _getAddonsMacrosListAll(): array {
    $addOns = [
        'Advanced Roadmaps for Jira in Confluence' => [
            'portfolio-for-jira-plan',
        ],
        'Advanced Tables for Confluence'           => [
            'json-table',
            'table-plus',
            'csv',
            'jql-table',
        ],
        'Basic Macros'                             => [
            'panel',
            'anchor',
            'noformat',
            'loremipsum',
        ],
        'BPMN Modeler Enterprise'                  => [
            'vbcp-macro-enterprise',
            'chart-plugin',
            'chart',
        ],
        'Code Macro Plugin'                        => [
            'code',
        ],
        'Comala Document Management'               => [
            'pagestatus',
            'pageactivity',
            'workflowreport',
            'document-states-report',
            'workflow-reporter',
            'document-stats-report',
            'get-metadata',
        ],
        'Confluence Attachments Plugin'            => [
            'attachments',
        ],
        'Confluence Business Blueprints - Plugin'  => [
            'sharelinks-urlmacro',
        ],
        'Confluence Content Report Plugin'         => [
            'content-report-table',
        ],
        'Confluence Contributors Plugin'           => [
            'contributors',
        ],
        'Confluence Create Content Plugin'         => [
            'create-from-template',
        ],
        'Confluence Expand Macro'                  => [
            'expand',
        ],
        'Confluence HTML Macros'                   => [
            'html',
            'html-include',
        ],
        'Confluence Inline Tasks'                  => [
            'tasks-report-macro',
        ],
        'Confluence Jira Plugin'                   => [
            'jira',
            'jirachart',
        ],
        'Confluence Live Search Macros Plugin'     => [
            'livesearch',
        ],
        'Confluence Roadmap Planner'               => [
            'roadmap',
        ],
        'Confluence View File Macro'               => [
            'view-file',
        ],
        'confluence-advanced-macros'               => [
            'excerpt',
            'children',
            'include',
            'excerpt-include',
            'contentbylabel',
            'recently-updated',
            'change-history',
            'blog-posts',
            'listlabels',
            'gallery',
            'favpages',
            'search',
            'content-by-user',
        ],
        'Dashboard Macros'                         => [
            'spaces',
        ],
        'Draw.io Confluence Plugin'                => [
            'drawio',
            'inc-drawio',
            'drawio-sketch',
        ],
        'Gadgets Plugin'                           => [
            'gadget',
        ],
        'Information Macros Plugin'                => [
            'info',
            'note',
            'warning',
            'tip',
        ],
        'Layout Macros'                            => [
            'column',
            'section',
        ],
        'Linking for Confluence'                   => [
            'incoming-links',
            'add-page',
            'child-counter',
            'link-window',
            'link-to',
            'outgoing-links',
            'link-page',
        ],
        'Office Connector plugin'                  => [
            'viewxls',
            'viewdoc',
            'viewppt',
            'viewpdf',
        ],
        'Page Properties Macros - Plugin'          => [
            'details',
            'detailssummary',
        ],
        'Page Tree Plugin'                         => [
            'pagetree',
            'pagetreesearch',
        ],
        'Profile Macros'                           => [
            'profile',
        ],
        'profile-picture'                          => [
            'profile-picture',
        ],
        'projectdoc Core Doctypes'                 => [
            'projectdoc-stakeholder-rating-macro',
        ],
        'projectdoc for Agile Planning'            => [
            'projectdoc-story-status',
            'projectdoc-story-relevance',
            'projectdoc-story-points',
        ],
        'projectdoc for Software Development'      => [
            'projectdoc-use-case-level',
            'projectdoc-feature-importance',
            'projectdoc-technical-debt-quality',
            'projectdoc-actor-type',
            'projectdoc-user-type',
        ],
        'projectdoc Toolbox for Confluence'        => [
            'projectdoc-properties-marker',
            'projectdoc-transclusion-parent-property',
            'projectdoc-section',
            'projectdoc-name-list',
            'projectdoc-tag-list-macro',
            'projectdoc-display-table',
            'projectdoc-transclusion-property-display',
            'projectdoc-tour-macro',
            'projectdoc-iteration',
            'projectdoc-hide-from-reader-macro',
            'projectdoc-transclusion-macro',
            'projectdoc-table-merger-macro',
            'projectdoc-display-list',
            'projectdoc-transclude-documents-macro',
            'projectdoc-link-wiki',
            'projectdoc-aside-panel-macro',
            'projectdoc-content-marker',
            'projectdoc-properties-supplier-macro',
            'projectdoc-link-external',
            'projectdoc-level-macro',
            'projectdoc-transclusion-property-display-ref',
            'projectdoc-tour-by-property-macro',
            'projectdoc-transclusion-properties-display',
            'projectdoc-transclusion-ref-macro',
            'projectdoc-box-info',
            'projectdoc-transclusion-property-display-as-image-macro',
            'projectdoc-hml-rating',
            'projectdoc-display-template-list',
            'projectdoc-transclusion-property-display-ref-concat',
            'projectdoc-box-caution',
            'projectdoc-box-note',
            'projectdoc-code-block-placeholder-macro',
            'projectdoc-physical-children-macro',
            'projectdoc-transclusion-property-display-as-link-macro',
            'projectdoc-definition-list-macro',
            'projectdoc-count-macro',
            'projectdoc-steps-macro',
            'projectdoc-space-property-display-macro',
            'projectdoc-create-one-document-macro',
            'projectdoc-box-warning',
            'projectdoc-box-example',
            'projectdoc-page-include-macro',
            'projectdoc-display-space-attribute-macro',
            'projectdoc-primary-page-property-display-macro',
            'projectdoc-issue-status',
            'projectdoc-quote-external',
            'projectdoc-priority',
            'projectdoc-transclusion-ancestor-property-macro',
            'projectdoc-attachment-link-macro',
            'projectdoc-index-entries-table-macro',
            'projectdoc-box-tip',
            'projectdoc-even-rating-macro',
            'projectdoc-box-question',
            'projectdoc-display-all-properties-macro',
            'projectdoc-quote',
            'projectdoc-table-set-macro',
            'projectdoc-box-generic',
            'projectdoc-property-display-as-list-macro',
            'projectdoc-space-list-macro',
            'projectdoc-complexity',
            'projectdoc-issue-severity',
            'projectdoc-hide',
            'projectdoc-in-document-link-macro',
            'projectdoc-display-all-space-properties-macro',
            'projectdoc-box-feedback',
            'projectdoc-box-fault',
            'projectdoc-box-deprecated',
            'projectdoc-box-pending',
            'projectdoc-box-references',
            'projectdoc-properties-supplier-from-documents-macro',
            'projectdoc-layout-element-macro',
            'projectdoc-box-version',
            'projectdoc-random-static-transclusion-macro',
            'projectdoc-changelog-macro',
            'projectdoc-action-button-macro',
            'projectdoc-dynamic-document-link-macro',
        ],
        'Scroll Documents for Confluence'          => [
            'scroll-document-location',
        ],
        'Scroll Exporter Extensions'               => [
            'scroll-ignore',
            'scroll-tablelayout',
            'scroll-title',
            'scroll-content-block',
            'scroll-pagebreak',
            'scroll-landscape',
            'scroll-portrait',
            'scroll-pagetitle',
            'scroll-only',
            'scroll-exportbutton',
            'scroll-bookmark',
        ],
        'Scroll Platform'                          => [
            'includeplus',
            'sv-pagetree',
            'excerpt-includeplus',
        ],
        'Status Macro'                             => [
            'status',
        ],
        'Table Filter and Charts for Confluence'   => [
            'table-joiner',
            'table-excerpt',
            'table-excerpt-include',
            'table-filter',
            'table-chart',
            'pivot-table',
            'csv-table',
            'spreadsheet-table',
        ],
        'Table of Contents Plugin'                 => [
            'toc',
            'toc-zone',
        ],
        'Team Calendars'                           => [
            'calendar',
        ],
        'User Lister'                              => [
            'userlister',
        ],
        'Widget Connector'                         => [
            'widget',
        ],
        'Wiki Markup Plugin'                       => [
            'unmigrated-wiki-markup',
        ],
    ];

    return $addOns;
}

function getAddonsMacrosList(int $mode = MACRO_SINGLE): array {
    switch ($mode) {
        case MACRO_BLOCKER:
            return _getAddonsMacrosList2();
            break;
        case MACRO_ALL:
            return _getAddonsMacrosListAll();
            break;
        default:
            return _getAddonsMacrosList1();
    }
}
