<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * TablesIgniter
 *
 * TablesIgniter based on CodeIgniter3. This library will help you use jQuery Datatables in server side mode.
 * @package    CodeIgniter3
 * @subpackage libraries
 * @category   library
 * @version    2.0.0
 * @author    monkenWu <610877102@mail.nknu.edu.tw>
 * @link      https://github.com/monkenWu/TablesIgniter_ci3
 *
 */

class TablesIgniter{ // Renamed class to match filename for CI loading conventions

    protected $builder;
    protected $tableName;
    protected $outputColumn;
    protected $defaultOrder = [];
    protected $searchLike = [];
    protected $order = [];

    public function __construct(array $init = []){
        if(!empty($init)){
            if(isset($init["setTable"][0]) && isset($init["setTable"][1])) // Corrected isset check
                $this->setTable($init["setTable"][0],$init["setTable"][1]);
            if(isset($init["setOutput"]))
                $this->setOutput($init["setOutput"]);
            if(isset($init["setDefaultOrder"])){
                foreach ($init["setDefaultOrder"] as $value) {
                    $this->setDefaultOrder($value[0],$value[1]);
                }
            }
            if(isset($init["setSearch"]))
                $this->setSearch($init["setSearch"]);
            if(isset($init["setOrder"]))
                $this->setOrder($init["setOrder"]);
        }
    }

    /**
     * Set the table and the CI Query Builder instance
     */
    public function setTable($builder,$tableName){
        $this->builder = &$builder;
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Set the searchable columns
     */
    public function setSearch(array $like){
        $this->searchLike = $like;
        return $this;
    }

    /**
     * Set the orderable columns
     */
    public function setOrder(array $order){
        $this->order = $order;
        return $this;
    }

    /**
     * Set the default order
     */
    public function setDefaultOrder($item,$type="ASC"){
        $this->defaultOrder[] = array($item, $type);
        return $this;
    }

    /**
     * Set the output columns, can include closures for custom formatting
     */
    public function setOutput(array $column){
        $this->outputColumn = $column;
        return $this;
    }

    private function getBuilder(){
        return clone $this->builder;
    }

    /**
     * Get the number of filtered records
     */
    private function getFiltered(){
        $bui = $this->extraConfig($this->getBuilder());
        $query = $bui->get($this->tableName)->num_rows();
        return $query;
    }

    /**
     * Get the total number of records
     */
    private function getTotal(){
        $bui = $this->getBuilder();
        $query = $bui->count_all_results($this->tableName);
        return $query;
    }

    /**
     * Execute the main query to get data for the current page
     * @return  object
     */
    private function getQuery(){
        $bui = $this->extraConfig($this->getBuilder());
        if(isset($_POST["length"])){
            if($_POST["length"] != -1) {
                $bui->limit($_POST['length'], $_POST['start']);
            }
        }
        $query = $bui->get($this->tableName);
        return $query;
    }

    /**
     * Format the output data for each row based on the setOutput configuration.
     * Uses your modified version for associative arrays.
     */
	private function getOutputData($row){
		$output = [];
		foreach ($this->outputColumn as $key => $col) {
			if (is_callable($col)) {
				// If it's a closure, use the array key as the output key
				$output[$key] = $col($row);
			} elseif (is_string($col)) {
				// Use the column name as the output key
				$output[$col] = $row[$col];
			}
		}
		return $output;
	}

    /**
     * Build the OR LIKE WHERE clause for searching
     */
    private function makeOrLike(array $like_column,$value){
        $where = "(";
        for($k=0;$k<count($like_column);$k++){
            if($k==0){
                $where .= $like_column[$k];
                $where .= " LIKE '%".$value."%'";
            }else{
                $where .= " OR ";
                $where .= $like_column[$k];
                $where .= " LIKE '%".$value."%'";
            }
            if($k == count($like_column)-1){
                $where .= ")";
            }
        }
        return $where;
    }

    /**
     * Apply search and order configurations to the Query Builder instance
     */
    private function extraConfig($bui){
        if(!empty($_POST["search"]["value"])){
            $like = $this->makeOrLike(
                $this->searchLike,
                $_POST["search"]["value"]
            );
            $bui->where($like, NULL, FALSE);
        }
        if(isset($_POST["order"])){
            if(!empty($this->order)){
                if(isset($this->order[$_POST['order']['0']['column']]) && $this->order[$_POST['order']['0']['column']] != null){
                    $bui->order_by($this->order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
                }else{
                    if(count($this->defaultOrder)!=0){
                        foreach ($this->defaultOrder as $value) {
                            $bui->order_by($value[0], $value[1]);
                        }
                    }
                }
            }else{
                if(count($this->defaultOrder)!=0){
                    foreach ($this->defaultOrder as $value) {
                        $bui->order_by($value[0], $value[1]);
                    }
                }
            }
        }else{
            if(count($this->defaultOrder)!=0){
                foreach ($this->defaultOrder as $value) {
                    $bui->order_by($value[0], $value[1]);
                }
            }
        }
        return $bui;
    }

    /**
     * Generate the final DataTables JSON output
     */
    public function getDatatable($isJson=true){
        if($result = $this->getQuery()){
            $data = array();
            foreach ($result->result_array() as $row){
                $data[] = $this->getOutputData($row);
            }
            $output = array(
                "draw" => (int)($_POST["draw"] ?? -1),
                "recordsTotal" => $this->getTotal(),
                "recordsFiltered" => $this->getFiltered(),
                "data" => $data
            );
            return $isJson ? json_encode($output) : $output;
        }
        return false; // Return false if query fails
    }

}
/* End of file TablesIgniter.php */
/* Location: ./application/libraries/TablesIgniter.php */
