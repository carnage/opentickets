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

namespace ConferenceTools\Tickets\Domain\ValueObject\DiscountType;

use ConferenceTools\Tickets\Domain\Service\Basket\ValidateBasket;
use ConferenceTools\Tickets\Domain\Service\Configuration;
use ConferenceTools\Tickets\Domain\ValueObject\Basket;
use ConferenceTools\Tickets\Domain\ValueObject\Money;
use ConferenceTools\Tickets\Domain\ValueObject\Price;
use ConferenceTools\Tickets\Domain\ValueObject\TicketReservation;

/**
 * @internal
 * @coversNothing
 */
final class FixedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Configuration
     */
    private $config;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->config = Configuration::fromArray([
            'tickets' => [
                'early' => ['name' => 'Early Bird', 'cost' => 5000, 'available' => 75],
                'std' => ['name' => 'Standard', 'cost' => 10000, 'available' => 150],
                'free' => ['name' => 'Free', 'cost' => 0, 'available' => 0],
            ],
            'financial' => [
                'taxRate' => 10,
                'currency' => 'GBP',
                'displayTax' => true,
            ],
        ]);
    }

    /**
     * @dataProvider provideTestApply
     *
     * @param $basket
     * @param Price $expected
     */
    public function testApply(Basket $basket, Price $discount, Price $expected)
    {
        $sut = new Fixed($discount);
        $this->assertTrue($expected->equals($sut->apply($basket)), 'Price didn\'t match expected value');
    }

    public function provideTestApply()
    {
        $validator = new ValidateBasket();

        return [
            [
                Basket::fromReservations(
                    $this->config,
                    $validator,
                    new TicketReservation($this->config->getTicketType('std'), 'abc'),
                    new TicketReservation($this->config->getTicketType('std'), 'abc')
                ),
                Price::fromNetCost(new Money(1000, $this->config->getCurrency()), $this->config->getTaxRate()),
                Price::fromNetCost(new Money(1000, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
            [
                Basket::fromReservations(
                    $this->config,
                    $validator,
                    new TicketReservation($this->config->getTicketType('std'), 'abc')
                ),
                Price::fromNetCost(new Money(1000, $this->config->getCurrency()), $this->config->getTaxRate()),
                Price::fromNetCost(new Money(1000, $this->config->getCurrency()), $this->config->getTaxRate()),
            ],
        ];
    }
}
