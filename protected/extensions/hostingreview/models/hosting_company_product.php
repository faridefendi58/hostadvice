<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HostingCompanyProductModel extends \Model\BaseModel
{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_hosting_company_product';
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
        $sql = 'SELECT t.*, c.title AS category_name   
            FROM {tablePrefix}ext_hosting_company_product t 
            LEFT JOIN {tablePrefix}ext_hosting_product_category c ON c.id = t.category_id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['company_id'])) {
                $sql .= ' AND t.company_id=:company_id';
                $params['company_id'] = $data['company_id'];
            }

            if (isset($data['category_id'])) {
                $sql .= ' AND t.category_id=:category_id';
                $params['category_id'] = $data['category_id'];
            }

            if (isset($data['enabled'])) {
                $sql .= ' AND t.enabled=:enabled';
                $params['enabled'] = $data['enabled'];
            }
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
        $sql = 'SELECT t.*  
            FROM {tablePrefix}ext_hosting_company_product t 
            WHERE t.id =:id';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $row = \Model\R::getRow( $sql, ['id'=>$id] );

        return $row;
    }

    public function getDataGroupedByCategory($data = array())
    {
        $sql = 'SELECT t.*, COUNT(t.id) AS tot_plan, MIN(t.price_range_from) AS price_from, 
            MIN(t.price_range_to) AS price_to 
            FROM {tablePrefix}ext_hosting_company_product t 
            WHERE t.enabled = 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['company_id'])) {
                $sql .= ' AND t.company_id=:company_id';
                $params['company_id'] = $data['company_id'];
            }

            if (isset($data['category_id'])) {
                $sql .= ' AND t.category_id=:category_id';
                $params['category_id'] = $data['category_id'];
            }
        }

        $sql .= ' GROUP BY t.category_id ORDER BY t.price_range_from DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        $items = [];
        if (count($rows) > 0) {
            foreach ($rows as $i => $row) {
                $items[$row['category_id']] = $row;
            }

            return $items;
        }

        return $rows;
    }

    public function getStartingPrices($data = array())
    {
        $sql = 'SELECT MIN(t.price_range_from) AS price_from, 
            MIN(t.price_range_to) AS price_to 
            FROM {tablePrefix}ext_hosting_company_product t 
            WHERE t.enabled = 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['company_id'])) {
                $sql .= ' AND t.company_id=:company_id';
                $params['company_id'] = $data['company_id'];
            }

            if (isset($data['category_id'])) {
                $sql .= ' AND t.category_id=:category_id';
                $params['category_id'] = $data['category_id'];
            }
        }

        $sql .= ' GROUP BY t.category_id ORDER BY t.price_range_from DESC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        $items = [];
        if (count($rows) > 0) {
            foreach ($rows as $i => $row) {
                $items[] = $row;
            }

            return $items;
        }

        return $rows;
    }

    public function getOptions($data) {
        $datas = self::getData($data);
        $items = [];
        if (is_array($datas) && count($datas) > 0) {
            foreach ($datas as $i => $data) {
                $items[$data['category_id']][] = $data;
            }
        }

        return $items;
    }
}
