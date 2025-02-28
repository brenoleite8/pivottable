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
    
    private $objects    = array();
    private $fieldNames = array();
    private $rows       = array(); 
    private $columns    = array();
    private $plotly     = false;
    private $export     = false;
    
    private $return;

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

    public function setColumns(array $columns) 
    {       
        $this->columns = $columns;
    }

    public function setFieldNames( array $names)
    {
        $this->fieldNames = $names;
    }

    public function usePlotly()
    {
        $this->plotly = TRUE;
    }

    public function useExport()
    {
        $this->export = TRUE;
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
        $renderers   = '';
        $derivers    = '';
        if($this->plotly === TRUE OR $this->export === TRUE) {
            $derivers   = 'var derivers = $.pivotUtilities.derivers;';
            
            $renderer = [];

            if ($this->plotly === TRUE) {
                $renderer[] = '$.pivotUtilities.plotly_renderers';
            }
            if ($this->export === TRUE) {
                $renderer[] = '$.pivotUtilities.export_renderers';
            }

            // Junta os valores com vírgula
            $renderer = implode(',', $renderer);
            
            $renderers  = 'var renderers = $.extend(
                                $.pivotUtilities.renderers,
                                '.$renderer.'
                            );';
        }
        $renderers_value = (!empty($renderers)) ? 'renderers: renderers,' : '' ;
        
        $script = "$(function(){
                            ".$derivers.$renderers."
                            $('#".$this->id."').pivotUI(                               
                                ".$jsonData.",
                                {
                                    ".$renderers_value."
                                    rows: ".$jsonRows.",
                                    cols: ".$jsonColumns."
                                }
                            , false, \"pt\");
                        });";

        TScript::create($script);

        return $script;
    }


    public function show()
    {
        $teste = $this->create();
        echo($teste);

        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->src  = 'vendor/brenoleite8/pivottable/src/js/pivot.min.js';

        $script_pt = new TElement('script');
        $script_pt->type = 'text/javascript';
        $script_pt->src  = 'vendor/brenoleite8/pivottable/src/js/pivot.pt.min.js';

        $renderers = '';

        if ($this->plotly === TRUE) {
            $script_plotly = new TElement('script');
            $script_plotly->type = 'text/javascript';
            $script_plotly->src  = 'vendor/brenoleite8/pivottable/src/js/plotly_renderers.min.js';
            $renderers .= $script_plotly;

            $script_plotly_basic = new TElement('script');
            $script_plotly_basic->type = 'text/javascript';
            $script_plotly_basic->src  = 'vendor/brenoleite8/pivottable/src/js/plotly-basic-latest.min.js';
            $renderers .= $script_plotly_basic;
        }

        if ($this->export === TRUE) {
            $script_export = new TElement('script');
            $script_export->type = 'text/javascript';
            $script_export->src  = 'vendor/brenoleite8/pivottable/src/js/export_renderers.min.js';
            $renderers .= $script_export;
        }

        $content = new TElement('div');
        $content->id = $this->id;
                
        return  $script.$script_pt.$renderers.$content;
    }

}
