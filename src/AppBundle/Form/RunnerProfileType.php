<?php

namespace AppBundle\Form;

use AppBundle\Entity\Runner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RunnerProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('surname')
            ->add('gender', ChoiceType::class, array(
                'choices' => array(
                    'Kobieta' => 1,
                    'Mężczyzna' => 0,
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('birthDate', DateType::class, array(
                'years' => range(1900, 2017),
            ))
            ->add('club')
            ->add('nationality', CountryType::class)
            ->add('phone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Runner::class,
        ));
    }
}
