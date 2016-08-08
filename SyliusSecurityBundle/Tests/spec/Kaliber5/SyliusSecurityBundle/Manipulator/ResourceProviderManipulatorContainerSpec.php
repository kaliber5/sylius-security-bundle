<?php

namespace spec\Kaliber5\SyliusSecurityBundle\Manipulator;

use Kaliber5\SyliusSecurityBundle\Manipulator\ResourceProviderManipulatorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ResourceProviderManipulatorContainerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Kaliber5\SyliusSecurityBundle\Manipulator\ResourceProviderManipulatorContainer');
    }

    function it_collects_manipulators(ResourceProviderManipulatorInterface $provider1, ResourceProviderManipulatorInterface $provider2)
    {
        $this->addManipulator($provider1, 'app.user');
        $this->addManipulator($provider2, 'something');
    }

    function it_throws_an_exception_on_invalid_namespace(ResourceProviderManipulatorInterface $provider1)
    {
        $this->shouldThrow('\InvalidArgumentException')
            ->duringAddManipulator($provider1, null);
    }

    function it_processes_the_manipulators_for_namespace(
        ResourceProviderManipulatorInterface $provider1,
        ResourceProviderManipulatorInterface $provider2,
        ResourceProviderManipulatorInterface $provider3,
        RequestConfiguration $configuration,
        RepositoryInterface $repository,
        Metadata $metadata
    ) {
    
        $configuration->getMetadata()->willReturn($metadata);
        $configuration->getMetadata()->shouldBeCalled();
        $metadata->getAlias()->willReturn('app.user');
        $metadata->getAlias()->shouldBeCalled();
        $this->addManipulator($provider1, 'app.user');
        $this->addManipulator($provider2, 'something');
        $this->addManipulator($provider3, 'app.user');

        $provider1->process($configuration, $repository)->shouldBeCalled();
        $provider2->process($configuration, $repository)->shouldNotBeCalled();
        $provider3->process($configuration, $repository)->shouldBeCalled();

        $this->process($configuration, $repository);
    }
}
