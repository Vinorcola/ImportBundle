<?php

namespace Vinorcola\ImportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MappingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['mapping'] as $mappingColumn) {
            $builder->add($mappingColumn, ChoiceType::class, [
                'label'                     => $options['labelPrefix'] . $mappingColumn,
                'choices'                   => $options['headers'],
                'choice_label'              => function ($value) {
                    return $value;
                },
                'choice_translation_domain' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('labelPrefix');
        $resolver->setAllowedTypes('labelPrefix', 'string');
        $resolver->setRequired('mapping');
        $resolver->setAllowedTypes('mapping', 'string[]');
        $resolver->setRequired('headers');
        $resolver->setAllowedTypes('headers', 'string[]');
    }
}
