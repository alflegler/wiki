<?php

namespace Acme\WikiBundle\Form\Type;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PageType extends BaseAbstractType
{
    protected $options = array(
        'data_class' => 'Acme\WikiBundle\Model\Page',
        'name'       => 'page',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pageName', 'text', array(
            'required' => false));
        $builder->add('title', 'text', array(
            'required' => true,
            'trim' => true));
        $builder->add('text', 'textarea', array(
            'required' => true));
        $builder->add('save', 'submit');
    }
}
