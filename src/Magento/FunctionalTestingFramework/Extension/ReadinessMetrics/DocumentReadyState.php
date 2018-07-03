<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension\ReadinessMetrics;

/**
 * Class DocumentReadyState
 */

use Codeception\TestInterface;
use Facebook\WebDriver\Exception\UnexpectedAlertOpenException;
use Magento\FunctionalTestingFramework\Extension\PageReadinessExtension;

/**
 * Class DocumentReadyState
 *
 * Looks for document.readyState == 'complete' before passing the readiness check
 */
class DocumentReadyState extends AbstractMetricCheck
{
    /**
     * Metric passes when document.readyState == 'complete'
     *
     * @param string $value
     * @return bool
     */
    protected function doesMetricPass($value)
    {
        return $value === 'complete';
    }

    /**
     * Retrieve document.readyState
     *
     * @return string
     * @throws UnexpectedAlertOpenException
     */
    protected function fetchValueFromPage()
    {
        return $this->executeJS('return document.readyState;');
    }
}
