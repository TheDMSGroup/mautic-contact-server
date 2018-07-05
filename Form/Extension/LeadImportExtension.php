<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticContactSourceBundle\Form\Extension;

use Doctrine\ORM\EntityManager;
use Mautic\CampaignBundle\Entity\Campaign;
use MauticPlugin\MauticContactSourceBundle\Entity\ContactSource;
use Symfony\Component\Form\AbstractTypeExtension;
use Mautic\LeadBundle\Form\Type\LeadImportType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;

class LeadImportExtension extends AbstractTypeExtension
{
    /** @var EntityManager */
    protected $em;

    public function __construct(EntityManager $em)
    {
        /* @var EntityManager $em */
        $this->em = $em;
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return LeadImportType::class;
    }

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //
    // }
    //
    // public function buildView(FormView $view, FormInterface $form, array $options)
    // {
    //
    // }

    /**
     * Add a custom 'object' type to write to a corresponding table for that new custom value.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
          'source',
          EntityType::class,
          array(
              'class'       => 'MauticContactSourceBundle:ContactSource',
              'empty_value' => 'Select A Source',
              'choice_label' => function ($source) {
                  return $source->getName();
              },
          )
        );

        $builder->get('source')
            ->addModelTransformer(new CallbackTransformer(
                function ($source) {
                    // transform the ID back to an Object
                    return $source ? $this->em->getRepository('MauticCampaignBundle:Event')->find($source) : null;
                },
                function ($source) {
                    // transform the object to a ID
                    return $source ? $source->getID() : null;

                }
            ));

        $builder->add(
            'campaign',
            EntityType::class,
            array(
                'class'       => 'MauticCampaignBundle:Campaign',
                'empty_value' => 'Select A Campaign',
                'choice_label' => function ($campaign) {
                    return $campaign->getName();
                },
            )
        );

        $builder->get('campaign')
            ->addModelTransformer(new CallbackTransformer(
                function ($campaign) {
                    // transform the ID back to an Object
                    return $campaign ? $this->em->getRepository('MauticCampaignBundle:Campaign')->find($campaign) : null;
                },
                function ($campaign) {
                    // transform the object to a ID
                    return $campaign ? $campaign->getID() : null;

                }
            ));

        // make sure if checked that cache record and evaluate limits are bypassed
        // $builder->add(
        //     'evaluate',
        //     'yesno_button_group',
        //     [
        //         'label'      => 'Evaluate Limits?',
        //         'label_attr' => ['class' => 'control-label'],
        //         'attr'       => [
        //             'class'   => 'form-control',
        //             'tooltip' => 'Should the leads in the uploaded file apply towards limits, duplicates or other validations?',
        //         ],
        //         'required'   => true,
        //         'data'       => 1,
        //     ]
        // );

    }
}