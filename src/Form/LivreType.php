<?php

namespace App\Form;

use App\Entity\Livre;
use App\Entity\Auteur;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
			->add('quatrieme')
            ->add('auteurs', EntityType::class, [
				"class" => Auteur::class,
				"multiple" => true,
				"expanded" => true,
				])
		;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
            'url' => null
        ]);
    }
}

