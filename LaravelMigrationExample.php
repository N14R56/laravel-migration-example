<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateNfeFinanceiroContasPagNDocumentoPag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Service::updateAndPrintExecutionStatus();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

class Service
{
    public static function updateAndPrintExecutionStatus()
    {
        $updates = new Updates();

        $existance = new Existance();

        $updates->ifRegistroExistsUpdate($existance);

        Prints::status($updates, $existance);
    }
}

class Prints
{
    public static function status(Updates $updates, Existance $existance): void
    {  
        self::ifExists($existance);

        self::jumpLine();

        self::numberOfUpdates($updates);

        self::jumpLine();
    }

    private static function ifExists(Existance $existance): void
    {  
        if ($existance->getRecordExists() === true)
        {
            echo 'O registro existe';

            return;
        }
        echo 'O registro não existe nesta conexão com o banco.';
    }

    private static function numberOfUpdates(Updates $updates): void
    {  
        echo "O número de registros que foram atualizados é {$updates->getNumberOfRecordsUpdated()}";
    }

    private static function jumpLine(): void
    {  
        echo PHP_EOL;
    }
}

class Updates
{
    private $numberOfRecordsUpdated = 0;

    public function ifRegistroExistsUpdate(Existance $existance): void
    {
        $existance->setRecordExists();

        if ($existance->getRecordExists() === true)
        {
            $number = Repository::update();

            $this->setNumberOfRecordsUpdated($number);
        }
    }

    private function setNumberOfRecordsUpdated(int $number): void
    {
        $this->numberOfRecordsUpdated = $number;
    } 

    public function getNumberOfRecordsUpdated(): int
    {
        return $this->numberOfRecordsUpdated;
    }  
}

class Existance
{
    private $recordExists;

    public function setRecordExists(): void
    {        
        $this->recordExists = Repository::existeORegistroNesteBanco();
    }

    public function getRecordExists(): bool
    {
        return $this->recordExists;
    }
}

class Repository
{
    const TABLE = 'table_fin_contas_pg';

    const ID_EMPRESA = 44444;

    const REGISTRO_PROBLEMATICO = 22222222;

    public static function existeORegistroNesteBanco(): bool
    {
        return DB::table(self::TABLE)
            ->select([
                'id_conta_pg',
                'n_doc_pg'
            ])
            ->where('id_conta_pg', '=', self::REGISTRO_PROBLEMATICO)
            ->where('id_empresa', '=', self::ID_EMPRESA)
            ->exists();
    }

    public static function update(): int
    {
        return DB::table(self::TABLE)
            ->where('id_conta_pg', '=', self::REGISTRO_PROBLEMATICO)
            ->where('id_empresa', '=', self::ID_EMPRESA)
            ->update([
               'n_documento_pg' => '999999'
            ]);
    }
}
