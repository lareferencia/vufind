<?php
/**
 * Admin Statistics Controller
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace LaReferencia\Controller;

/**
 * Class controls VuFind statistical data.
 *
 * @category VuFind2
 * @package  Controller
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */

class StatisticsController extends \VuFind\Controller\AbstractBase
{
    /**
     * Statistics reporting
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function clicksAction()
    {
       
        $view = $this->createViewModel();
        $view->setTemplate('lareferencia/stats/clicks');
        $config = $this->getConfig();

		$solrStats = $this->getServiceLocator()->get('LaReferencia\SolrStats');   

        $view->pivots = $solrStats->getClicksPerSource()->getPivotFacets() ;	
        
        return $view;
    }
    
    /**
     * Statistics reporting
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function countAction()
    {
    	
    	$field = $this->params()->fromQuery('field', 'type');
    	$limit = $this->params()->fromQuery('limit', -1);
    	 
    	 
    	$view = $this->createViewModel();
    	$view->setTemplate('lareferencia/stats/count');
    	$config = $this->getConfig();
    
    	$solrStats = $this->getServiceLocator()->get('LaReferencia\SolrStats');
    
    	$fieldsArray = array( $field );
    	$view->data = $solrStats->getFieldsCountPerNetwork($fieldsArray, $limit);
    	$view->field = $field;
    
    	return $view;
    }
    
    /**
     * Statistics reporting
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function networksAction()
    {
    	
    	$view = $this->createViewModel();
    	$view->setTemplate('lareferencia/stats/networks');
    	$config = $this->getConfig();
    
    	$lrbStats = $this->getServiceLocator()->get('LaReferencia\LRBackendStats');
    
    	$view->data = $lrbStats->getNetworkList();
    	$view->harvestingHistory = $lrbStats->getHarvestingHistory();
    
    	return $view;
    }
    
}

