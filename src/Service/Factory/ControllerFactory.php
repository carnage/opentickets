<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace ConferenceTools\Tickets\Service\Factory;

use Carnage\Cqrs\Command\CommandBusInterface;
use ConferenceTools\Tickets\Domain\Service\Availability\DiscountCodeAvailability;
use ConferenceTools\Tickets\Domain\Service\Availability\TicketAvailability;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfrStripe\Client\StripeClient;

class ControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();

        return new $requestedName(
            $serviceLocator->get(CommandBusInterface::class),
            $serviceLocator->get('doctrine.entitymanager.orm_default'),
            $serviceLocator->get(StripeClient::class),
            $serviceLocator->get(Configuration::class),
            $serviceLocator->get(TicketAvailability::class),
            $serviceLocator->get(DiscountCodeAvailability::class),
            $serviceLocator->get('FormElementManager')
        );
    }
}
