<?php

namespace AppBundle\Form;

use AppBundle\Entity\RaceCategory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRaceType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('startTime',DateTimeType::class, array(
                'years' => range(2017, 2027),
            ))
            ->add('distance')
            ->add('maxRunners')
            ->add('categories', EntityType::class, array(
                'class' => RaceCategory::class,
                'multiple' => true,
                'expanded' => false,
                'attr' => array(
                    'class' => '',
                ),
                'required' => false,
            ))
            ->add('route', HiddenType::class, array(
                'mapped' => false,
            ))
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();

                if(array_key_exists('categories', $data)){
                    $categories = [];
                    foreach($data['categories'] as $category){
                        $tmpCat = $this->em->getRepository(RaceCategory::class)->findOneById($category);
                        if($tmpCat){
                            $categories[] = $tmpCat->getId();
                        }else{
                            $newCat = new RaceCategory();
                            $newCat->setName($category);
                            $this->em->persist($newCat);
                            $this->em->flush();
                            $categories[] = $newCat->getId();
                        }
                    }

                    $data['categories'] = $categories;
                    $event->setData($data);
                }
            }
        );

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Race'
        ));
    }

}
