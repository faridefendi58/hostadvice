<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingFeatureModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_feature';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['title', 'required'],
        ];
    }

    /**
     * @return array
     */
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, a.name AS admin_name  
            FROM {tablePrefix}ext_hosting_feature t 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
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
        $sql = 'SELECT t.*,  a.name AS created_by_name, ab.name AS updated_by_name 
            FROM {tablePrefix}ext_hosting_feature t 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            LEFT JOIN {tablePrefix}admin ab ON ab.id = t.updated_by 
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }
}
