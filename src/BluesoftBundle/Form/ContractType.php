<?php

namespace BluesoftBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('active')
                ->add('amount')
                ->add('amountPeriod')
                ->add('amountType')
                ->add('authorizationPercent')
                ->add('fromDate')
                ->add('orderNumber')
                ->add('request')
                ->add('toDate')
                ->add('system', EntityType::class, [
                      'class' => 'BluesoftBundle:System',
                      'choice_label' => 'name'
                    ]
                )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BluesoftBundle\Entity\Contract'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bluesoftbundle_contract';
    }


}
