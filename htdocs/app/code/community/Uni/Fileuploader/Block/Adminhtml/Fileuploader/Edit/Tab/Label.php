<?php
/**
 * Unicode Systems
 * @category   Uni
 * @package    Uni_Fileuploader
 * @copyright  Copyright (c) 2010-2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Uni_Fileuploader_Block_Adminhtml_Fileuploader_Edit_Tab_Label extends Mage_Adminhtml_Block_Widget_Form 
{

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('fileuploader_label_fieldset', array('legend' => Mage::helper('fileuploader')->__('Default Label')));
              
        if (Mage::getSingleton('adminhtml/session')->getFileUploaderData()) {
            $model = Mage::getSingleton('adminhtml/session')->getFileUploaderData();
            Mage::getSingleton('adminhtml/session')->setFileUploaderData(null);
        } elseif (Mage::registry('fileuploader_data')) {
            $model = Mage::registry('fileuploader_data')->getData();
        }

        if (is_array($model) )
            $labels = Mage::getModel('fileuploader/fileuploader')->Load($model['fileuploader_id'])->getStoreLabels();
        else 
            $labels = $model->getStoreLabels();

        $fieldset->addField('store_default_label', 'text', array(
            'name'      => 'store_labels[0]',
            'required'  => false,
            'label'     => Mage::helper('fileuploader')->__('Default Attachmnet label for All Store Views'),
            'value'     => isset($labels[0]) ? $labels[0] : '',
        ));

        $fieldset = $form->addFieldset('store_labels_fieldset', array(
            'legend'       => Mage::helper('fileuploader')->__('Store View Specific Labels'),
            'table_class'  => 'form-list stores-tree',
        ));
        
        
        foreach (Mage::app()->getWebsites() as $website) {
            $fieldset->addField("w_{$website->getId()}_label", 'note', array(
                'label'    => $website->getName(),
                'fieldset_html_class' => 'website',
            ));
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                $fieldset->addField("sg_{$group->getId()}_label", 'note', array(
                    'label'    => $group->getName(),
                    'fieldset_html_class' => 'store-group',
                ));
                foreach ($stores as $store) {
                    $fieldset->addField("s_{$store->getId()}", 'text', array(
                        'name'      => 'store_labels['.$store->getId().']',
                        'required'  => false,
                        'label'     => $store->getName(),
                        'value'     => isset($labels[$store->getId()]) ? $labels[$store->getId()] : '',
                        'fieldset_html_class' => 'store',
                    ));
                }
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

}