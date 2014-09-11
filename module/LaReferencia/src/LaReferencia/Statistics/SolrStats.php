<?php
/**
 * Solr Statistics Driver
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
 * @package  Statistics
 * @author   Chris Hallberg <challber@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace LaReferencia\Statistics;

use VuFindSearch\Backend\Solr\Backend;
use VuFindSearch\Query\Query;
use VuFindSearch\ParamBag;

/**
 * Writer to put statistics to the Solr index
 *
 * @category VuFind2
 * @package  Statistics
 * @author   Chris Hallberg <challber@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
class SolrStats
{
    
    /**
     * Solr backend
     *
     * @var Backend
     */
    protected $solrBackend;

    /**
     * Constructor
     *
     * @param Backend $backend Solr backend
     */
    public function __construct(Backend $backend)
    {
        $this->solrBackend = $backend;
    }


    /**
     * Get the total count of a field.
     *
     * @param string $field What field of data are we researching?
     * @param array  $value Extra options for search. Value => match this value
     *
     * @return array
     */
    public function getFullList($field, $value = array('value' => '[* TO *]'))
    {
        $query = new Query($field.':'.$value['value']);
        $params = new ParamBag();
        $params->add('fl', $field);
        $start = 0;
        $limit = 1000;
        $data = array();
        do {
            $response = $this->solrBackend->search($query, $start, $limit, $params);
            $records = $response->getRecords();
            foreach ($records as $doc) {
                $data[] = array($field => $doc->$field);
            }
            $start += $limit;
        } while (count($records) > 0);
        return $data;
    }

    /**
     * Returns browser usage statistics
     *
     * @param bool    $version    Include the version numbers in the list
     * @param integer $listLength How many items to return
     *
     * @return array
     */
    public function getBrowserStats($version, $listLength = 5)
    {
        $query = new Query('*:*');
        $params = new ParamBag();
        $params->add('fl', 'browser,browserVersion');
        $params->add('group', 'true');
        $params->add('group.field', 'session');
        $start = 0;
        $limit = 1000;
        $hashes = array();
        do {
            $response = $this->solrBackend->search($query, $start, $limit, $params);
            $groups = $response->getGroups();
            foreach ($groups['session']['groups'] as $group) {
                if ($version) {
                    // Version specific
                    $browser = $group['doclist']['docs'][0]['browser']
                        .' '.$group['doclist']['docs'][0]['browserVersion'];
                    if (isset($hashes[$browser])) {
                        $hashes[$browser] ++;
                    } elseif (count($hashes) < $limit) {
                        $hashes[$browser] = 1;
                    }
                } else {
                    // Browser name
                    if (isset($hashes[$group['doclist']['docs'][0]['browser']])) {
                        $hashes[$group['doclist']['docs'][0]['browser']] ++;
                    } elseif (count($hashes) < $limit) {
                        $hashes[$group['doclist']['docs'][0]['browser']] = 1;
                    }
                }
            }
            $start += $limit;
        } while (count($groups['session']['groups']) > 0);
        $solrBrowsers = array();
        foreach ($hashes as $browser=>$count) {
            $newBrowser = array(
                'browserName' => $browser,
                'count' => $count
            );
            // Insert sort (limit to listLength)
            for ($i=0;$i<$listLength-1 && $i<count($solrBrowsers);$i++) {
                if ($count > $solrBrowsers[$i]['count']) {
                    // Insert in order
                    array_splice($solrBrowsers, $i, 0, array($newBrowser));
                    continue 2; // Skip the append after this loop
                }
            }
            if (count($solrBrowsers) < $listLength) {
                $solrBrowsers[] = $newBrowser;
            }
        }
        return $solrBrowsers;
    }
}
