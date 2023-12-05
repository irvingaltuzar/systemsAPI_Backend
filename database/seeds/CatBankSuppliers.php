<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class CatBankSuppliers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANXICO',
            'receptor_bank' => 'BANCO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCOMEXT',
            'receptor_bank' => 'BCEXT',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANOBRAS',
            'receptor_bank' => 'BOBRA',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANJERCITO',
            'receptor_bank' => 'BEJER',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'NACIONAL FINANCIERA',
            'receptor_bank' => 'NAFIN',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO DEL BIENESTAR (BANSEFI)',
            'receptor_bank' => 'BANSE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'HIPOTECARIA FEDERAL',
            'receptor_bank' => 'HIFED',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANAMEX',
            'receptor_bank' => 'BANAM',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BBVA BANCOMER',
            'receptor_bank' => 'BACOM',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO SANTANDER',
            'receptor_bank' => 'BANME',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'HSBC',
            'receptor_bank' => 'BITAL',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BAJIO',
            'receptor_bank' => 'BAJIO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'INBURSA',
            'receptor_bank' => 'BINBU',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'SCOTIA BANK',
            'receptor_bank' => 'COMER',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANREGIO',
            'receptor_bank' => 'BANRE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'INVEX',
            'receptor_bank' => 'BINVE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANSI',
            'receptor_bank' => 'BANSI',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'AFIRME',
            'receptor_bank' => 'BAFIR',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANORTE/IXE',
            'receptor_bank' => 'BBANO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'ACCENDO BANCO',
            'receptor_bank' => 'ABNBA',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'AMERICAN EXPRESS',
            'receptor_bank' => 'AMEX',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANK OF AMERICA',
            'receptor_bank' => 'BAMSA',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'MUFG BANK MEXICO (TOKYO)',
            'receptor_bank' => 'TOKYO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'JP MORGAN',
            'receptor_bank' => 'CHASE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO MONEX',
            'receptor_bank' => 'CMCA',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'VE POR MAS',
            'receptor_bank' => 'DRESD',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'DEUTSCHE',
            'receptor_bank' => 'DEUTB',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CREDIT SUISSE',
            'receptor_bank' => 'CRESU',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'AZTECA',
            'receptor_bank' => 'BAZTE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO AUTOFIN',
            'receptor_bank' => 'BAUTO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BARCLAYS BANK',
            'receptor_bank' => 'BARCL',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO COMPARTAMOS',
            'receptor_bank' => 'BCOMP',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO MULTIVA',
            'receptor_bank' => 'MULTI',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'ACTINVER',
            'receptor_bank' => 'PRUDE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'INTERCAM BANCO',
            'receptor_bank' => 'REGIO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCOPPEL',
            'receptor_bank' => 'COPEL',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'ABC CAPITAL',
            'receptor_bank' => 'AMIGO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CONSUBANCO',
            'receptor_bank' => 'FACIL',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'VOLKSWAGEN BANK',
            'receptor_bank' => 'VOLKS',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CI BANCO',
            'receptor_bank' => 'CONSU',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BBASE',
            'receptor_bank' => 'BBASE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANKAOOL',
            'receptor_bank' => 'AGROF',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'PAGATODO',
            'receptor_bank' => 'PTODO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'INMOBILIARIO',
            'receptor_bank' => 'INMOB',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'DONDE',
            'receptor_bank' => 'DONDE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCREA',
            'receptor_bank' => 'BCREA',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO FINTERRA',
            'receptor_bank' => 'FINTE',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'ICBC',
            'receptor_bank' => 'ICBCH',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'SABADELL',
            'receptor_bank' => 'SABAD',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'SHINAN',
            'receptor_bank' => 'SHINH',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'MIZUHO BANK',
            'receptor_bank' => 'MISUO',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'BANCO S3',
            'receptor_bank' => 'BCOS3',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'MONEX CASA DE BOLSA',
            'receptor_bank' => '90600',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'GBM CASA DE BOLSA',
            'receptor_bank' => '90601',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'MASARI CASA DE BOLSA',
            'receptor_bank' => '90602',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'VALUE CASA DE BOLSA',
            'receptor_bank' => '90605',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'ESTRUCTURADORES',
            'receptor_bank' => '90606',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'VECTOR CASA DE BOLSA',
            'receptor_bank' => '90608',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'MULTIVA CBOLSA',
            'receptor_bank' => '90613',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'FINAMEX',
            'receptor_bank' => '90616',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'VALMEX (VALORES MEXICANOS CASA DE BOLSA)',
            'receptor_bank' => '90617',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'PROFUTURO GNP AFORE',
            'receptor_bank' => '90620',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'SKANDIA VIDA',
            'receptor_bank' => '90623',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'INTERCAM CASA DE BOLSA',
            'receptor_bank' => '90630',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CI BOLSA',
            'receptor_bank' => '90631',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'FINCOMUN, SERVICIOS FINANCIEROS COMUNITARIOS',
            'receptor_bank' => '90634',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'HDI SEGUROS, S.A. DE .CV.',
            'receptor_bank' => '90636',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'ORDER EXPRESS CASA DE CAMBIO SA DE CV',
            'receptor_bank' => '90637',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'AKALA SA DE CV SOCIEDAD FINANCIERA POPULAR',
            'receptor_bank' => '90638',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'REFORMA',
            'receptor_bank' => '90642',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'STP (SISTEMA DE TRANSFERENCIAS Y PAGOS SOFOM)',
            'receptor_bank' => '90646',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'EVERCORE',
            'receptor_bank' => '90648',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'OSKNDIA OPERADORA DE FONDOS SA DE CV',
            'receptor_bank' => '90649',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CREDICAPITAL',
            'receptor_bank' => '90652',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'KUSPIT',
            'receptor_bank' => '90653',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'SOFIEXPRESS',
            'receptor_bank' => '90655',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'UNAGRA',
            'receptor_bank' => '90656',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'ASP INTEGRA OPC',
            'receptor_bank' => '90659',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'LIBERTAD',
            'receptor_bank' => '90670',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CAJA POP MEXICANA',
            'receptor_bank' => '90677',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CRISTOBAL COLON',
            'receptor_bank' => '90680',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'TRANSFER',
            'receptor_bank' => '90684',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'FONDO FIRA',
            'receptor_bank' => '90685',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'INVERCAP',
            'receptor_bank' => '90686',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'FOMPED',
            'receptor_bank' => '90689',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CLS BANK',
            'receptor_bank' => 'CLSB',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'INDEVAL',
            'receptor_bank' => '90902',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('cat_banks_suppliers')->insert([
            'name' => 'CODI VALIDA',
            'receptor_bank' => '90903',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);




    }
}
