<?php

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        ReservationsRepository::class => \DI\autowire(ReservationsRepository::class)
    ]);
};
