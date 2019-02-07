<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingPlanModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_plan';
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
        $sql = 'SELECT t.*, a.name AS admin_name, 
            c.title AS hosting_company_name, c.website AS hosting_company_website   
            FROM {tablePrefix}ext_hosting_plan t 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            LEFT JOIN {tablePrefix}ext_hosting_company c ON c.id = t.hosting_company_id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['hosting_company_id'])) {
                $sql .= ' AND t.hosting_company_id =:hosting_company_id';
                $params['hosting_company_id'] = $data['hosting_company_id'];
            }

            if (isset($data['hidden'])) {
                $sql .= ' AND t.hidden =:hidden';
                $params['hidden'] = $data['hidden'];
            }

            if (isset($data['headline'])) {
                $sql .= ' AND t.headline =:headline';
                $params['headline'] = $data['headline'];
            }
        }

        $sql .= ' ORDER BY t.created_at DESC';

        if (isset($data['limit'])) {
            $sql .= ' LIMIT '. $data['limit'];
        }

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
        $sql = 'SELECT t.*,  a.name AS created_by_name, ab.name AS updated_by_name,
            c.title AS hosting_company_name, c.website AS hosting_company_website  
            FROM {tablePrefix}ext_hosting_plan t 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            LEFT JOIN {tablePrefix}admin ab ON ab.id = t.updated_by 
            LEFT JOIN {tablePrefix}ext_hosting_company c ON c.id = t.hosting_company_id
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }

    public function getQuery($data)
    {
        $sql = 'SELECT t.*,  a.name AS created_by_name, ab.name AS updated_by_name,
            c.title AS hosting_company_name, c.website AS hosting_company_website 
            FROM {tablePrefix}ext_hosting_plan t 
            LEFT JOIN {tablePrefix}ext_hosting_company c ON c.id = t.hosting_company_id
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            LEFT JOIN {tablePrefix}admin ab ON ab.id = t.updated_by 
            WHERE 1';

        $params = [];
        if (isset($data['hosting_company_id'])) {
            $sql .= ' AND t.hosting_company_id =:hosting_company_id';
            $params['hosting_company_id'] = $data['hosting_company_id'];
        }

        if (isset($data['title'])) {
            $sql .= ' AND LOWER(t.title) =:title';
            $params['title'] = $data['title'];
        }

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, $params );

        return $row;
    }
}
