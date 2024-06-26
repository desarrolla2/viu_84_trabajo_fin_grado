<?php
/*
 * This file is part of the Data Miner.
 *
 * Daniel González <daniel@devtia.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Infrastructure\Reader\Service\ChatGPT\Entity\Serializer\Normalizer;

use App\Domain\Generator\Service\PreProcessorInterface;
use App\Domain\Reader\Entity\Person;
use App\Domain\Reader\Entity\Vehicle;
use App\Domain\Reader\Entity\VehicleSaleAndPurchaseAgreement;
use DateTime;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AutoconfigureTag('serializer.normalizer')]
readonly class VehicleSaleAndPurchaseAgreementNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(private VehicleNormalizer $vehicleNormalizer, private PersonNormalizer $personNormalizer)
    {
    }

    /** @param string[] $context */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): VehicleSaleAndPurchaseAgreement
    {
        $date = $data['date'] ? DateTime::createFromFormat('Y-m-d', $data['date']) : '';
        $vehicle = $data['vehicle'] ? $this->vehicleNormalizer->denormalize($data['vehicle'], Vehicle::class) : new Vehicle('', '', '');
        $sellers = $data['sellers'] ? array_map(function (array $item): Person {
            return $this->personNormalizer->denormalize($item, Person::class);
        }, $data['sellers']) : [];
        $buyers = $data['buyers'] ? array_map(function (array $item): Person {
            return $this->personNormalizer->denormalize($item, Person::class);
        }, $data['buyers']) : [];

        $price = (float) $data['price'] ?? 0.0;

        return new VehicleSaleAndPurchaseAgreement($date, $vehicle, $sellers, $buyers, $price);
    }


    /** @param VehicleSaleAndPurchaseAgreement $object */
    /** @param string[] $context */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        return [
            'date' => $object->date()->format('Y-m-d'),
            'vehicle' => $this->vehicleNormalizer->normalize($object->vehicle()),
            'sellers' => array_map(function (Person $person): array {
                return $this->personNormalizer->normalize($person);
            }, $object->sellers()),
            'buyers' => array_map(function (Person $person): array {
                return $this->personNormalizer->normalize($person);
            }, $object->buyers()),
            'price' => $object->price(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof VehicleSaleAndPurchaseAgreement;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [VehicleSaleAndPurchaseAgreement::class => true];
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === VehicleSaleAndPurchaseAgreement::class;
    }
}
