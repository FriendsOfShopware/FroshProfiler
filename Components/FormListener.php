<?php


namespace ShyimProfiler\Components;


use Shopware\Bundle\FormBundle\Extension\EventExtension;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\DataCollector\FormDataCollectorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FormListener extends AbstractTypeExtension
{
    /**
     * @var EventExtension
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    private $eventExtension;

    /**
     * @var FormDataCollectorInterface
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    private $dataCollector;

    /**
     * FormListener constructor.
     * @param EventExtension $eventExtension
     * @param FormDataCollectorInterface $formDataCollector
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function __construct(EventExtension $eventExtension, FormDataCollectorInterface $formDataCollector)
    {
        $this->eventExtension = $eventExtension;
        $this->dataCollector = $formDataCollector;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     * @author Soner Sayakci <s.sayakci@gmail.com>
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'postSetData'], 255);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'postSubmit'], -255);
        $this->eventExtension->buildForm($builder, $options);
    }


    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FormType';
    }

    /**
     * Listener for the {@link FormEvents::POST_SET_DATA} event.
     *
     * @param FormEvent $event The event object
     */
    public function postSetData(FormEvent $event)
    {
        if ($event->getForm()->isRoot()) {
            // Collect basic information about each form
            $this->dataCollector->collectConfiguration($event->getForm());

            // Collect the default data
            $this->dataCollector->collectDefaultData($event->getForm());
        }
    }

    /**
     * Listener for the {@link FormEvents::POST_SUBMIT} event.
     *
     * @param FormEvent $event The event object
     */
    public function postSubmit(FormEvent $event)
    {
        if ($event->getForm()->isRoot()) {
            // Collect the submitted data of each form
            $this->dataCollector->collectSubmittedData($event->getForm());

            // Assemble a form tree
            // This is done again after the view is built, but we need it here as the view is not always created.
            $this->dataCollector->buildPreliminaryFormTree($event->getForm());
        }
    }
}