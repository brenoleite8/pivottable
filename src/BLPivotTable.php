<?php
namespace BrenoLeite8\pivottable;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TStyle;
use Adianti\Database\TRecord;
use Adianti\Database\TRepository;

class BLPivotTable extends TElement
{
    protected $id;
    
    private $return;
    private $aggregator;
    private $typeTable;
    private $valAggregator = array();
    private $objects       = array();
    private $fieldNames    = array();
    private $rows          = array(); 
    private $columns       = array();
    
    public function __construct()
    {
        parent::__construct('div');
        
        // CSS Libraries
        TStyle::importFromFile('vendor/brenoleite8/pivottable/src/css/pivot.min.css');
        
        $this->id = 'bl_pivot_table_' . uniqid();
      
    }

    public function setObjects(array $repository) 
    {
        foreach($repository as $key => $object)  
        {
            $this->objects[$key] = $object->toArray();
        }
    }

    public function setRows(array $rows) 
    {       
        $this->rows = $rows;
    }

    public function setId($id) 
    {       
        $this->id = $id;
    }

    public function getId() 
    {       
        return $this->id;
    }

    public function setColumns(array $columns) 
    {       
        $this->columns = $columns;
    }

    public function setFieldNames( array $names)
    {
        $this->fieldNames = $names;
    }

    public function setAggregator(string $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    public function setValAggregator(array $valAggregator)
    {
        $this->valAggregator = $valAggregator;
    }

    public function setTypeTable(string $typeTable)
    {
        $this->typeTable = $typeTable;
    }

    private function formatDataAndColumns(array $data, array $mapping)
    {
        // Atualizar os dados mapeando as chaves
        return array_map(function ($row) use ($mapping) {
            $formattedRow = [];
            foreach ($row as $key => $value) {
                $newKey = $mapping[$key] ?? $key; // Mantém a chave original se não houver mapeamento
                $formattedRow[$newKey] = $value;
            }
            return $formattedRow;
        }, $data);
    }

    private function formatColumnNames(array $columns, array $mapping)
    {
        return array_map(function ($col) use ($mapping) {
            return $mapping[$col] ?? $col;
        }, $columns);
    }

    private function create()
    {
        if(!empty($this->fieldNames)) {
            if(!empty($this->objects))
                $this->objects = $this->formatDataAndColumns($this->objects, $this->fieldNames);
            if(!empty($this->rows))
                $this->rows    = $this->formatColumnNames($this->rows, $this->fieldNames);
            if(!empty($this->columns))
                $this->columns = $this->formatColumnNames($this->columns, $this->fieldNames);
        }
        $jsonData    = json_encode($this->objects);
        $jsonRows    = json_encode($this->rows);
        $jsonColumns = json_encode($this->columns);

        $aggregator    = (!empty($this->aggregator)) ? ', aggregatorName: "'.$this->aggregator.'"' : '';
        $valAggregator = (!empty($this->aggregator)) ? ', vals: '. json_encode($this->valAggregator) : '';
        $typeTable     = (!empty($this->typeTable))  ? ', rendererName: '. $this->typeTable : '';
       
        $script = "$(function(){
                            $('#".$this->id."').pivotUI(
                                ".$jsonData.",
                                {
                                    rows: ".$jsonRows.",
                                    cols: ".$jsonColumns."
                                    ".$valAggregator."
                                    ".$aggregator."
                                    ".$typeTable."
                                }
                            , false, \"pt\");
                        });";
        TScript::create($script);
    }


    public function show()
    {
        $this->create();

        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->src  = 'vendor/brenoleite8/pivottable/src/js/pivot.min.js';

        $script_pt = new TElement('script');
        $script_pt->type = 'text/javascript';
        $script_pt->src  = 'vendor/brenoleite8/pivottable/src/js/pivot.pt.min.js';
        
        $content = new TElement('div');
        $content->id = $this->id;
        return  $script.$script_pt.$content;
    }

}
