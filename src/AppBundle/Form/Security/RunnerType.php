<?php

namespace AppBundle\Form\Security;

use AppBundle\Entity\Runner;
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RunnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UserType::class,
                array(
                  'constraints' => new Valid(),
                ))
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
