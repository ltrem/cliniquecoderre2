<?php

namespace AppBundle\Controller\Admin;


use AppBundle\Entity\Communication;
use AppBundle\Entity\Employe;
use AppBundle\Entity\Event;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Coordinate;
use AppBundle\Entity\Schedule;
use AppBundle\Entity\User;
use AppBundle\Form\EmployeProfileForm;
use AppBundle\Form\EmployeType;
use AppBundle\Form\SearchEmployeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin controller.
 *
 * @Route("admin/employe")
 */
class EmployeController extends Controller
{
    /**
     * Lists all employe entities.
     *
     * @Route("/", name="admin_employe")
     * @Method("GET")
     */
    public function employeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Prepare Search Filter
        {
            $search_employe_form = $this->createForm(SearchEmployeType::class);
            $search_employe_form->handleRequest($request);
            $filter = $search_employe_form->getData();
        }

        // Filter query
        {
            $queryBuilder = $em->getRepository('AppBundle:Employe')->createQueryBuilder('c')
                ->leftJoin('c.contacts', 'contacts');
            if ($filter['search_name']) {
                $queryBuilder
                    ->where('c.firstname LIKE :filter_name OR c.lastname LIKE :filter_name')
                    ->setParameter('filter_name', '%' . $filter['search_name'] . '%');
            }
            if ($filter['search_phone']) {
                $queryBuilder
                    ->where('contacts.phoneCell LIKE :filter_phoneCell')
                    ->setParameter('filter_phoneCell', '%' .$filter['search_phone'] . '%');
            }
            $query = $queryBuilder->getQuery();
        }

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15),
            array(
                'wrap-queries' => true
            )

        );

        return $this->render('admin/employe/index.html.twig', array(
            'employes' => $result,
            'search_employe_form' => $search_employe_form->createView()
        ));
    }

    /**
     * Create new employe.
     *
     * @Route("/new", name="admin_employe_new")
     */
    public function employeNewAction(Request $request)
    {
        // Add Client and default Coordinate to User
        $user = new User();
        $employe = new Employe();
        $coordinate = new Coordinate();
        $contact = new Contact();

        $coordinate->setIsPrimary(true);
        $employe->addCoordinate($coordinate);
        $employe->addContact($contact);
        $user->setEmploye($employe);

        $form = $this->createForm(EmployeProfileForm::class, $user);
        $form->remove('plainPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Assign ROLE_CLIENT
            $roles = array('ROLE_CLIENT');
            $user->setRoles($roles);

            // Assign User to Client
            $employe = $user->getEmploye();
            $employe->setUser($user);


            // Generate Token
            $token = rtrim(strtr(base64_encode(openssl_random_pseudo_bytes(32)), '+/', '-_'), '=');

            // Get User and assign password and token
            $user = $form->getData();
            $user->setPlainPassword(random_bytes(10));
            $user->setResetPasswordToken($token);
            $user->setResetPasswordDate(new \DateTime());

            $password_reset_template= $this->renderView('user/password_reset.html.twig', array(
                'confirmationUrl' =>  $token
            ));

            // Send email to new Client
            // Create Communication
            $communication = new Communication();
            $communication->setType('email');
            $communication->setDateSent(new \DateTime('now'));
            $communication->setTitle('Création de profil employé sur Cliniquecoderre.com');
            $communication->setContent($password_reset_template);
            $communication->setEmail($user->getUsername());

            $employe->addCommunication($communication);

            // Communication sent
            // Send email
            $message = \Swift_Message::newInstance()
                ->setFrom('info@cliniquecoderre.com')
                ->setTo($communication->getEmail())
                ->setSubject(
                    $communication->getTitle()
                )
                ->setBody(
                    $communication->getContent(),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Welcome '.$user->getEmail());

            return $this->redirectToRoute('admin_employe_edit', array('id' => $employe->getId()));

        }

        return $this->render('admin/employe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing employe entity.
     *
     * @Route("/{id}", name="admin_employe_edit")
     * @Method({"GET", "POST"})
     */
    public function employeEditAction(Request $request, Employe $employe)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this sections.');
        }

        if ($user) {
            // Upcoming events
            $upcomingEvents = $em->getRepository('AppBundle:Event')->findUpcomingEvents($user->getClient(), $user->getEmploye());
        }

        // Filter query
        {
            $queryBuilder = $em->getRepository('AppBundle:Schedule')->createQueryBuilder('s');
            $schedule_query = $queryBuilder->getQuery();
            dump($schedule_query);
        }

        /**
         * @var $paginator \Knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $schedule_result = $paginator->paginate(
            $schedule_query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 15),
            array(
                'wrap-queries' => true
            )
        );

        $editForm = $this->createForm(EmployeType::class, $employe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_employe_edit', array('id' => $employe->getId()));
        }

        return $this->render('admin/employe/edit.html.twig', array(
            'employe' => $employe,
            'client'  => '',
            'upcomingEvents' => $upcomingEvents,
            'schedules' => $schedule_result,
            'form' => $editForm->createView(),
        ));
    }
}
