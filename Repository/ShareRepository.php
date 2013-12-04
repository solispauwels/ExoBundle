<?php

/**
 * ExoOnLine
 * Copyright or © or Copr. Université Jean Monnet (France), 2012
 * dsi.dev@univ-st-etienne.fr
 *
 * This software is a computer program whose purpose is to [describe
 * functionalities and technical features of your software].
 *
 * This software is governed by the CeCILL license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
*/

namespace UJM\ExoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ShareRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShareRepository extends EntityRepository
{
    public function getControlSharedQuestion($user, $question)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->join('s.user', 'u')
            ->where($qb->expr()->in('s.question', $question))
            ->andWhere($qb->expr()->in('u.id', $user));

        return $qb->getQuery()->getResult();
    }

    public function getUserInteractionSharedImport($exoId, $uid, $em)
    {

        $questions = array();

        $dql = 'SELECT eq FROM UJM\ExoBundle\Entity\ExerciseQuestion eq WHERE eq.exercise=' . $exoId
            . ' ORDER BY eq.ordre';

        $query = $em->createQuery($dql);
        $eqs = $query->getResult();

        foreach ($eqs as $eq) {
            $questions[] = $eq->getQuestion()->getId();
        }

        $qb = $this->createQueryBuilder('s');

        $qb->join('s.question', 'q')
           ->join('s.user', 'u')
           ->where($qb->expr()->in('u.id', $uid));
        if (count($questions) > 0) {
             $qb->andWhere('q.id not in ('.implode(',', $questions).')');
        }

        return $qb->getQuery()->getResult();
    }

    public function findByCategoryShared($userId, $whatToFind)
    {
        $dql = 'SELECT s FROM UJM\ExoBundle\Entity\Share s JOIN s.question q JOIN q.category c
            WHERE c.value LIKE :search
            AND s.user = '.$userId.'
        ';

        $query = $this->_em->createQuery($dql)
            ->setParameter('search', "%{$whatToFind}%");

        return $query->getResult();
    }

    public function findByTitleShared($userId, $whatToFind)
    {
        $dql = 'SELECT s FROM UJM\ExoBundle\Entity\Share s JOIN s.question q
            WHERE q.title LIKE :search
            AND s.user = '.$userId.'
        ';

        $query = $this->_em->createQuery($dql)
            ->setParameter('search', "%{$whatToFind}%");

        return $query->getResult();
    }

    public function findByTypeShared($userId, $whatToFind)
    {
        $dql = 'SELECT s FROM UJM\ExoBundle\Entity\Share s, UJM\ExoBundle\Entity\Interaction i
                WHERE s.question = i.question
                AND s.user = '.$userId.'
                AND i.type LIKE :search
        ';

        $query = $this->_em->createQuery($dql)
            ->setParameter('search', "%{$whatToFind}%");

        return $query->getResult();
    }

    public function findByContainShared($userId, $whatToFind)
    {
         $dql = 'SELECT s FROM UJM\ExoBundle\Entity\Share s, UJM\ExoBundle\Entity\Interaction i
                WHERE s.question = i.question
                AND s.user = '.$userId.'
                AND i.invite LIKE :search
        ';

        $query = $this->_em->createQuery($dql)
            ->setParameter('search', "%{$whatToFind}%");

        return $query->getResult();
    }

    public function findByAllShared($userId, $whatToFind)
    {
        $dql = 'SELECT s FROM UJM\ExoBundle\Entity\Share s, UJM\ExoBundle\Entity\Interaction i,
                UJM\ExoBundle\Entity\Question q, UJM\ExoBundle\Entity\Category c
                WHERE s.question = i.question AND i.question = q AND q.category = c
                AND s.user = '.$userId.'
                AND (i.invite LIKE :search OR i.type LIKE :search OR c.value LIKE :search OR q.title LIKE :search)
        ';

        $query = $this->_em->createQuery($dql)
            ->setParameter('search', "%{$whatToFind}%");

        return $query->getResult();
    }
}