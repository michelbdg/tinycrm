<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Interaction;
use App\Entity\Offre;
use App\Entity\Transaction;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('pages/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/logo.png" height="40">');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');
        yield MenuItem::section('Gestion des utilisateurs');
        yield MenuItem::linkToCrud('Nos clients', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Suivi', 'fa fa-calendar', Interaction::class);
        yield MenuItem::linkToCrud('Nos offres', 'fa fa-cubes', Offre::class);
        yield MenuItem::linkToCrud('Transaction', 'fa fa-euro', Transaction::class);
        yield MenuItem::section('Gestion des articles');
        yield MenuItem::linkToCrud('Les articles', 'fa fa-newspaper', Article::class);
        yield MenuItem::section('Gestion des paramètres');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
