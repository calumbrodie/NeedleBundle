<?php

namespace Markup\NeedleBundle;

use Markup\NeedleBundle\DependencyInjection\Compiler\AddCorporaPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddFacetValueCanonicalizersPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddIndexSchedulingEventsPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddSolariumPluginsPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\AddSuggestersPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\RegisterSubjectDataMappersPass;
use Markup\NeedleBundle\DependencyInjection\Compiler\RegisterSearchInterceptMappersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MarkupNeedleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddCorporaPass());
        $container->addCompilerPass(new AddFacetValueCanonicalizersPass());
        $container->addCompilerPass(new AddIndexSchedulingEventsPass());
        $container->addCompilerPass(new RegisterSubjectDataMappersPass());
        $container->addCompilerPass(new RegisterSearchInterceptMappersPass());
        $container->addCompilerPass(new AddSolariumPluginsPass());
        $container->addCompilerPass(new AddSuggestersPass());
    }
}
