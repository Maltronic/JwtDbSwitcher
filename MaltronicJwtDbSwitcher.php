<?php
namespace Maltronic\JwtDbSwitcher;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * MaltronicJwtDbSwitcherBundle
 *
 * @author Maltronic <maltronic.email@gmail.com>
 */
class MaltronicJwtDbSwitcherBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}