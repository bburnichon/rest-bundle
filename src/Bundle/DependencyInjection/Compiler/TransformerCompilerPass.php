<?php

namespace Alchemy\RestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransformerCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('alchemy_rest.transformer_registry')) {
            return;
        }

        $transformerRegistry = $container->findDefinition('alchemy_rest.transformer_registry');
        $transformerTags = $container->findTaggedServiceIds('alchemy_rest.transformer');

        foreach ($transformerTags as $id => $tags) {
            foreach ($tags as $tag) {
                $transformerRegistry->addMethodCall('setTransformer', array($tag['alias'], new Reference($id)));
            }
        }
    }
}
