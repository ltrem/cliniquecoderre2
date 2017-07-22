<?php
namespace AppBundle\Controller\EasyAdmin;

use AppBundle\Entity\Client;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Coordinate;
use AppBundle\Entity\Employe;
use AppBundle\Entity\Event;
use AppBundle\Entity\Schedule;
use AppBundle\Entity\User;
use AppBundle\Event\ClientCreatedEvent;
use AppBundle\Form\ScheduleType;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class EventController extends BaseAdminController
{

    protected function newAction()
    {
        $entity = new Event();

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', array($entity, $fields));

        $newForm->handleRequest($this->request);

        if ($newForm->isSubmitted() && $newForm->isValid()) {

            // Get event from Form data
            $event = $newForm->getData();

            $startTime = $event->getStartTime();
            if ($startTime) {
                // Round to nearest lowest hour
                $startTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
                $event->setStartTime($startTime);

                // Add 1 hour to endTime
                $endTime = new \DateTime($startTime->format("Y-m-d H:00:00"));
                $event->setEndTime($endTime->modify("+1 hour"));
            }

            $this->dispatch(EasyAdminEvents::PRE_PERSIST, array('entity' => $entity));

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->getConnection()->beginTransaction();
            $em->flush();

            // Send a notice to the employe to notify of the user creation
            //$this->get('event_dispatcher')->dispatch(ClientCreatedEvent::NAME, new ClientCreatedEvent($user));

            $this->addFlash('notice', 'Welcome '. $user->getEmail());

            $em->getConnection()->commit();

            $this->dispatch(EasyAdminEvents::POST_PERSIST, array('entity' => $entity));

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, array(
            'entity_fields' => $fields,
            'form' => $newForm,
            'entity' => $entity,
        ));

        return $this->render($this->entity['templates']['new'], array(
            'form' => $newForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
        ));
    }

    protected function editAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_EDIT);

        $id = $this->request->query->get('id');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $entity = $easyadmin['item'];

        if ($this->request->isXmlHttpRequest() && $property = $this->request->query->get('property')) {
            $newValue = 'true' === mb_strtolower($this->request->query->get('newValue'));
            $fieldsMetadata = $this->entity['list']['fields'];

            if (!isset($fieldsMetadata[$property]) || 'toggle' !== $fieldsMetadata[$property]['dataType']) {
                throw new \RuntimeException(sprintf('The type of the "%s" property is not "toggle".', $property));
            }

            $this->updateEntityProperty($entity, $property, $newValue);

            return new Response((string) $newValue);
        }

        $fields = $this->entity['edit']['fields'];

        $editForm = $this->executeDynamicMethod('create<EntityName>EditForm', array($entity, $fields));
        $deleteForm = $this->createDeleteForm($this->entity['name'], $id);

        $editForm->handleRequest($this->request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatch(EasyAdminEvents::PRE_UPDATE, array('entity' => $entity));

            $this->executeDynamicMethod('preUpdate<EntityName>Entity', array($entity));
            $this->em->flush();

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);

        return $this->render($this->entity['templates']['edit'], array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function createEventEntityFormBuilder(Event $entity, $view)
    {
        $formBuilder = parent::createEntityFormBuilder($entity, $view);

        return $formBuilder;
    }

    protected function executeDynamicMethod($methodNamePattern, array $arguments = array())
    {
        $methodName = str_replace('<EntityName>', $this->entity['name'], $methodNamePattern);

        if (!is_callable(array($this, $methodName))) {
            $methodName = str_replace('<EntityName>', '', $methodNamePattern);
        }

        return call_user_func_array(array($this, $methodName), $arguments);
    }
}