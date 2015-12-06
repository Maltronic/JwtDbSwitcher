<?php
namespace Maltronic\Bundle\JwtDbSwitcher;

use Maltronic\Bundle\DependencyInjection\Security\Factory\ConfigSwitcherFactory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * MaltronicJwtDbSwitcherBundle
 *
 * @author Malcolm Davis <maltronic.email@gmail.com>
 */
class MaltronicJwtDbSwitcherBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SecurityExtension $extension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ConfigSwitcherFactory());
    }
}