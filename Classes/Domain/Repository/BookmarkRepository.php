<?php
namespace EWW\Dpf\Domain\Repository;

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

use \EWW\Dpf\Domain\Model\Bookmark;

/**
 * The repository for Bookmarks
 */
class BookmarkRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * /**
     * Finds all bookmarks filtered by owner uid.
     *
     * @param int $ownerUid
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAllByOwnerUid($ownerUid = NULL)
    {
        $query = $this->createQuery();
        $constraintsAnd = array();

        if ($ownerUid) {
            $constraintsAnd[] = $query->equals('owner', $ownerUid);
        }

        if (!empty($constraintsAnd)) {
            $query->matching($query->logicalAnd($constraintsAnd));
        }

        return $query->execute();
    }

}
