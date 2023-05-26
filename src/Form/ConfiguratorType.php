<?php

namespace App\Form;

use App\Entity\Configurator;
use App\Entity\Product;

use App\Repository\ProductRepository;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

use Doctrine\ORM\EntityRepository;


class ConfiguratorType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('totalPrice')
            // ->add('User')
            // ->add('cart')
            ->add('ordinateur', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
                'mapped' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->join('p.category', 'c')
                        ->where('c.name = :categoryName')
                        ->setParameter('categoryName', 'ordinateur');
                }
            ])
            ->add('sacoche', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
                'mapped' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->join('p.category', 'c')
                        ->where('c.name = :categoryName')
                        ->setParameter('categoryName', 'sacoche');
                }
            ])
            ->add('clavier', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
                'mapped' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->join('p.category', 'c')
                        ->where('c.name = :categoryName')
                        ->setParameter('categoryName', 'clavier');
                }
            ])
            ->add('souris', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'title',
                'mapped' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->join('p.category', 'c')
                        ->where('c.name = :categoryName')
                        ->setParameter('categoryName', 'souris');
                }
            ])
            ; 
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Configurator::class,
        ]);
    }
}
