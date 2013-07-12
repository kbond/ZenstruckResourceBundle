<?php

namespace Zenstruck\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ControllerUtil extends Controller
{
    const FLASH_SUCCESS = 'success';
    const FLASH_ERROR = 'danger';

    public function addFlash($message, $parameters = array(), $type = self::FLASH_SUCCESS)
    {
        $this->get('session')->getFlashBag()->add($type, $this->get('translator')->trans($message, $parameters));
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param string $entityName
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($entityName)
    {
        return $this->getDoctrine()->getRepository($entityName);
    }

    /**
     * @param object $entity
     * @param bool   $flush
     */
    public function persist($entity, $flush = true)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);

        if ($flush) {
            $em->flush();
        }
    }

    /**
     * @param object $entity
     * @param bool   $flush
     */
    public function delete($entity, $flush = true)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);

        if ($flush) {
            $em->flush();
        }
    }
}