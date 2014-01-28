<?php
/**
 * CommerceLab Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the CommerceLab License Agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://commerce-lab.com/LICENSE.txt
 *
 * @category   CommerceLab
 * @package    CommerceLab_News
 * @copyright  Copyright (c) 2012 CommerceLab Co. (http://commerce-lab.com)
 * @license    http://commerce-lab.com/LICENSE.txt
 */

class CommerceLab_News_Model_Mysql4_News_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('clnews/news');
    }

    public function addEnableFilter($status)
    {
        $this->getSelect()
            ->where('status = ?', $status);
        return $this;
    }

    public function addCategoryFilter($categoryId)
    {
        $this->getSelect()->join(
            array('news_category_table' => $this->getTable('news_category')),
            'main_table.news_id = news_category_table.news_id',
            array()
        )->join(
            array('category_table' => $this->getTable('category')),
            'news_category_table.category_id = category_table.category_id',
            array()
        )->join(
            array('category_store_table' => $this->getTable('category_store')),
            'category_table.category_id = category_store_table.category_id',
            array()
        )
        ->where('category_table.category_id = "'.$categoryId.'"')
        ->where('category_store_table.store_id in (?)', array(0, Mage::app()->getStore()->getId()))
        ;
        return $this;
    }

    public function addStoreFilter($store)
    {
        //Disabiliato per da un pò di coerenza a tutto ....
        return $this;
        $this->getSelect()->join(
            array('news_store_table' => $this->getTable('news_store')),
            'main_table.news_id = news_store_table.news_id',
            array()
        )
        ->where('news_store_table.store_id in (?)', array(0, $store));
        $this->getSelect()->distinct();
        return $this;
    }
}