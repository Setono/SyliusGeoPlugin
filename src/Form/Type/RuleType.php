<?php

declare(strict_types=1);

namespace Setono\SyliusGeoPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class RuleType extends AbstractResourceType
{
    private RepositoryInterface $countryRepository;

    /**
     * @param list<string> $validationGroups
     */
    public function __construct(RepositoryInterface $countryRepository, string $dataClass, array $validationGroups = [])
    {
        parent::__construct($dataClass, $validationGroups);

        $this->countryRepository = $countryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        $preferredCountryCodes = array_map(static fn (CountryInterface $country) => $country->getCode(), $this->countryRepository->findAll());

        $builder
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_geo.form.rule.name',
                'attr' => [
                    'placeholder' => 'setono_sylius_geo.form.rule.name_placeholder',
                ],
            ])
            ->add('excludeBots', CheckboxType::class, [
                'label' => 'setono_sylius_geo.form.rule.exclude_bots',
                'required' => false,
            ])
            ->add('excludedIps', TextareaType::class, [
                'label' => 'setono_sylius_geo.form.rule.excluded_ips',
                'attr' => [
                    'placeholder' => 'setono_sylius_geo.form.rule.excluded_ips_placeholder',
                ],
                'required' => false,
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.ui.enabled',
                'required' => false,
            ])
            ->add('sourceChannel', ChannelChoiceType::class, [
                'label' => 'setono_sylius_geo.form.rule.source_channel',
                'placeholder' => 'sylius.ui.select',
            ])
            ->add('countryCodes', CountryType::class, [
                'preferred_choices' => $preferredCountryCodes,
                'label' => 'setono_sylius_geo.form.rule.country_codes',
                'multiple' => true,
            ])
            ->add('targetChannel', ChannelChoiceType::class, [
                'label' => 'setono_sylius_geo.form.rule.target_channel',
                'placeholder' => 'sylius.ui.select',
            ])
        ;

        $builder
            ->get('excludedIps')
            ->addModelTransformer(new CallbackTransformer(
                /** @psalm-suppress MixedArgumentTypeCoercion */
                fn (array $excludedIps): string => implode("\n", $excludedIps),
                function (?string $excludedIps): array {
                    if (null === $excludedIps || '' === $excludedIps) {
                        return [];
                    }

                    return array_map(static fn (string $ip) => trim($ip), preg_split('/[,\n]+/', $excludedIps));
                },
            ))
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_geo_rule';
    }
}
