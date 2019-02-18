<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingRateModel extends \Model\BaseModel
{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_rate';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['category_id', 'required'],
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, r.content, c.title AS category_name   
            FROM {tablePrefix}ext_hosting_rate t 
            LEFT JOIN {tablePrefix}ext_hosting_review r ON r.id = t.review_id
            LEFT JOIN {tablePrefix}ext_hosting_rate_category c ON c.id = t.category_id
            WHERE 1';

        $params = [];
        if (is_array($data)) {
        }

        $sql .= ' ORDER BY t.created_at DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        return $rows;
    }

    /**
     * @param $id
     * @return array
     */
    public function getDetail($id)
    {
        $sql = 'SELECT t.*, r.content, c.title AS category_name 
            FROM {tablePrefix}ext_hosting_rate t 
            LEFT JOIN {tablePrefix}ext_hosting_review r ON r.id = t.review_id
            LEFT JOIN {tablePrefix}ext_hosting_rate_category c ON c.id = t.category_id
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }
}
