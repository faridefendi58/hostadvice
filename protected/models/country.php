<?php
namespace Model;

require_once __DIR__ . '/base.php';

class CountryModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'country';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['code, title', 'required'],
        ];
    }

    public function getItems()
    {
        $sql = 'SELECT t.*  
          FROM {tablePrefix}country t 
          WHERE 1';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $options = R::getAll( $sql );
        $items = [];
        if (is_array($options)) {
            foreach ($options as $i => $option) {
                $items[$option['code']] = $option['title'];
            }
        }

        return $items;
    }
}
