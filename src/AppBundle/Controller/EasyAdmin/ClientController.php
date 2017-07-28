<?php
namespace AppBundle\Controller\EasyAdmin;

use AppBundle\Entity\Client;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Coordinate;
use AppBundle\Entity\User;
use AppBundle\Event\ClientCreatedEvent;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use JavierEguiluz\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class ClientController extends BaseAdminController
{

    /**
     * This method overrides the default query builder used to search for this
     * entity. This allows to make a more complex search joining related entities.
     */
    protected function createSearchQueryBuilder($entityClass, $searchQuery, array $searchableFields, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        /* @var EntityManager */
        $em = $this->getDoctrine()->getManagerForClass($this->entity['class']);
        /* @var DoctrineQueryBuilder */
        $queryBuilder = $em->createQueryBuilder()
            ->select('entity')
            ->from($this->entity['class'], 'entity')
            ->join('entity.contacts', 'contacts')
            ->join('entity.user', 'user')
            ->orWhere('LOWER(contacts.phoneCell) LIKE :query')
            ->orWhere('LOWER(user.email) LIKE :query')
            ->orWhere('LOWER(entity.firstname) LIKE :query')
            ->orWhere('LOWER(entity.lastname) LIKE :query')
            ->orWhere('LOWER(entity.gender) LIKE :query')
            ->setParameter('query', '%'.strtolower($searchQuery).'%')
        ;

        if (!empty($dqlFilter)) {
            $queryBuilder->andWhere($dqlFilter);
        }

        if (null !== $sortField) {
            $queryBuilder->orderBy('entity.'.$sortField, $sortDirection ?: 'DESC');
        }

        return $queryBuilder;
    }

    protected function newAction()
    {
        $entity = new Client();
        $user = new User();
        $coordinate = new Coordinate();
        $contact = new Contact();
        $coordinate->setIsPrimary(true);
        $entity->addCoordinate($coordinate);
        $entity->addContact($contact);

        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', array($entity, $fields));

        $newForm->handleRequest($this->request);

        if ($newForm->isSubmitted() && $newForm->isValid()) {

            // Get client from Form data
            $client = $newForm->getData();

            $user->setClient($client);

            $this->dispatch(EasyAdminEvents::PRE_PERSIST, array('entity' => $entity));

            // Assign ROLE_CLIENT
            $roles = array('ROLE_CLIENT');
            $user->setRoles($roles);

            // Assign User to Client
            $client = $user->getClient();
            $client->setUser($user);

            // Generate Token
            $token = rtrim(strtr(base64_encode(openssl_random_pseudo_bytes(32)), '+/', '-_'), '=');

            // Get User and assign password and token
            $user->setEmail($newForm->get('email')->getData());
            $user->setPlainPassword(random_bytes(10));
            $user->setResetPasswordToken($token);
            $user->setResetPasswordDate(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->getConnection()->beginTransaction();
            $em->flush();

            // Send a notice to every client that are waiting for an appointment
            $this->get('event_dispatcher')->dispatch(ClientCreatedEvent::NAME, new ClientCreatedEvent($user));

            $this->addFlash('notice', 'Welcome '.$user->getEmail());

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

            $this->addFlash('success', 'Welcome '.$entity->getEmail());

            $this->dispatch(EasyAdminEvents::POST_UPDATE, array('entity' => $entity));

            $refererUrl = $this->request->query->get('referer', '');

            return !empty($refererUrl)
                ? $this->redirect(urldecode($refererUrl))
                : $this->redirect($this->generateUrl('easyadmin', array('action' => 'list', 'entity' => $this->entity['name'])));
        }

        $this->dispatch(EasyAdminEvents::POST_EDIT);


        // TODO: Rethink this process... the entity CLient should already have these variable setted up.
        if ($user = $entity->getUser()) {
            // Event Availability Notification
            $appointmentAvailabilityNotification = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:AppointmentAvailabilityNotification')->findAppointmentAvailabilityFromClient($user->getClient());
            $notifToken = '';
            $eventOffered = '';
            if (null !== $appointmentAvailabilityNotification) {
                $notifToken = $appointmentAvailabilityNotification->getToken();
                $eventOffered = $appointmentAvailabilityNotification->getEventFreed();
            }
            // Past events
            $pastEvents = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Event')->findPastEvents($user->getClient(), 7);
            // Upcoming events
            $upcomingEvents = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Event')->findUpcomingEvents($user->getClient());
            // Upcoming and not cancelled events
            $cancelledUpcomingEvents = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Event')->findCancelledUpcomingEvents($user->getClient());
            // Find upcoming 7 next days
            $events7days = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Event')->findXDaysUpcomingEvents(7);
        }

        return $this->render($this->entity['templates']['edit'], array(
            'form' => $editForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'eventOffered' => $eventOffered,
            'notifToken' => $notifToken,
            'upcomingEvents' => $upcomingEvents,
            'cancelledUpcomingEvents' => $cancelledUpcomingEvents,
            'events7days' => $events7days,
            'pastEvents' => $pastEvents,
        ));
    }

    public function createClientEntityFormBuilder(Client $entity, $view)
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