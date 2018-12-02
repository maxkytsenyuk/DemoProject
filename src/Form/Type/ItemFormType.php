<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Item;

class ItemFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    'label' => 'Item Name',
                    'required' => true,
                )
            )
            ->add(
                'type',
                TextType::class,
                array(
                    'label' => 'Type',
                    'required' => false
                )
            )
            ->add(
                'is_private',
                CheckboxType::class,
                array(
                    'attr' => array(
                        'class' => 'custom-control-input',
                    ),
                    'label_attr' => array(
                        'class' => 'custom-control-label'
                    ),
                    'label' => 'Is Private'
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'data_class' => Item::class,
                    'is_new' => true,
                    'translation_domain' => 'client',
                )
            );

        parent::configureOptions($resolver);
    }

    /**
     * Returns the name of this type.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'item';
    }
}