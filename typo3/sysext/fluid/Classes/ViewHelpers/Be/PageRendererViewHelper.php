<?php
namespace TYPO3\CMS\Fluid\ViewHelpers\Be;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which allows you to create extbase based modules in the style of TYPO3 default modules.
 *
 * = Examples =
 *
 * <code title="All options">
 * <f:be.pageRenderer pageTitle="foo" includeCssFiles="0: '{f:uri.resource(path:\'Css/Styles.css\')}'" includeJsFiles="0: '{f:uri.resource(path:\'JavaScript/Library1.js\')}', 1: '{f:uri.resource(path:\'JavaScript/Library2.js\')}'" addJsInlineLabels="{0: 'label1', 1: 'label2'}" />
 * </code>
 * <output>
 *
 * Custom CSS file EXT:your_extension/Resources/Public/Css/styles.css and
 * JavaScript files EXT:your_extension/Resources/Public/JavaScript/Library1.js and EXT:your_extension/Resources/Public/JavaScript/Library2.js
 * will be loaded, plus some inline labels for usage in JS code.
 * </output>
 */
class PageRendererViewHelper extends AbstractViewHelper
{
    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @param PageRenderer $pageRenderer
     */
    public function injectPageRenderer(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('pageTitle', 'string', 'title tag of the module. Not required by default, as BE modules are shown in a frame', false, '');
        $this->registerArgument('includeCssFiles', 'array', 'List of custom CSS file to be loaded');
        $this->registerArgument('includeJsFiles', 'array', 'List of custom JavaScript file to be loaded');
        $this->registerArgument('addJsInlineLabels', 'array', 'Custom labels to add to JavaScript inline labels');
        $this->registerArgument('includeRequireJsModules', 'array', 'List of RequireJS modules to be loaded');
        $this->registerArgument('addInlineSettings', 'array', 'Adds Javascript Inline Setting');
    }

    /**
     * Render start page with \TYPO3\CMS\Backend\Template\DocumentTemplate and pageTitle
     */
    public function render()
    {
        $pageTitle = $this->arguments['pageTitle'];
        $includeCssFiles = $this->arguments['includeCssFiles'];
        $includeJsFiles = $this->arguments['includeJsFiles'];
        $addJsInlineLabels = $this->arguments['addJsInlineLabels'];
        $includeRequireJsModules = $this->arguments['includeRequireJsModules'];
        $addInlineSettings = $this->arguments['addInlineSettings'];

        if ($pageTitle) {
            $this->pageRenderer->setTitle($pageTitle);
        }
        // Include custom CSS and JS files
        if (is_array($includeCssFiles) && count($includeCssFiles) > 0) {
            foreach ($includeCssFiles as $addCssFile) {
                $this->pageRenderer->addCssFile($addCssFile);
            }
        }
        if (is_array($includeJsFiles) && count($includeJsFiles) > 0) {
            foreach ($includeJsFiles as $addJsFile) {
                $this->pageRenderer->addJsFile($addJsFile);
            }
        }
        if (is_array($includeRequireJsModules) && count($includeRequireJsModules) > 0) {
            foreach ($includeRequireJsModules as $addRequireJsFile) {
                $this->pageRenderer->loadRequireJsModule($addRequireJsFile);
            }
        }

        if (is_array($addInlineSettings) && count($addInlineSettings) > 0) {
            $this->pageRenderer->addInlineSettingArray(null, $addInlineSettings);
        }

        // Add inline language labels
        if (is_array($addJsInlineLabels) && count($addJsInlineLabels) > 0) {
            $extensionKey = $this->controllerContext->getRequest()->getControllerExtensionKey();
            foreach ($addJsInlineLabels as $key) {
                $label = LocalizationUtility::translate($key, $extensionKey);
                $this->pageRenderer->addInlineLanguageLabel($key, $label);
            }
        }
    }
}
