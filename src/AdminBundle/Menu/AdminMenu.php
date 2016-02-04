<?php
namespace AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Admingenerator\GeneratorBundle\Menu\AdmingeneratorMenuBuilder;

/**
 *
 * @author Lionel Bouzonville
 */
class AdminMenu extends AdmingeneratorMenuBuilder
{
    /**
     * @see Admingenerator\GeneratorBundle\Menu\AdmingeneratorMenuBuilder
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack, $dashboardRoute)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
        $this->dashboardRoute = $dashboardRoute;
    }

    /**
     *
     */
    public function sidebarMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'sidebar-menu'));

        if ($dashboardRoute = $this->dashboardRoute) {
            $this
                ->addLinkRoute($menu, 'Accueil', $dashboardRoute)
                ->setExtra('icon', 'fa fa-dashboard');
        }

        // Content
        $content = $this->addDropdown($menu, 'Contenu');
        $this->addLinkRoute(
            $content,
            'Page',
            'AdminBundle_CmsContent_list'
        );
        $this->addLinkRoute(
            $content,
            'Catégorie',
            'AdminBundle_CmsCategory_list'
        );

        return $menu;
    }
}
