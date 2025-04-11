# PivotTable
Adaptação da ferramenta PivotTable.js (https://pivottable.js.org/) para utilização no Adianti Framework e MadBuilder.
#### Obs: Está sendo utilizada a versão 2.23.0 do componente PivotTable.js.

# Instalação
### MadBuider
Na aba "Composer packages" adicione:

    brenoleite8/pivottable:dev-master

### Adianti Framework
Navegue até o diretório raiz do projeto Adianti Framework usando o terminal:

    cd /caminho/do/seu/projeto

Use o comando composer require para adicionar a biblioteca:

    composer require brenoleite8/pivottable:dev-master

# Utilização

No início da classe que irá utilizar a ferramenta, adicione : 

    use Brenoleite8\pivottable\BLPivotTable;

Utilize as classes ``BElement`` ou ``TElement`` para inserir a PivotTable.

Exemplo: 

<?php

use Brenoleite8\pivottable\BLPivotTable;

class PivotTableForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_PivotTableForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("PivotTable");


        $element_pivot = new BElement('div');


        $element_pivot->setSize('100%', 80);

        $element_pivot->id = 'element_pivot';

        $this->element_pivot = $element_pivot;

        // Obtém os dados do repositório
        TTransaction::open('db_ferramentas');
        $objects = VendasCompleta::all();
        TTransaction::close();

        // Configura a PivotTable
        $pivottable = new BLPivotTable();
        $pivottable->setRows(['pessoa']); // Campos exibidos como linhas
        $pivottable->setColumns(['mes']); // Campos exibidos como colunas
        $pivottable->setObjects($objects); // Dados carregados na tabela
        $pivottable->setAggregator('Soma');
        $pivottable->setValAggregator(['Valor Total']);
        $pivottable->setTypeTable('Mapa de Calor');
        $pivottable->setFieldNames([
            'venda_id'      => 'ID da Venda',
            'pessoa'        => 'Nome da Pessoa',
            'produto'       => 'Produto',
            'categoria'     => 'Categoria',
            'data_venda_br' => 'Data da Venda',
            'mes'           => 'Mês',
            'ano'           => 'Ano',
            'quantidade'    => 'Quantidade',
            'preco'         => 'Preço Unitário',
            'total_venda'   => 'Valor Total'
        ]);
        $show_table = $pivottable->show();
        $element_pivot->add($show_table);
        $row1 = $this->form->addFields([$element_pivot]);
        $row1->layout = [' col-sm-12'];

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onShow($param = null)
    {               

    } 

}



# Outros recursos
O PivotTable.js oficial possui inúmeros recursos. Inicialmente, foi desenvolvido apenas com os básicos, mas os outros podem ser adicionadas futuramente.

# Contribuições
Entre em contato no email `brenoleite8@outlook.com`.
