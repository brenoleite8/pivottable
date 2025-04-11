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
    
    class teste extends TPage
    {
        protected $form;
        private $formFields = [];
        private static $database = '';
        private static $activeRecord = '';
        private static $primaryKey = '';
        private static $formName = 'form_teste';

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
            $this->form->setFormTitle("Teste");


            $table = new BElement('div');
            $table->setSize('100%', 80);
            $table->id = 'table';
            $this->table = $table;

            // Obtém os dados do repositório
            TTransaction::open('db_teste');
            $objects = Pessoa::select('idade', 'nome')->load();
            TTransaction::close();

            // Configura a PivotTable
            $pivottable = new BLPivotTable();
            $pivottable->setRows(['nome']); // Campos exibidos como linhas
            $pivottable->setColumns(['idade']); // Campos exibidos como colunas
            $pivottable->setObjects($objects); // Dados carregados na tabela
            $pivottable->setFieldNames([
                'nome'  => 'Nome', 
                'idade' => 'Idade'
            ]); // Mapeamento para formatação dos nomes das colunas
            $show_table = $pivottable->show();
            $table->add($show_table);

            $row1 = $this->form->addFields([$table]);
            $row1->layout = [' col-sm-12'];

            // vertical box container
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->class = 'form-container';
            if(empty($param['target_container']))
            {
                $container->add(TBreadCrumb::create(["Agendamentos","Teste"]));
            }
            $container->add($this->form);

            parent::add($container);

        }

# Outros recursos
O PivotTable.js oficial possui inúmeros recursos. Inicialmente, foi desenvolvido apenas com os básicos, mas os outros podem ser adicionadas futuramente.

# Contribuições
Entre em contato no email `brenoleite8@outlook.com`.
