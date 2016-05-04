<?php
namespace EWW\Dpf\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * SearchFEController
 */
class SearchFEController extends \EWW\Dpf\Controller\AbstractController
{
    /**
     * documentRepository
     *
     * @var \EWW\Dpf\Domain\Repository\DocumentRepository
     * @inject
     */
    protected $documentRepository = null;

    const RESULT_COUNT = 50;

    const NEXT_RESULT_COUNT = 50;

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $args = $this->request->getArguments();

        // assign result list from elastic search
        $this->view->assign('searchList', $args['results']);

        // assign form values
        $this->assignExtraFields($args['extra']);

    }

    public function assignExtraFields($array)
    {
        // assign all form(extra) field values
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $this->view->assign($key, $value);
            }
        }
    }

    /**
     * get next search results
     * @return array ElasticSearch results
     */
    public function nextResultsAction()
    {

        if (!empty($GLOBALS['BE_USER'])) {
            $userGlobals = $GLOBALS['BE_USER'];
        } else {
            $userGlobals = $GLOBALS['TSFE']->fe_user;
        }

        $sessionVars = $userGlobals->getSessionData("tx_dpf");
        if (!$sessionVars['resultCount']) {
            // set number of results in session
            $sessionVars['resultCount'] = self::NEXT_RESULT_COUNT;
        } else {
            $resultCount = $sessionVars['resultCount'];
            $sessionVars['resultCount'] = $resultCount + self::NEXT_RESULT_COUNT;
        }
        $userGlobals->setAndSaveSessionData('tx_dpf', $sessionVars);

        $query = $sessionVars['query'];

        unset($query['extra']);

        $type = 'object';

        $query['body']['from'] = $sessionVars['resultCount'];
        $query['body']['size'] = self::NEXT_RESULT_COUNT;

        $results = $this->getResultList($query, $type);

        $this->view->assign('resultList', $results);
        $this->view->assign('alreadyImported', array());
    }

    /**
     * build array for elasticsearch
     * @return array Elasticsearch query array
     */
    public function extendedSearch()
    {
        $args = $this->request->getArguments();
        $client = $this->clientRepository->findAll()->current();

        // extended search
        $countFields = 0;

        if ($args['extSearch']['extId']) {
            $id = $args['extSearch']['extId'];
            $fieldQuery['_id'] = $id;
            $countFields++;
            // will be removed from query later
            $query['extra']['id'] = $id;
        }

        if ($args['extSearch']['extTitle']) {
            $title = $args['extSearch']['extTitle'];
            $fieldQuery['title'] = $title;
            $countFields++;
            // will be removed from query later
            $query['extra']['title'] = $title;
        }

        if ($args['extSearch']['extAuthor']) {
            $author = $args['extSearch']['extAuthor'];
            $fieldQuery['author'] = $author;
            $countFields++;
            // will be removed from query later
            $query['extra']['author'] = $author;
        }

        if ($args['extSearch']['extDeleted']) {
            // STATE deleted
            $delete['bool']['must'][] = array('match' => array('STATE' => 'D'));
            // STATE inactive
            $inactive['bool']['must'][] = array('match' => array('STATE' => 'I'));

            $query['body']['query']['bool']['should'][] = $delete;
            $query['body']['query']['bool']['should'][] = $inactive;

            $query['body']['query']['bool']['minimum_should_match'] = 1;

            $query['extra']['showDeleted'] = true;

        } else {
            // STATE active
            $deleted = true;
            $fieldQuery['STATE'] = 'A';
            $countFields++;
        }

        if ($countFields >= 1) {
            // multi field search
            $i = 1;
            foreach ($fieldQuery as $key => $qry) {
                $query['body']['query']['bool']['must'][] = array('match' => array($key => $qry));
                $i++;
            }
        }

        // filter
        $filter = array();
        if ($args['extSearch']['extFrom']) {
            $from = $args['extSearch']['extFrom'];
            $filter['gte'] = $this->formatDate($from);
            // will be removed from query later
            $query['extra']['from'] = $from;
        }

        if ($args['extSearch']['extTill']) {
            $till = $args['extSearch']['extTill'];
            $filter['lte'] = $this->formatDate($till);
            // will be removed from query later
            $query['extra']['till'] = $till;
        }

        if (isset($filter['gte']) || isset($filter['lte'])) {
            // "format": "dd/MM/yyyy
            // $filter['format'] = 'dd.MM.yyyy';
            $query['body']['query']['bool']['must'][] = array('range' => array('CREATED_DATE' => $filter));
        }

        // owner id
        $query['body']['query']['bool']['must'][] = array('match' => array('OWNER_ID' => $client->getOwnerId()));

        return $query;
    }

    public function formatDate($date)
    {
        // convert date from dd.mm.yyy to yyyy-dd-mm
        $date = explode(".", $date);
        return $date[2] . '-' . $date[1] . '-' . $date[0];
    }

    public function searchFulltext()
    {
        // perform fulltext search
        $args = $this->request->getArguments();

        $client = $this->clientRepository->findAll()->current();

        // dont return query if keys not existing
        if (!key_exists('search', $args) || !key_exists('query', $args['search'])) {
            return null;
        }

        $searchText = $this->escapeQuery($args['search']['query']);

        // add owner id
        $query['body']['query']['bool']['must']['term']['OWNER_ID'] = $client->getOwnerId(); // qucosa

        $query['body']['query']['bool']['should'][0]['query_string']['query'] = $searchText;
        $query['body']['query']['bool']['should'][1]['has_child']['query']['query_string']['query'] = $searchText;

        $query['body']['query']['bool']['minimum_should_match'] = "1"; // 1

        $query['body']['query']['bool']['should'][1]['has_child']['child_type'] = "datastream"; // 1

        // $query['body']['query']['fields'][0] = "PID";
        // $query['body']['query']['fields'][1] = "_dissemination._content.PUB_TITLE";
        // $query['body']['query']['fields'][2] = "_dissemination._content.PUB_AUTHOR";
        // $query['body']['query']['fields'][3] = "_dissemination._content.PUB_DATE";
        // $query['body']['query']['fields'][4] = "_dissemination._content.PUB_TYPE";

        // extra information
        // dont use it for elastic query
        // will be removed later
        $query['extra']['search'] = $searchText;

        return $query;

    }

    public function searchLatest()
    {
        $client = $this->clientRepository->findAll()->current();

        // get the latest documents /CREATED_DATE
        $query['body']['sort'] = array('CREATED_DATE' => array('order' => 'desc'));

        // add owner id
        $query['body']['query']['bool']['must']['term']['OWNER_ID'] = $client->getOwnerId(); // qucosa

        $query['body']['query']['bool']['should'][0]['query_string']['query'] = '*';
        $query['body']['query']['bool']['should'][1]['has_child']['query']['query_string']['query'] = '*';

        $query['body']['query']['bool']['minimum_should_match'] = "1"; // 1

        $query['body']['query']['bool']['should'][1]['has_child']['child_type'] = "datastream"; // 1

        return $query;
    }

    private function escapeQuery($string)
    {
        $luceneReservedCharacters = preg_quote('+-&|!(){}[]^"~?:\\');
        $string = preg_replace_callback(
            '/([' . $luceneReservedCharacters . '])/',
            function ($matches) {
                return '\\' . $matches[0];
            },
            $string
        );

        return $string;
    }

    /**
     * search
     * @return array elasticsearch query
     */
    public function search()
    {
        // perform search action
        $args = $this->request->getArguments();

        $client = $this->clientRepository->findAll()->current();
        if (empty($args['search']['query'])) {
            // elasticsearch dsl requires an empty object to match all
            $query['body']['query']['match_all'] = new \stdClass();
        } else {
            $query['body']['query']['match']['_all'] = $args['search']['query'];
        }

        return $query;
    }

    /**
     * get results from elastic search
     * @param  array $query elasticsearch search query
     * @return array        results
     */
    public function getResultList($query, $type)
    {
        $elasticSearch = new \EWW\Dpf\Services\ElasticSearch();

        $results = $elasticSearch->search($query, $type);

        return $results;
    }

    public function extendedSearchAction()
    {
        // show extended search template
        $objectIdentifiers = $this->documentRepository->getObjectIdentifiers();

        $args = $this->request->getArguments();
        $elasticSearch = new \EWW\Dpf\Services\ElasticSearch();
        // assign result list from elastic search
        $this->view->assign('searchList', $args['results']);
        $this->view->assign('alreadyImported', $objectIdentifiers);

        // assign form values
        $this->assignExtraFields($args['extra']);

    }

    /**
     * action search
     *
     * @return void
     */
    public function searchAction()
    {

        // perform search action
        $args = $this->request->getArguments();

        // reset session pagination
        if (!empty($GLOBALS['BE_USER'])) {
            $userGlobals = $GLOBALS['BE_USER'];
        } else {
            $userGlobals = $GLOBALS['TSFE']->fe_user;
        }

        if ($userGlobals) {
            $sessionVars = $userGlobals->getSessionData('tx_dpf');
            $sessionVars['resultCount'] = self::RESULT_COUNT;
            $userGlobals->setAndSaveSessionData('tx_dpf', $sessionVars);
        }


        // set sorting
        // $query['body']['sort']['PID']['order'] = 'asc';
        if ($args['extSearch']) {
            // extended search
            $query = $this->extendedSearch();
        } else {
            $query = $this->searchFulltext();
        }

        // save search query
        if ($query) {
            $query['body']['from'] = '0';
            $query['body']['size'] = '' . self::RESULT_COUNT . '';

            if ($userGlobals) {
                $sessionVars = $userGlobals->getSessionData("tx_dpf");
                $sessionVars['query'] = $query;
                $userGlobals->setAndSaveSessionData('tx_dpf', $sessionVars);
            }

            // set type local vs object
            $type = 'object';

            // unset extra information
            $extra = $query['extra'];
            unset($query['extra']);

            $results = $this->getResultList($query, $type);

        } else {
            if ($userGlobals) {
                $sessionVars = $userGlobals->getSessionData('tx_dpf');
                $query = $sessionVars['query'];
            }
        }

        if ($args['extSearch']) {
            // redirect to extended search view
            $this->forward("extendedSearch", null, null, array('results' => $results, 'extra' => $extra));
        } else {
            // redirect to list view
            $this->forward("list", null, null, array('results' => $results, 'extra' => $extra));
        }
    }

    /**
     *
     * @param  string $documentObjectIdentifier
     * @return void
     */
    public function updateIndexAction($documentObjectIdentifier)
    {
        $document = $this->documentRepository->findByObjectIdentifier($documentObjectIdentifier);

        if (is_a($document, '\EWW\Dpf\Domain\Model\Document')) {
            $elasticsearchRepository = $this->objectManager->get('\EWW\Dpf\Services\Transfer\ElasticsearchRepository');
            $elasticsearchMapper = $this->objectManager->get('EWW\Dpf\Helper\ElasticsearchMapper');
            $json = $elasticsearchMapper->getElasticsearchJson($document);
            // send document to index
            $elasticsearchRepository->add($document, $json);
        }

        $this->redirect('search');
    }

    /**
     * action showSearchForm
     *
     * @return void
     */
    public function showSearchFormAction()
    {

    }
}