<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditContestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('www')
            ->add('city')
            ->add('postCode')
            ->add('street')
            ->add('notice')
            ->add('country', CountryType::class)
            ->add('startTime',DateTimeType::class, array(
                'years' => range(2017, 2027),
            ))
            ->add('endTime',DateTimeType::class, array(
                'years' => range(2017, 2027),
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contest'
        ));
    }

}
