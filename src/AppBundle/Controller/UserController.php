<?php

namespace AppBundle\Controller;

use AppBundle\Exception\ClmAccountRepositoryException;
use AppBundle\Form\UserImportFromFileType;
use AppBundle\Service\ClmXmlDeserializer;
use AppBundle\Service\UserLootManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Config\Definition\Exception\DuplicateKeyException;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController
{

    /**
     * @var UserLootManager
     */
    private $userLootManager;
    private $router;
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $templating;
    private $xmlDeserializer;

    /**
     * UserController constructor.
     * @param EngineInterface $templating
     * @param UrlGeneratorInterface $router
     * @param FormFactory $formFactory
     * @param UserLootManager $userLootManager
     * @param ClmXmlDeserializer $xmlDeserializer
     */
    public function __construct(
        EngineInterface $templating,
        UrlGeneratorInterface $router,
        FormFactory $formFactory,
        UserLootManager $userLootManager,
        ClmXmlDeserializer $xmlDeserializer)
    {
       $this->userLootManager = $userLootManager;
       $this->templating = $templating;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->xmlDeserializer = $xmlDeserializer;
    }

    /**
     * @return Response
     */
    public function listAction()
    {
        $users =$this->userLootManager->getAllAccounts();

        return $this->templating->renderResponse(
                'User/listUsers.html.twig',
                ['users' => $users]
            );
    }
    
    public function importAction(Request $request)
    {
        $xmlFile = null;
        $form = $this->formFactory->create(UserImportFromFileType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $xmlFile = $form['XmlFile']->getData();
            dump($xmlFile->getClientOriginalName());
            
            try {
                $accounts = $this->xmlDeserializer->deserializeAccounts($xmlFile);
                foreach ($accounts as $account) {
                    $request->getSession()->getFlashBag()
                        ->add(
                            'info',
                            sprintf(
                                'Spieler %s wurde erfolgreich angelegt.',
                                $account->getAccountName())
                        );

                }
            } catch (ClmAccountRepositoryException $e){
//              throw new DuplicateKeyException('Es gab Duplikate beim Import', null , $e );
                $request->getSession()->getFlashBag()
                    ->add('info', 'Duplikate beim Import übersprungen.');
            }

            return new RedirectResponse(
                $this->router->generate('user_list')
            );
        }


        return $this->templating->renderResponse(
            'User/importUsers.html.twig',
            array('form' => $form->createView(),)
        );

    }
}
























