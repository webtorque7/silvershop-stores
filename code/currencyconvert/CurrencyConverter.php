<?php

/**
 * Created by PhpStorm.
 * User: User
 * Date: 08/06/2016
 * Time: 1:01 PM
 */
class CurrencyConverter extends DataObject
{
    private static $db = array(
        'BaseCurrency' => 'Varchar(3)'
    );

    private static $has_many = array(
        'CurrencyRates' => 'CurrencyRate'
    );

    public function getCMSFields()
    {
        $fields = new FieldList(
            new TabSet("Root",
                $tabMain = new Tab('Main',
                    TextField::create('BaseCurrency', 'Base Currency')
                        ->setDescription('Please use a ISO4217 currency code.'),
                    GridField::create('CurrencyRates', 'Currency Rates', $this->CurrencyRates(),
                        GridFieldConfig_RelationEditor::create()
                            ->removeComponentsByType('GridFieldAddExistingAutocompleter')
                            ->removeComponentsByType('GridFieldDeleteAction')
                            ->addComponent(new GridFieldDeleteAction(false))
                    )
                )
            )
        );

        return $fields;
    }

    public function getCMSActions() {
        if (Permission::check('ADMIN') || Permission::check('EDIT_SITECONFIG')) {
            $actions = new FieldList(
                FormAction::create('save_converter', _t('CMSMain.SAVE','Save'))
                    ->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
            );
        } else {
            $actions = new FieldList();
        }

        $this->extend('updateCMSActions', $actions);

        return $actions;
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $converter = DataObject::get_one('CurrencyConverter');
        if (empty($converter)) {
            $converter = CurrencyConverter::create();
            $converter->write();
        }
    }
}

class CurrencyConverterLeftAndMain extends LeftAndMain
{

    /**
     * @var string
     */
    private static $url_segment = 'currency_converter_admin';

    /**
     * @var string
     */
    private static $url_rule = '/$Action/$ID/$OtherID';

    /**
     * @var string
     */
    private static $menu_title = 'Currency Converter';

    /**
     * @var string
     */
    private static $tree_class = 'CurrencyConverter';

    /**
     * @var array
     */
    private static $required_permission_codes = array('EDIT_SITECONFIG');


    /**
     * @param null $id Not used.
     * @param null $fields Not used.
     *
     * @return Form
     */
    public function getEditForm($id = null, $fields = null)
    {
        $converter = DataObject::get_one('CurrencyConverter');
        $fields = $converter->getCMSFields();

        // Tell the CMS what URL the preview should show
        $home = Director::absoluteBaseURL();
        $fields->push(new HiddenField('PreviewURL', 'Preview URL', $home));

        // Added in-line to the form, but plucked into different view by LeftAndMain.Preview.js upon load
        $fields->push($navField = new LiteralField('SilverStripeNavigator', $this->getSilverStripeNavigator()));
        $navField->setAllowHTML(true);

        // Retrieve validator, if one has been setup (e.g. via data extensions).
        if ($converter->hasMethod("getCMSValidator")) {
            $validator = $converter->getCMSValidator();
        } else {
            $validator = null;
        }

        $actions = $converter->getCMSActions();
        $form = CMSForm::create(
            $this, 'EditForm', $fields, $actions, $validator
        )->setHTMLID('Form_EditForm');
        $form->setResponseNegotiator($this->getResponseNegotiator());
        $form->addExtraClass('cms-content center cms-edit-form');
        $form->setAttribute('data-pjax-fragment', 'CurrentForm');

        if ($form->Fields()->hasTabset()) {
            $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
        }
        $form->setHTMLID('Form_EditForm');
        $form->loadDataFrom($converter);
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        // Use <button> to allow full jQuery UI styling
        $actions = $actions->dataFields();
        if ($actions) {
            foreach ($actions as $action) {
                $action->setUseButtonTag(true);
            }
        }

        $this->extend('updateEditForm', $form);

        return $form;
    }

    /**
     * Used for preview controls, mainly links which switch between different states of the page.
     *
     * @return ArrayData
     */
    public function getSilverStripeNavigator()
    {
        return $this->renderWith('CMSSettingsController_SilverStripeNavigator');
    }

    /**
     * Save the current sites {@link CurrencyConverter} into the database.
     *
     * @param array $data
     * @param Form $form
     * @return String
     */
    public function save_converter($data, $form)
    {
        $converter = DataObject::get_one('CurrencyConverter');
        $form->saveInto($converter);

        try {
            $converter->write();
        } catch (ValidationException $ex) {
            $form->sessionMessage($ex->getResult()->message(), 'bad');
            return $this->getResponseNegotiator()->respond($this->request);
        }

        $this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));

        return $form->forTemplate();
    }


    public function Breadcrumbs($unlinked = false)
    {
        $defaultTitle = self::menu_title_for_class(get_class($this));

        return new ArrayList(array(
            new ArrayData(array(
                'Title' => _t("{$this->class}.MENUTITLE", $defaultTitle),
                'Link' => $this->Link()
            ))
        ));
    }
}